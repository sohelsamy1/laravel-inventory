<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function categoryPage(){
        return view('pages.dashboard.category-page');
    }
}
