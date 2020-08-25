
@include('users.layouts.header.header')
	<div class="page-content">
		<!-- Breadcrumb Section -->
		<section class="breadcrumb-contact-us breadcrumb-section section-box" style="background-image: url({{ asset('images/shop-bc.jpg') }})">
			<div class="container">
				<div class="breadcrumb-inner">
					<h1>Cart</h1>
					<ul class="breadcrumbs">
						<li><a class="breadcrumbs-1" href="index.html">Home</a></li>
						<li><p class="breadcrumbs-2">Cart Items</p></li>
					</ul>
				</div>
			</div>
        </section>
		<!-- End Breadcrumb Section -->

        <!-- Shop Cart Section -->
    @if(session('cart'))
		<section class="shop-cart-section section-box">
			<div class="woocommerce">
				<div class="container">
					<div class="entry-content">
                        @if (session('status'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('status') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        @endif
                        @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        @endif
						<div  class="woocommerce-cart-form">
                            @csrf
							<table class="shop_table shop_table_responsive cart woocommerce-cart-form__contents">
								<thead>
									<tr>
										<th class="product-remove"></th>
										<th class="product-name">Product</th>
										<th class="product-price">Price</th>
										<th class="product-quantity">Quantity</th>
										<th class="product-subtotal">Total</th>
									</tr>
								</thead>
								<tbody>
                                <?php $total = 0 ?>
                                    @foreach(session('cart') as $id => $cart_info)

                                    <?php $total += $cart_info['price'] * $cart_info['quantity'] ?>
									<tr class="woocommerce-cart-form__cart-item cart_item">
										<td class="product-remove">
                                            <a href="{{ route('cart.remove',['id'=>$id]) }}" class="remove">
                                               <i class="zmdi zmdi-close"></i>
                                            </a>
										</td>
										<td class="product-name" data-title="Product">
                                            <a href="{{ route('shop.product',['id' =>$id ,'slug' =>str_slug($cart_info['name'])]) }}">
                                                <img src="{{ asset('uploads/products/'.$cart_info['photo']) }}" alt="product">
                                            </a>
                                            <a href="{{ route('shop.product',['id' =>$id ,'slug' =>str_slug($cart_info['name'])]) }}">
                                                {{ $cart_info['name'] }}  x{{ $cart_info['productQuantity'] }}
                                            </a>
										</td>
										<td class="product-price" data-title="Price">
											<span class="woocommerce-Price-amount amount">
												<span class="woocommerce-Price-currencySymbol">$</span>
												{{ $cart_info['price'] }}
											</span>
										</td>
										<td class="product-quantity" data-title="Quantity">
											<div class="quantity form-group">
                                                <form action="{{ route('cart.update',['id'=>$id]) }}" method="POST">
                                                    @csrf
                                                    <select class="custom-select mr-sm-2" onchange="this.form.submit()"
                                                    name="quantity"  style="cursor: pointer" >
                                                        @for ($i = 1; $i <= $cart_info['inStock']; $i++)
                                                            @if ($cart_info['quantity']==$i)
                                                                <option value="{{ $i }}" selected>{{ $i }}</option>
                                                            @else
                                                                <option value="{{ $i }}">{{ $i }}</option>
                                                            @endif
                                                        @endfor
                                                    </select>
                                                </form>
		                                    </div>
										</td>
										<td class="product-subtotal" data-title="Total">
											<span class="woocommerce-Price-amount amount">
												<span class="woocommerce-Price-currencySymbol">$</span>
												{{  $cart_info['price'] * $cart_info['quantity'] }}
											</span>
										</td>
                                    </tr>
                                    @endforeach
									<tr>
										<td colspan="6" class="actions">
											<div class="coupon">
												<input type="text" name="coupon_code" class="input-text" id="coupon_code" value="" placeholder="Coupon code">
												<div>
													<input type="submit" class="button au-btn btn-small" name="apply_coupon" value="Apply Coupon">
													<span><i class="zmdi zmdi-arrow-right"></i></span>
												</div>
											</div>
											{{-- <div class="action-btn">
												<input type="submit" class="button au-btn btn-small" name="update_cart" value="Update Cart">
												<span><i class="zmdi zmdi-arrow-right"></i></span>
											</div> --}}
										</td>
									</tr>
								</tbody>
							</table>
						</div>
						<div class="cart-collaterals">
							<div class="cart_totals">
								<h2>Cart totals</h2>
								<table class="shop_table shop_table_responsive">
									<tbody>
										<tr class="cart-subtotal">
											<th>Subtotal</th>
											<td data-title="Subtotal">
												<span class="woocommerce-Price-amount amount">
													<span class="woocommerce-Price-currencySymbol">$</span>
													{{ $total }}
												</span>
											</td>
										</tr>
										<tr class="shipping">
											<th>Shipping</th>
											<td>
												<span class="shipping-calculator-button">Calculate Shipping</span>
												<section class="shipping-calculator-form">
													<p class=" form-row-wide" id="calc_shipping_country_field">
														<select name="calc_shipping_country" id="calc_shipping_country" class="country_to_state country_select select2-hidden-accessible" rel="calc_shipping_state" tabindex="-1" aria-hidden="true">
															<option value="VN">VietNam</option>
															<option value="JP">Japan</option>
															<option value="ZM">Korea</option>
														</select>
														<span class="select-btn">
															<i class="zmdi zmdi-caret-down"></i>
														</span>
													</p>
													<p class="form-row-wide" id="calc_shipping_state_field">
														<input type="text" class="hidden" name="calc_shipping_state" id="calc_shipping_state" value="" placeholder="Town / City">
													</p>
													<p class="form-row-wide" id="calc_shipping_postcode_field">
														<input type="text" class="input-text" value="" placeholder="Postcode / ZIP" name="calc_shipping_postcode" id="calc_shipping_postcode">
													</p>
													<p>
														<button type="submit" name="calc_shipping" value="1" class="button au-btn btn-small">Update Totals<i class="zmdi zmdi-arrow-right"></i></button>
													</p>
												</section>
											</td>
										</tr>
										<tr class="order-total">
											<th>Total</th>
											<td data-title="Total">
												<span class="woocommerce-Price-amount amount">
													<span class="woocommerce-Price-currencySymbol">$</span>
													{{ $total }}
												</span>
											</td>
										</tr>
									</tbody>
								</table>
								<div class="wc-proceed-to-checkout">
                                    <form action="{{ route('cart.checkOut') }}" class="woocommerce-cart-form" method="GET">
                                        @csrf
                                        <input type="text" value="{{ $total }}" name="total" hidden>
                                        <button class="checkout-button button wc-forward au-btn btn-small " style="cursor: pointer">Proceed to Checkout<i class="zmdi zmdi-arrow-right"></i></a>
                                    </form>
								</div>
							</div>
                        </div>
					</div>
				</div>
			</div>
        </section>
    @else
    <div class="text-center" style="margin: 100px auto">
        <h4>There is not cart items to show </h4>
        <a href="{{ route('shop') }}"> <button class="btn btn-primary">Back To Shopping</button></a>
    </div>

    @endif
		<!-- End Shop Cart Section -->

	</div>
@include('users.layouts.footer.footer')