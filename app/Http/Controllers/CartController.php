<?php

namespace App\Http\Controllers;

use App\BillingOrder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use App\Category;
use App\Comment;
use App\Order;
use App\Order_product;
use App\Product;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();
        $categories = Category::all();
        $products = Product::all();
        $comments = Comment::all();
        return view('users.carts.index',compact('users','categories','products','comments'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,$id)
    {
        $order      = Order::where('user_id',Auth::user()->id)
        ->where('status','pending')
        ->get();
        $product    = Product::find($id);
        $priceAfterDiscount = $product->price - (($product->price * $product->discount)/100);
        $cart = session()->get('cart');
        if(isset($cart[$id])) {
            if($cart[$id]['quantity'] < $cart[$id]['inStock']){
                $cart[$id]['quantity']++;
            }else{
                session()->flash('status', 'Product '.$cart[$id]['name'].' has only '.$cart[$id]['inStock'].' quantity');
                return redirect()->route('shop.cart');
            }
            session()->put('cart', $cart);
            return redirect()->route('shop.cart');
        }
        $cart[$id] = [
            "name" => $product->name,
            "quantity" => $request->quantity,
            "inStock"   => $product->inStock,
            "price" => $priceAfterDiscount,
            "photo" => $product->pictures[0]->picture,
            "productQuantity" =>$product->quantity
        ];
        session()->put('cart', $cart);
        return redirect()->route('shop.cart');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if($id and $request->quantity)
        {
            $cart = session()->get('cart');

            $cart[$id]["quantity"] = $request->quantity;

            session()->put('cart', $cart);

            session()->flash('success', 'Cart updated successfully');
            return redirect()->route('shop.cart');
        }
    }
    public function remove($id)
    {
        $cart = session()->get('cart');
        if(isset($cart[$id])) {
            $orderProduct = Order_product::where('product_id',$id)->first();
            if($orderProduct){
                $orderProduct->delete();
            }
            unset($cart[$id]);

            session()->put('cart', $cart);
        }

        session()->flash('status', 'Product removed successfully');
        return redirect()->back();

    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    public function checkOut(Request $request)
    {

        if(!Auth::user()){
            return redirect()->route('login');
        }else{
            $categories         = Category::active()
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();
            return view('users.carts.check-out',compact('categories'));
        }
    }
    public function placeOrder(Request $request)
    {
        $this->validate($request,[
            'fullName'      => 'required',
            'governorate'   => 'required|max:40',
            'city'          => 'required|max:40',
            'address'       => 'required|max:100',
            'phone'         => 'required|max:20',
            'paymentMethod' => 'required'
        ]);
        $categories         = Category::active()
        ->orderBy('created_at', 'desc')
        ->take(6)
        ->get();
        if(session('cart')){
            /* Add Order */
            $order = Order::create([
                'user_id'           =>  Auth::user()->id,
                'total'             =>  $request->total,
                'shipping'          =>  0,
                'subtotal'          =>  $request->total,
                'ip'                =>  $request->ip(),
                'status'            =>  'withApproval',
                'fullName'          =>  $request->fullName,
                'governorate'       =>  $request->governorate,
                'city'              =>  $request->city,
                'address'           =>  $request->address,
                'phone'             =>  $request->phone,
                'paymentMethod'     =>  $request->paymentMethod
            ]);
            if($request->has('moreInfo')){
                $order->moreInfo = $request->moreInfo;
                $order->save();
            }
            foreach(session('cart') as $id => $cart_info){
                $orderProduct = Order_product::create([
                    'product_id'    => $id,
                    'order_id'      => $order->id,
                    'quantity'      => $cart_info['quantity']
                ]);
            }
            /*Add order */
            $request->session()->forget('cart');//Clear seasion
            return view('users.carts.thanks',compact('order','categories'));
        }else{
            return redirect()->route('cart.tracking');
        }

    }
    public function trackOrder()
    {
        $categories     = Category::active()
        ->orderBy('created_at', 'desc')
        ->take(6)
        ->get();
        $orders         = Order::where('user_id',Auth::user()->id)
        ->orderBy('created_at', 'desc')
        ->get();

        return view('users.carts.tracking',compact('orders','categories'));
    }
}
