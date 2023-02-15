@extends('app.base')

@section('content')
<script>
	window.addEventListener("load", function(event) {
	 if (document.getElementsByClassName('pagination').length > 0) {
	 	let ul = document.getElementsByClassName('pagination')[0];
	 	ul.classList.remove("pagination");
	 	ul.classList.add("store-pagination");
	 }
	 "<?php if (isset($_GET['price']) && !empty($_GET['price'])) { 
		if (!str_contains($_GET['price'], ':')) {
            $price = "0:900";
        } else {
        	$price = $_GET['price'];
        }
	 ?>"
	 document.getElementById('price-min').value = "<?php echo explode(':', $price)[0] ?>";
	 document.getElementById('price-max').value = "<?php echo explode(':', $price)[1] ?>";
	 "<?php } else { ?>"
	 document.getElementById('price-min').value = "0";
	 document.getElementById('price-max').value = "900";
	 "<?php } ?>"
	});
</script>
<div class="section">
	<!-- container -->
	<div class="container">
		<!-- row -->
		<div class="row">
			<!-- ASIDE -->
			<div id="aside" class="col-md-3">
				<!-- aside Widget -->
				<div class="aside">
					<h3 class="aside-title">Format</h3>
					<div class="checkbox-filter">
						<?php
						$formats = Illuminate\Support\Facades\DB::select('select * from format');
						foreach($formats as $format) {
						?>
						<div class="input-checkbox">
							<input type="checkbox" id="cb-format-{{ strtolower($format->name) }}" value="{{ strtolower($format->name) }}"
								<?php
									if (isset($_GET['format']) && !empty($_GET['format'])) { 
										echo in_array(strtolower($format->name), explode(':', strtolower($_GET['format']))) ? 'checked' : '';
									}
								?>
							>
							<label for="cb-format-{{ strtolower($format->name) }}">
								<span></span>
								{{ $format->name }}
							</label>
						</div>
						<?php
						}
						?>
					</div>
				</div>
				<!-- /aside Widget -->
				<!-- aside Widget -->
				<div class="aside">
					<h3 class="aside-title">Genre</h3>
					<div class="checkbox-filter">
						<?php
						$genres = Illuminate\Support\Facades\DB::select('select * from genre');
						foreach($genres as $genre) {
						?>
						<div class="input-checkbox">
							<input type="checkbox" id="cb-genre-{{ strtolower($genre->name) }}" value="{{ strtolower($genre->name) }}"
								<?php
									if (isset($_GET['genre']) && !empty($_GET['genre'])) { 
										echo in_array(strtolower($genre->name), explode(':', strtolower($_GET['genre']))) ? 'checked' : '';
									}
								?>
							>
							<label for="cb-genre-{{ strtolower($genre->name) }}">
								<span></span>
								{{ $genre->name }}
							</label>
						</div>
						<?php
						}
						?>
					</div>
				</div>
				<!-- /aside Widget -->
				<!-- aside Widget -->
				<div class="aside">
					<h3 class="aside-title">Price</h3>
					<div class="price-filter">
						<div class="input-number price-min">
							<input id="price-min" type="number">
							<span class="qty-up">+</span>
							<span class="qty-down">-</span>
						</div>
						<span>-</span>
						<div class="input-number price-max">
							<input id="price-max" type="number">
							<span class="qty-up">+</span>
							<span class="qty-down">-</span>
						</div>
					</div>
				</div>
				<!-- /aside Widget -->
				<!-- aside Widget -->
				<div class="aside">
					<a id="btFilters" class="primary-btn cta-btn filter-button">Apply filters</a>
					<script>
					document.getElementById('btFilters').addEventListener("click", function(event) {
						let filterFormat = '';
						for (format of <?php echo json_encode($formats); ?>) {
							let cb = document.getElementById('cb-format-'+format.name.toLowerCase());
							if (cb.checked) {
								filterFormat += `${format.name}:`;
							}
						}
						filterFormat = filterFormat.replace(/.$/,"");
						
						let filterGenre = '';
						for (genre of <?php echo json_encode($genres); ?>) {
							let cb = document.getElementById('cb-genre-'+genre.name.toLowerCase());
							if (cb.checked) {
								filterGenre += `${genre.name}:`;
							}
						}
						filterGenre = filterGenre.replace(/.$/,"");
						
						let priceMin = document.getElementById('price-min').value;
						let priceMax = document.getElementById('price-max').value;
						let filterPrice = `${priceMin}:${priceMax}`;
						
						let url = window.location.href;
						let orderby = '';
						let ordertype = '';
						let q = '';
						
						<?php if (isset($_GET['orderby']) && !empty($_GET['orderby'])) { ?>
							orderby = '<?= $_GET['orderby'] ?>';
						<?php } ?>
						<?php if (isset($_GET['ordertype']) && !empty($_GET['ordertype'])) { ?>
							ordertype = '<?= $_GET['ordertype'] ?>';
						<?php } ?>
						<?php if (isset($_GET['q']) && !empty($_GET['q'])) { ?>
							q = '<?= $_GET['q'] ?>';
						<?php } ?>
						
						url = url.substring(0, url.indexOf("movie") + 5);
						window.location.href = `${url}?orderby=${orderby}&ordertype=${ordertype}
												&q=${q}&format=${filterFormat}&genre=${filterGenre}
												&price=${filterPrice}`;
					});
					</script>
				</div>
				<!-- /aside Widget -->
			</div>
			<!-- /ASIDE -->

			<!-- STORE -->
			<div id="store" class="col-md-9">
				<!-- store top filter -->
				<div class="store-filter clearfix">
					<div class="store-sort">
						Sort By:
						<a href="{{ $order['movie.name']['asc'] }}">Name: A to Z</a>
						<a href="{{ $order['movie.name']['desc'] }}">Name: Z to A</a>
						<a href="{{ $order['movie.price']['asc'] }}">Price: Low to High</a>
						<a href="{{ $order['movie.price']['desc'] }}">Price: High to Low</a>
					</div>
				</div>
				<!-- /store top filter -->

				<!-- store products -->
				<div class="row">
					@if(count($movies) == 0)
					<h3>No movies, sorry...</h3>
					@else
						@foreach($movies as $movie)
						<!-- product -->
						<div class="col-md-4 col-xs-6">
							<div class="product">
								<div class="product-img">
									<img src="{{ asset('storage/images/' . $movie->mainimage) }}" alt="">
								</div>
								<div class="product-body">
									<p class="product-category">{{ $movie->gname }}</p>
									<h3 class="product-name"><a href="{{ url('movie/' . $movie->id) }}">{{ $movie->name }}</a></h3>
									<h4 class="product-price">${{ $movie->price }}</h4>
									<div class="product-rating">
										<?php
										$ratingStars = rand(0, 5); 
										for ($i = 0; $i < $ratingStars; $i++) {
										?>
										<i class="fa fa-star"></i>
										<?php }
										for ($i = 0; $i < 5-$ratingStars; $i++) {
										?>
										<i class="fa fa-star-o"></i>
										<?php } ?>
									</div>
									<div class="product-btns">
										<button class="add-to-wishlist"><i class="fa fa-heart-o"></i><span class="tooltipp">add to wishlist</span></button>
										<button class="add-to-compare"><i class="fa fa-exchange"></i><span class="tooltipp">add to compare</span></button>
										<button class="quick-view"><i class="fa fa-eye"></i><span class="tooltipp">quick view</span></button>
									</div>
								</div>
								<div class="add-to-cart">
									<button class="add-to-cart-btn"><i class="fa fa-shopping-cart"></i> add to cart</button>
								</div>
							</div>
						</div>
						<!-- /product -->
						@endforeach
					@endif
				</div>
				<!-- /store products -->

				<!-- store bottom filter -->
				<div class="store-filter clearfix paginator">
					{{ $movies->links('pagination::bootstrap-4') }}
				</div>
				<!-- /store bottom filter -->
			</div>
			<!-- /STORE -->
		</div>
		<!-- /row -->
	</div>
	<!-- /container -->
</div>
@endsection
