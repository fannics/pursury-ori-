<div id="product-modal">
    <div class="product-modal product-file">
        <button class="close-btn"><i class="fa fa-times fa-lg"></i></button>
        <div class="row">
            <div class="col-xs-12 col-md-6 text-center">
                <div class="product-image-wrapper hidden-xs hidden-sm">
                    <img class="modalMainImg" alt="Loading...">
                </div>
            </div>
            <div class="col-xs-12 col-md-6">
                <div class="row">
                    <div class="col-sm-12 text-center">
                        <a class="ptitle" data-product_href><h1>%product_title%</h1></a>
                        @if(\Auth::user() && \Auth::user()->role == 'ROLE_ADMIN')
                            <a href="/admin/products/edit/%product_id%" class="btn btn-primary iepb"><i class="fa fa-edit"></i> {{ trans('main.product_modal.product_modal_update_product') }}</a>
                        @endif
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12 text-center">
                        <span class="product-previous-price">%product_previous_price%</span>
                        <span class="product-price">%product_price%</span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <div class="product-image-wrapper visible-xs-block visible-sm-block">
                            <img class="modalMainImg" alt="Loading..." />
                        </div>
                    </div>
                </div>
                
                <!-- If product is NOT parent -->
                <div class="row product-file-buttons" id="buttonsContent">
                    <div class="col-sm-8 col-sm-offset-2">
                        <form action="{{ route('product_shop') }}" method="post" target="_blank">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input type="hidden" name="product_id" value="%product_id%" />
                            <button class="btn-ultra btn-shop prim-btn">{{ trans('main.product_modal.purchase_here') }}</button>
                        </form>
                        <hr>
                        <button data-url="{{ route('wishlist_item') }}" data-pi="%product_id%" class="btn-ultra btn-wishlist .def-btn wishlist on-wishlist">
                            <i class="fa fa-heart-o fa-lg"></i>
                            <i class="fa fa-heart fa-lg"></i>
                            <span class="not-added">{{ trans('main.product_modal.add_to_wishlist') }}</span>
                            <span class="added">{{ trans('main.product_modal.added_to_wishlist') }}</span>
                        </button>
                    </div>
                </div>
                
                <!-- If product IS parent -->
                <!--
                <div class="row product-file-buttons" id="childContent">
                  <div id="childrenLoop">
                    {{ trans('category.index.loading') }}
                  </div>
                  <hr>
                  <button data-url="{{ route('wishlist_item') }}" data-pi="%product_id%" class="btn-ultra btn-wishlist .def-btn wishlist on-wishlist">
                    <i class="fa fa-heart-o fa-lg"></i>
                    <i class="fa fa-heart fa-lg"></i>
                    <span class="not-added">{{ trans('main.product_modal.add_to_wishlist') }}</span>
                    <span class="added">{{ trans('main.product_modal.added_to_wishlist') }}</span>
                  </button>
                </div>
                -->
                
                <div class="row">
                    <div class="col-sm-12 text-center">
                        <h2 class="share-text">{{ trans('main.product_modal.share') }}</h2>
                    </div>
                    <div class="col-sm-12 text-center social-share-list-compact">
                        <button class="sns" data-sns="facebook" data-sns-title="{{ settings('app.app_title').' - '}} %product_title%" data-sns-url="%product_href%"><i class="fa fa-lg fa-facebook"></i></button>
                        <button class="sns" data-sns="twitter" data-sns-title="{{ settings('app.app_title').' - '}} %product_title%" data-sns-url="%product_href%"><i class="fa fa-lg fa-twitter"></i></button>
                        <button class="sns" data-sns="google+" data-sns-title="{{ settings('app.app_title').' - '}} %product_title%" data-sns-url="%product_href%"><i class="fa fa-lg fa-google-plus"></i></button>
                    </div>
                </div>
            </div>
        </div>
        <div class="row similar-products" style="display: none">
            <div class="col-xs-12 text-center">
                <h4>
                    {{ trans('main.product_modal.similar_products_header') }}
                </h4>
            </div>
            <div class="col-xs-12 similar products-wrapper">
                <div id="similar-product-template" style="display: none;">
                    <div class="product-tile-wrapper">
                        <div class="product-tile" >
                            <div class="product-discounts">
                                <a class="coupon" data-sim_product_coupon_href>{{ trans('main.product_modal.product_tile_coupon') }}</a>
                                <span class="discount">%sim_product_discount%</span>
                            </div>
                            <div class="product-image">
                                <div class="image-async-loader" data-src="%sim_product_image_url%" data-alt="%sim_product_image_alt%"></div>
                                <div class="overlay">
                                    <form action="{{ route('product_shop') }}" method="post" target="_blank">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="hidden" name="product_id" value="%sim_product_id%" />
                                        <button class="second prim-btn" rel="nofollow" target="_blank" class="product-file-link"><span>{{ trans('main.product_modal.product_tile_follow') }}</span>  <i class="fa fa-chevron-right"></i></button>
                                    </form>
                                </div>
                            </div>
                            <div class="product-title">
                                <a class="product-title-link" title="%sim_product_title%" data-sim_product_href>
                                    %sim_product_title%
                                </a>
                            </div>
                            <div class="product-price">
                                <span class="old-price">%sim_product_previous_price%</span>
                                <span>%sim_product_price%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>