@extends('layouts.app', ['title' => __('product')])

@section('content')
    @include('admin.users.partials.header', ['title' => __('Products')])

    <div class="container-fluid mt--7">
        <div class="row">
            <div class="col-xl-12 order-xl-1">
                <div class="card bg-secondary shadow">
                    <div class="card-header bg-white border-0">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h3 class="mb-0">{{ __('product ') . $product->name}}</h3>
                            </div>
                            <div class="col-4 text-right">
                                <a href="{{ route('products') }}" class="btn btn-sm btn-primary">{{ __('Back to products') }}</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="card mb-3 product-show">
                            <div class="row product-show-img">
                                @foreach ($product_pictures as $key => $product_picture)
                                        @if ($key==0)
                                            <div class="col-12">
                                                <img class="card-img-top" src="{{asset('uploads/products/'.$product_picture->picture)}}"
                                                alt="Card image cap" width="100%" height="500px">
                                                <hr>
                                            </div>
                                        @else
                                            <div class="col-4">
                                                <img src="{{asset('uploads/products/'.$product_picture->picture)}}"
                                                alt="Card image cap" width="100%">
                                            </div>
                                        @endif
                                @endforeach
                            </div>
                            <div class="card-body">
                                <h1 class="card-title">{{$product->name}}</h1>
                                <h3 class="red">Price : <span>{{$product->price}}$</span> </h3>
                                <strong class="red">Quantity : <span>{{$product->quantity}} pieces</span> </strong>
                                <h3 class="red">Comments :
                                    @if ($comment->count())
                                        <a href="{{ route('comments.product',['id' => $product->id]) }}">{{ $comment->count() }}</a>
                                    @else
                                        No Comment Yet
                                    @endif
                                </h3>
                                <hr>
                                <p class="card-text">{{$product->description}}</p>
                                <hr>
                                <strong>Category : <a href="{{route('categories.show',$product->category_id)}}">{{$product->category->name}}</a> </strong>
                                @if ($product->weight==0||$product->weight==NULL)
                                    <h3>Weight : <span>unknown</span></h3>
                                @else
                                    <h3>Weight : <span>{{$product->weight}}KG</span></h3>
                                @endif
                                @if ($product->discount==0||$product->discount==NULL)
                                    <h3>Discount : <span>No discount yet.</span></h3>
                                @else
                                    <h3>Discount : <span>{{$product->discount}}%</span></h3>
                                @endif
                                @if ($product->discount==0||$product->discount==NULL)
                                    <h3>In Stock : <span>unknown</span></h3>
                                @else
                                    <h3>In Stock : <span>{{$product->inStock}}</span></h3>
                                @endif
                                <p class="card-text"><small class="text-muted"> <strong>Updated At :</strong> {{ $product->updated_at->format('d/m/Y H:i') }}</small></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @include('layouts.footers.auth')
    </div>
@endsection
