<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;
use PHPUnit\Runner\Exception;
use App\Models\InvoiceProduct;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{

   public function InvoiceCreate(Request $request){

    DB::beginTransaction();

    try {
        $user_id = $request->header('user_id');

        $total = $request->input('total');
        $discount = $request->input('discount');
        $vat = $request->input('vat');
        $payable = $request->input('payable');
        $customer_id = $request->input('customer_id');

      $invoice = Invoice::create([
            'total' =>$total,
            'discount' =>$discount,
            'vat' =>$vat,
            'payable' =>$payable,
            'user_id' =>$user_id,
            'customer_id' =>$customer_id,
      ]);

       $invoiceID = $invoice->id;
       $products = $request->input('products');

       foreach ($products as $eachProduct){
        InvoiceProduct::create([
            'invoice_id' => $invoiceID,
            'user_id' => $user_id,
            'product_id' => $eachProduct['product_id'],
            'qty' => $eachProduct['qty'],
            'sale_price' => $eachProduct['sale_price'],
            // 'sale_price' =>$eachProduct['qtsale_price'],
        ]);
       }
       DB::commit();

       return 1;

        }catch (Exception $e){
            DB::rollBack();
            return $e->getMessage();
        }

   }

}
