<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Product;
use App\Models\Category;
use App\Models\Customer;
use Illuminate\Http\Request;

class DashboardController extends Controller
{

  public function summary(Request $request){
    $user_id = $request->header('user_id');

    $product = Product::where('user_id', $user_id)->count();
    $category = Category::where('user_id', $user_id)->count();
    $customer = Customer::where('user_id', $user_id)->count();
    $invoice = Invoice::where('user_id', $user_id)->count();
    $total = Invoice::where('user_id', $user_id)->count('total');
    $vat = Invoice::where('user_id', $user_id)->count('vat');
    $payable = Invoice::where('user_id', $user_id)->count('payable');

    return response()->json([
        'status' => 'success',
        'data'  =>[

        'product' => $product,
        'category' => $category,
        'customer' => $customer,
        'invoice' => $invoice,
        'total' => $total,
        'vat' => $vat,
        'payable' => $payable

        ]
    ], 200);
  }
}
