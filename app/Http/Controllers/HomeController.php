<?php

namespace App\Http\Controllers;

use App\Category;
use App\Comment;
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
        $first_Slider       = Category::active()
        ->orderBy('created_at','desc')
        ->first();
        $second_Slider      = Category::active()
        ->orderBy('created_at','desc')
        ->skip(1)
        ->take(1)
        ->get()
        ->first();
        $third_Slider       = Category::active()
        ->orderBy('created_at','desc')
        ->skip(2)
        ->take(1)
        ->get()
        ->first();
        $users              = User::all();
        $categories         = Category::active()
        ->orderBy('created_at', 'desc')
        ->take(6)
        ->get();
        $product_picture    = product_picture::all();
        $products           = Product::active()
        //Select from db depend on Relation
        ->whereHas('category', function ($query) {
            return $query->where('active', 1);
        })
        ->orderBy('created_at', 'desc')
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
        $query = Product::active()
        ->whereHas('category', function ($query) {
            return $query->where('active', 1);
        });
        // search filter

        //Start Filter Section
        /*
        -Select
        -Where
        -OrderBy
        */
        // -----------Search And Sort--------------
        if (isset($Request->order) && $Request->order == 'desc') {
            $query->selectRaw(" * , price - ((price * discount)/100) as new_price ");
        }elseif(isset($Request->order) && $Request->order == 'asc'){
            $query->selectRaw(" * , price - ((price * discount)/100) as new_price ");
        }

        if (isset($Request->search)){
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
        //Select from db depend on Relation

        // -----------Search And Sort--------------
        //End Filter Section
        $all_product        = Product::active() //Prodcut Count
        ->whereHas('category', function ($query) {
            return $query->where('active', 1);
        })
        ->get();
        //dd($products->toSql());

        $product_picture    = product_picture::all();
        $categories         = Category::active()
        ->get();
        return view('users.categories.show',compact('categories','products','product_picture','all_product',
        ));
    }
    public function SpecificCateg(Request $Request,$id, $slug)
    {
        $category       = Category::find($id);//If no matching model exist, it returns null.
        if($category==NULL){
            return view('users.notfound');
        }
        $category_slug  = str_slug($category->name);
        //Valide URL
        if (!$category->active||($slug!=$category_slug)) {
            return view('users.notfound');
        }
        //Valide URL
        $query = Product::active($id);
        // -----------Search And Sort--------------
        if (isset($Request->order) && $Request->order == 'desc') {
            $query->selectRaw(" * , price - ((price * discount)/100) as new_price ");
        }elseif(isset($Request->order) && $Request->order == 'asc'){
            $query->selectRaw(" * , price - ((price * discount)/100) as new_price ");
        }

        if (isset($Request->search)){
            $request_search = request('search');
            $query->where('name','like','%'. $request_search .'%');
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
        // -----------Search And Sort--------------
        $all_product        = Product::active($id);
        $product_picture    = product_picture::all();
        $categories         = Category::active()
        ->get();
        $category_info      = Category::where('id',$id)
        ->get()
        ->first();
        return view('users.categories.show',compact('categories','products','product_picture','all_product',
        'category_info'));
    }
    public function product($id,$slug)
    {
        $categories         = Category::active()
        ->get();
        $product            = Product::find($id);
        if($product==NULL){
            return view('users.notfound');
        }
        //Valide URL
        if (!$product->active||($slug!=$product->slug)) {
            return view('users.notfound');
        }
        $product_pictures = product_picture::where('product_id', $id)
        ->orderBy('id', 'desc')
        ->get();
        $products       = Product::active()
        ->where('category_id',$product->category->id)
        ->where('id','!=',$id)
        ->orderBy('created_at', 'desc')
        ->take(8)
        ->get();
        $comments       = Comment::where('product_id',$id)
        ->orderBy('created_at', 'desc')
        ->get();
        $avr_star       = Comment::where('product_id',$id)->selectRaw('SUM(rate)/COUNT(user_id) AS avg_rating')
        ->first()
        ->avg_rating;
        $product_star = round($avr_star);
        return view('users.product.show',compact('categories','product','product_pictures','products',
        'comments','avr_star','product_star'));
    }
}
