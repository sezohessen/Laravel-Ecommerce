<?php

namespace App\Http\Controllers;

use App\Category;
use App\Product;
use App\product_picture;
use App\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{

	public function __construct() {
		$this->middleware('auth');
	}

	public function index() {
        $first_Slider       = Category::orderBy('created_at','desc')->first();
        $second_Slider      = Category::orderBy('created_at','desc')->skip(1)->take(1)->get()->first();
        $third_Slider       = Category::orderBy('created_at','desc')->skip(2)->take(1)->get()->first();
        $users              = User::all();
        $categories         = Category::orderBy('created_at', 'desc')
        ->take(6)
        ->get();
        $product_picture    = product_picture::all();
        $products           = Product::orderBy('created_at', 'desc')
        ->where('active',1)
        ->take(8)
        ->get();
        return view('users.main',compact('users','categories','products','first_Slider','second_Slider'
    ,'third_Slider'));
    }
    public function shop(Request $Request)
    {
        //dd($Request);
        //Can not passing  $Request->order in orderBy
        //Like orderBy('new_price',$Request->order)  --->> vulnerable SQL Injection
        // get active rows
        $query = Product::active();
        // search filter

        //Start Filter Section
        if (isset($Request->order) && $Request->order == 'desc') {
            $query->selectRaw(" * , price - ((price * discount)/100) as new_price ");
        }elseif(isset($Request->order) && $Request->order == 'asc'){
            $query->selectRaw(" * , price - ((price * discount)/100) as new_price ");
        }

        if (isset($Request->search)) {
            $query->where('name','like','%'.request('search').'%');
        }

        if (isset($Request->order) && $Request->order == 'desc') {
            $query->orderBy('new_price','desc');
        }elseif(isset($Request->order) && $Request->order == 'asc'){
            $query->orderBy('new_price','asc');
        }else{
            $query->orderBy('created_at', 'desc');
        }
        $products = $query->paginate(9);
        $products->appends(['order' => $Request->order, 'search' => $Request->search ]);
        //End Filter Section
        $all_product        = Product::where('active',1) //Prodcut Count
        ->get();
        //dd($products->toSql());

        $product_picture    = product_picture::all();
        $categories         = Category::all();
        return view('users.categories.show',compact('categories','products','product_picture','all_product',
        ));
    }
    public function SpecificCateg(Request $Request,$id, $slug)
    {
        if (isset($Request->order) && $Request->order == 'desc') {
            $products           = Product::selectRaw(" * , price - ((price * discount)/100) as new_price ")
            ->where('active',1)
            ->where('category_id',$id)
            ->orderBy('new_price','desc')
            ->paginate(9)
            ->appends(['order' => $Request->order ]);
        }elseif(isset($Request->order) && $Request->order == 'asc'){
            $products           = Product::selectRaw(" * , price - ((price * discount)/100) as new_price ")
            ->where('active',1)
            ->where('category_id',$id)
            ->orderBy('new_price','asc')
            ->paginate(9)
            ->appends(['order' => $Request->order ]);
        }else{
            $products           = Product::where('active',1)
            ->where('category_id',$id)
            ->orderBy('created_at', 'desc')
            ->paginate(9);
        }
        $all_product        = Product::where('active',1)
        ->where('category_id',$id)
        ->get();
        $product_picture    = product_picture::all();
        $categories         = Category::all();
        $category_info      = Category::where('id',$id)
        ->get()
        ->first();
        return view('users.categories.show',compact('categories','products','product_picture','all_product',
        'category_info'));
    }
}
