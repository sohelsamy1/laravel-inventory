<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{

      public function categoryPage(){
        return view('pages.dashboard.category-page');
    }

    public function categoryList(Request $request){

     $user_id = $request->header('user_id');
     return Category::where('user_id',$user_id)->get();
    }

   public function CreateCategory(Request $request){
    $user_id = $request->header('user_id');
    return Category::create([
        'name' => $request->input('name'),
        'user_id' => $user_id
    ]);
   }

   public function CategoryDelete(Request $request){
     $user_id = $request->header('user_id');
     $cat_id = $request->input('id');
     return Category::where('id',$cat_id)->where('user_id', $user_id)->delete();

   }

   public function CategoryByID(Request $request){
    $user_id = $request->header('user_id');
    $cat_id = $request->input('id');
    return Category::where('id', $cat_id)->where('user_id',$user_id)->first();
   }

public function CategoryUpdate(Request $request)
{
    $user_id = $request->header('user_id');
    $cat_id = $request->input('id');
    return Category::where('id',$cat_id)->where('user_id', $user_id)->update([
        'name'=>$request->input('name')
    ]);
}


}
