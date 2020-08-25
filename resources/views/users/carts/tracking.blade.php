@include('users.layouts.header.header')
	<div class="page-content">
		<!-- Coming Soon Section -->
		<section class="coming-soon-page" style="background-image: url({{ asset('images/coming-soon-bg.jpg') }})">
			<div class="page-detail">
				<div class="page-inner">
					<h1>Tracking</h1>
					<div id="clock"></div>
					<div class="container">
                        <div class="row">
                            @foreach ($orders as $order)
                                <div class="col-md-12 hh-grayBox pt45 pb20" style="margin: 50px 0px">
                                    <h4>ID tracking :{{ $order->id }}</h4>
                                    <div class="row justify-content-between">
                                        <div class="order-tracking <?php
                                                if($order->status == 'withApproval'||$order->status == 'Shipped' || $order->status == 'Delivered'){
                                                    echo 'completed';
                                                }
                                                ?>">
                                            <span class="is-complete"></span>
                                            <p>Ordered<br>
                                            <p>{{ $order->created_at->format('H:i ') }}</p>
                                            <span>{{ $order->created_at->format('l, F j ') }}</span>
                                            </p>
                                        </div>
                                        <div class="order-tracking <?php
                                            if($order->status == 'Shipped' || $order->status == 'Delivered'){
                                                echo 'completed';
                                            }
                                            ?>">
                                            <span class="is-complete"></span>
                                            <p>Shipped<br><span>Tue, June 25</span></p>
                                        </div>
                                        <div class="order-tracking <?php
                                            if($order->status == 'Delivered'){
                                                echo 'completed';
                                            }
                                            ?>">
                                            <span class="is-complete"></span>
                                            <p>Delivered<br><span>Fri, June 28</span></p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                      </div>
				</div>
			</div>
		</section>
		<!-- End Coming Soon Section -->
	</div>
@include('users.layouts.footer.footer')
