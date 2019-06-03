@extends('master')

@section('metadata')

    @if (!$product->meta_index)
    <meta NAME="robots" CONTENT="index, follow">
    @else
    <meta NAME="robots" CONTENT="noindex, nofollow">
    @endif
    
    <meta name="description" content="{{ $product->meta_description }}">
    <meta itemprop="image" content="{{ resized_image($product->image) }}"/>
    <meta property="og:title" content="{{ settings('app.app_title').' - '.$product->title }}" />                               
    <meta property="og:type" content="website" />
    <meta property="og:image" content="{{ resized_image($product->image) }}" />
    <meta property="og:description" content="{{ $product->short_description }}" />
    <meta property="og:site_name" content="{{ settings('app.app_title') }}" />
    
    @foreach($hrefLangProducts as $hrefLangProduct)
    <link rel="alternate" hreflang="{{ $hrefLangProduct->language }}-{{ $hrefLangProduct->country }}" href="{{ url('') }}{{ settings('app.route_prefix') }}/{{ $hrefLangProduct->country }}/{{ $hrefLangProduct->language_url }}{{ $hrefLangProduct->url_key }}" />
    @endforeach
    <link rel="canonical" href="{{ url('/') }}/{{ prefixed_route($product->url_key) }}" />

@endsection

@section('title', settings('app.app_title').' - '.$product->title)

@section('main_content')
    <div class="row product-file" itemscope itemtype="https://schema.org/Product">
        <div class="col-sm-10 col-sm-offset-1">
            <div class="row">
                <div class="col-xs-12 col-md-7 text-center">
                  <div class="product-image-wrapper">
                    <img itemprop="image" class="hidden-xs hidden-sm" src="{{ resized_image($product->image, 'file') }}" alt="{{ $product->image_alt }}">
                  </div>
                </div>
                <div class="col-xs-12 col-md-5">
                    <div class="row">
                        <div class="col-sm-12 text-center category-tree">
                            @foreach($product->categoryTree() as $tree)
                            <ol class="breadcrumb" vocab="https://schema.org/" typeof="BreadcrumbList">
                                <li property="itemListElement" typeof="ListItem">
                                    <a property="item" typeof="WebPage" href="{{ route('homepage') }}" title="Inicio">
                                        <span property="name" >Inicio</span>
                                        <meta name="position" content="1" />
                                    </a>
                                </li>
                                @foreach($tree as $key=>$subtree)
                                    <li property="itemListElement" typeof="ListItem">
                                        <a property="item" typeof="WebPage" href="{{ prefixed_route($subtree['link']) }}" title="{{ $subtree['title'] }}">
                                            <span property="name">{{ $subtree['title'] }}</span>
                                            <meta name="position" content="{{ $key + 2 }}" />
                                        </a>
                                    </li>
                                @endforeach
                            </ol>
                            @endforeach
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 text-center">
                            <h1 itemprop="name">{{ $product->title }}</h1>
                            @if(\Auth::user() && \Auth::user()->role == 'ROLE_ADMIN')
                                <a href="{{ route('admin_products_edit', ['id' => $product->id]) }}" class="btn btn-primary iepb"><i class="fa fa-edit"></i> {{ trans('main.product.product_modal_update_product') }}</a>
                            @endif
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 text-center" itemprop="offers" itemscope itemtype="https://schema.org/Offer">
                            <meta itemprop="priceCurrency" content="EUR" />
                            <link itemprop="availability" href="https://schema.org/InStock"/>
                            @if ($product->previous_price and $product->previous_price != $product->price)
                                <span class="product-previous-price">{{ number_format($product->previous_price, 2) . '&euro;' }}</span>
                            @endif
                            <span itemprop="price" class="product-price">{!! print_price($product->price) !!}</span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="product-image-wrapper">
                                <img class="visible-xs-block visible-sm-block" src="{{ resized_image($product->image, 'file') }}" alt="{{ $product->image_alt }}" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div itemprop="description" class="product-description">
                                {!! $product->description !!}
                            </div>
                        </div>
                    </div>

@if ($product->is_parent)
                    
                    <hr>
                    <button id="childrenDestopAddWishlist" data-url="{{ route('wishlist_item') }}" data-pi="{{ $product->id }}" class="btn-ultra btn-wishlist .def-btn wishlist {{ $product->onUserWishlist(\Auth::user() ? \Auth::user()->id : null) ? 'on-wishlist' : '' }}">
                      <i class="fa fa-heart-o fa-lg"></i>
                      <i class="fa fa-heart fa-lg"></i>
                      <span class="not-added">{{ trans('main.product.add_to_wishlist') }}</span>
                      <span class="added">{{ trans('main.product.added_to_wishlist') }}</span>
                    </button>

                    <div id="childrenMobile" class="minWidth80" align="center">

                      <form action="{{ route('product_shop') }}" method="post" target="_blank">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" id="id_mobileItemSelector" name="product_id" />
                        
                        <div class="maxWidth100 optionsSelect" id="optionsSelect">
                          <div class="floatLeft paddingTop3">
                            &nbsp;&nbsp;&nbsp;<b><span id="availableOptionsLabel">{{ count($children_products) }}  available options<span></b> 
                          </div>
                          <div class="floatRight">
                            <img src="/images/arrow-right.gif" width="17" height="27">
                          </div>
                          <br clear="all"/>         
                        </div>

                        <div id="seeAvailableOptions">
                          <div class="header">
                            <div class="floatLeft fontSize12">
                              AVAILABLE OPTIONS
                            </div>
                            <div class="floatRight fontSize12">
                              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            </div>
                            <div class="floatRight fontSize12">
                              PRICE:
                            </div>
                            <br clear="all"/>
                          </div>
                          <div class="options">
                            @foreach ($children_products as $children_product)
                              <div class="floatLeft fontSize12">
                                <?php $counter = 0; ?>
                                @foreach ($parent_filters as $parent_filter)
                                  <span id="item_{{ $children_product->id }}">
                                    {{ $parent_filter }}: {{ ucfirst(getTagForProduct($children_product->id, $parent_filter)) }}@if ($counter < count($parent_filters)-1 ),@endif
                                  <span>
                                  <?php $counter = $counter + 1; ?> 
                                @endforeach
                              </div>
                              <div class="floatRight fontSize12">
                                <input type="radio" id="id_selectedItem" name="selectedItem" value="{{ $children_product->id }}">
                              </div>
                              <div class="floatRight fontSize12">
                                <span id="price_{{ $children_product->id }}" class="mobilePriceText">{{ print_price($children_product->price) }}<span>&nbsp;&nbsp;&nbsp;
                              </div>
                              <br clear="all"/><br>
                            @endforeach                          
                          </div>
                        </div>
                      
                      
                        <div id="mobileChildrePurchaseButton" class="maxWidth100" align="center">
                          <div id="id_selectedPrice" class="floatRight fontSize21 mobilePriceText"></div>     
                          <br clear="all"/><br> 
                          <button class="btn-ultra2 btn-shop prim-btn fontSize18">{{ trans('main.product.purchase_here') }}</button>
                        </div>
                      
                      </form>                 

                      <hr>
                      <button data-url="{{ route('wishlist_item') }}" data-pi="{{ $product->id }}" class="btn-ultra btn-wishlist .def-btn wishlist {{ $product->onUserWishlist(\Auth::user() ? \Auth::user()->id : null) ? 'on-wishlist' : '' }}">
                        <i class="fa fa-heart-o fa-lg"></i>
                        <i class="fa fa-heart fa-lg"></i>
                        <span class="not-added">{{ trans('main.product.add_to_wishlist') }}</span>
                        <span class="added">{{ trans('main.product.added_to_wishlist') }}</span>
                      </button>

                    </div>
                </div>
            </div>
            <div id="childrenDesktop" align="center">
                <div  align="center">
                    <div align="center">
                      <div class="row">
                        <table class="fontSize15 minWidth80">
                            @if(empty($blocks))
                          <tr class="border_bottom">
                            <td class="childrenTH" nowrap>&nbsp;</td>
                            <td class="childrenTH" nowrap>&nbsp;</td>
                            @foreach ($parent_filters as $parent_filter)
                            <td class="childrenTH" nowrap>{{ $parent_filter }}</td>
                            @endforeach
                            <td class="childrenTH" nowrap>Price</td>
                            <td class="childrenTH" nowrap>In stock</td>
                            <td class="childrenTH" nowrap>&nbsp;</td>
                          </tr>
                          @foreach ($children_products as $children_product)
                          <tr>                                                                 
                            <td class="childrenTD" nowrap>
                              <img src="{{ resized_image($children_product->thumbnail, 'thumbnail', '50x50') }}" alt="{{ $children_product->image_alt }}">
                            </td nowrap>
                            <td class="childrenTD" nowrap><b>{{ $children_product->title }}</b></td>
                            @foreach ($parent_filters as $parent_filter)
                            <td class="childrenTD" nowrap>{{ ucfirst(getTagForProduct($children_product->id, $parent_filter)) }}</td>
                            @endforeach
                            <td nowrap>
                              @if ($children_product->previous_price)
                                <span class="old-price">{!! print_price($children_product->previous_price) !!}</span>
                              @endif                                        
                              {{ print_price($children_product->price) }}
                            </td>
                            <td class="childrenTD" nowrap>
                              {{ $children_product->stock ? 'Yes':'No' }}
                            </td>
                            <td class="childrenTD" nowrap>
                              <form action="{{ route('product_shop') }}" method="post" target="_blank">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="hidden" name="product_id" value="{{ $children_product->id }}" />
                                <button class="btn-ultra2 btn-shop prim-btn fontSize18">{{ trans('main.product.purchase_here') }}</button>
                              </form>
                            </td>
                          </tr>
                          @endforeach


                            @else
                                   @foreach($blocks as $key => $block)

                                    <tr class="border_bottom">
                                        <td class="childrenTH" nowrap>&nbsp; <h3><strong>{{ucfirst($key)}}</strong></h3></td>
                                        <td class="childrenTH" nowrap>&nbsp;</td>
                                        @foreach ($parent_filters as $parent_filter)
                                            <td class="childrenTH" nowrap>{{ $parent_filter }}</td>
                                        @endforeach
                                        <td class="childrenTH" nowrap>Price</td>
                                        <td class="childrenTH" nowrap>In stock</td>
                                        <td class="childrenTH" nowrap>&nbsp;</td>
                                    </tr>
                                    @foreach ($block as $children_product)
                                        <tr>
                                            <td class="childrenTD" nowrap>
                                                <img src="{{ resized_image($children_product->thumbnail, 'thumbnail', '50x50') }}" alt="{{ $children_product->image_alt }}">
                                            </td nowrap>
                                            <td class="childrenTD" nowrap><b>{{ $children_product->title }}</b></td>
                                            @foreach ($parent_filters as $parent_filter)
                                                <td class="childrenTD" nowrap>{{ ucfirst(getTagForProduct($children_product->id, $parent_filter)) }}</td>
                                            @endforeach
                                            <td nowrap>
                                                @if ($children_product->previous_price)
                                                    <span class="old-price">{!! print_price($children_product->previous_price) !!}</span>
                                                @endif
                                                {{ print_price($children_product->price) }}
                                            </td>
                                            <td class="childrenTD" nowrap>
                                                {{ $children_product->stock ? 'Yes':'No' }}
                                            </td>
                                            <td class="childrenTD" nowrap>
                                                <form action="{{ route('product_shop') }}" method="post" target="_blank">
                                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                    <input type="hidden" name="product_id" value="{{ $children_product->id }}" />
                                                    <button class="btn-ultra2 btn-shop prim-btn fontSize18">{{ trans('main.product.purchase_here') }}</button>
                                                </form>
                                            </td>
                                        </tr>
                                       @endforeach
                                @endforeach
                            @endif
                        </table>
                      </div>
                    </div>
          
@else  
          
                    <div class="row">
                      <div class="col-sm-8 col-sm-offset-2">
                        <form action="{{ route('product_shop') }}" method="post" target="_blank">
                          <input type="hidden" name="_token" value="{{ csrf_token() }}">
                          <input type="hidden" name="product_id" value="{{ $product->id }}" />
                          <button class="btn-ultra btn-shop prim-btn">{{ trans('main.product.purchase_here') }}</button>
                        </form>
                        <hr>
                        <button data-url="{{ route('wishlist_item') }}" data-pi="{{ $product->id }}" class="btn-ultra btn-wishlist .def-btn wishlist {{ $product->onUserWishlist(\Auth::user() ? \Auth::user()->id : null) ? 'on-wishlist' : '' }}">
                          <i class="fa fa-heart-o fa-lg"></i>
                          <i class="fa fa-heart fa-lg"></i>
                          <span class="not-added">{{ trans('main.product.add_to_wishlist') }}</span>
                          <span class="added">{{ trans('main.product.added_to_wishlist') }}</span>
                        </button>
                      </div>
                    </div>

@endif
                
                    <div class="row">
                        <div class="col-sm-12 text-center">
                            <h2 class="share-text">{{ trans('main.product.share') }}</h2>
                        </div>
                        <div class="col-sm-12 text-center social-share-list-compact">
                            <button class="sns" data-sns="facebook" data-sns-title="{{ settings('app.app_title').' - '. $product->title }}" data-sns-url="{{ Request::url() }}"><i class="fa fa-lg fa-facebook"></i></button>
                            <button class="sns" data-sns="twitter" data-sns-title="{{ settings('app.app_title').' - '. $product->title }}" data-sns-url="{{ Request::url() }}"><i class="fa fa-lg fa-twitter"></i></button>
                            <button class="sns" data-sns="google+" data-sns-title="{{ settings('app.app_title').' - '. $product->title }}" data-sns-url="{{ Request::url() }}"><i class="fa fa-lg fa-google-plus"></i></button>
                        </div>
                    </div>
                    
                    
                </div>
            </div>
        </div>
    </div>
    @if($similar_products)
        <div class="row related-products">
            <div class="col-sm-10 col-sm-offset-1">
                <hr/>
                <h3>{{ trans('main.products.similar_products_header') }}</h3>
                <div class="products-list">
                    @foreach($similar_products as $similar_product)
                        @include('main/product_tile', ['product' => $similar_product])
                    @endforeach
                </div>
            </div>
        </div>
    @endif
@endsection