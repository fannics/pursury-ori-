{{-- GLOBAL RELATED --}}
body{
    @if(isset($theme['page_background']))
        background-color: {{ $theme['page_background']}} !important;
    @endif
    @if(isset($theme['page_text_color']))
        color: {{ $theme['page_text_color']}} !important;
    @endif
}
a{
    @if(isset($theme['page_link_color']))
        color: {{ $theme['page_link_color']}}  !important;
    @endif
}
a:hover, a:active, a:focus{
    @if(isset($theme['page_link_active_color']))
        color: {{ $theme['page_link_active_color']}}  !important;
    @endif
}

.pagination>.active>a, .pagination>.active>a:focus, .pagination>.active>a:hover, .pagination>.active>span, .pagination>.active>span:focus, .pagination>.active>span:hover{
    @if(isset($theme['page_secondary_button_background']))
        background-color: {{ $theme['page_secondary_button_background'] }}  !important;
        border-color: {{ $theme['page_secondary_button_background'] }}  !important;
    @endif
}
.prim-btn{
    @if(isset($theme['page_primary_button_background']))
        background-color: {{ $theme['page_primary_button_background'] }} !important;
    @endif
    @if(isset($theme['page_primary_button_text']))
        color: {{ $theme['page_primary_button_text'] }}  !important;
    @endif

}
.prim-btn:hover, .prim-btn:focus, .prim-btn:active{
    @if(isset($theme['page_primary_button_background']))
        background-color: {{ $theme['page_primary_button_background'] }}  !important;
    @endif
    @if(isset($theme['page_primary_button_background']))
        color: {{ $theme['page_primary_button_text'] }}  !important;
    @endif
}
.prim-bordered{
    @if(isset($theme['page_primary_button_background']))
        border-color: {{ $theme['page_primary_button_background'] }} !important;
    @endif
    @if(isset($theme['page_primary_button_background']))
        color: {{ $theme['page_primary_button_background'] }} !important;
    @endif
}
.prim-bordered:hover, .prim-bordered:focus, .prim-bordered:active{
    @if(isset($theme['page_primary_button_background']))
        background-color: {{ $theme['page_primary_button_background'] }} !important;
    @endif
    @if(isset($theme['page_primary_button_background']))
        color: {{ $theme['page_primary_button_text'] }} !important;
    @endif
}
.def-btn{
    @if(isset($theme['page_secondary_button_background']))
        background-color: {{ $theme['page_secondary_button_background'] }} !important;
    @endif
    @if(isset($theme['page_secondary_button_text']))
        color: {{ $theme['page_secondary_button_text'] }} !important;
    @endif
}
.def-btn:hover, .def-btn:focus, .def-btn:active{
    @if(isset($theme['page_secondary_button_background']))
        background-color: {{ $theme['page_secondary_button_background'] }} !important;
    @endif
    @if(isset($theme['page_secondary_button_text']))
        color: {{ $theme['page_secondary_button_text'] }} !important;
    @endif
}

{{-- FOOTER RELATED --}}
#new-footer{
    @if(isset($theme['page_footer_background']))
        background-color: {{ $theme['page_footer_background']}} !important;
    @endif
    @if(isset($theme['page_footer_text_color']))
        color: {{ $theme['page_footer_text_color']}} !important;
    @endif
}
@if(isset($theme['page_footer_text_color']))
    #new-footer h1, #new-footer h2, #new-footer h3, #new-footer h4, #new-footer h5, #new-footer h6{
        color: {{ $theme['page_footer_text_color'] }} !important;
    }
@endif
#new-footer a{
    @if(isset($theme['page_footer_links_color']))
        color: {{ $theme['page_footer_links_color']}} !important;
    @endif
}
#new-footer a:hover, #new-footer a:active, #new-footer a:focus{
    @if(isset($theme['page_footer_links_active_color']))
        color: {{ $theme['page_footer_links_active_color']}} !important;
    @endif
}

{{-- NAVIGATION RELATED  --}}

.custom-menu{
    @if (isset($theme['navigation_background']))
        background-color: {{ $theme['navigation_background'] }} !important;
    @endif
    @if (isset($theme['navigation_shadow_color']))
        -webkit-box-shadow: 0px 0px 10px {{ $theme['navigation_shadow_color'] }} !important;
        -moz-box-shadow: 0px 0px 10px {{ $theme['navigation_shadow_color'] }} !important;
        box-shadow: 0px 0px 10px {{ $theme['navigation_shadow_color'] }} !important;
    @endif
}

.small-navbar{
    @if (isset($theme['navigation_background']))
        background-color: {{ $theme['navigation_background'] }} !important;
    @endif
}

.custom-menu .submenu{
    @if (isset($theme['submenu_background']))
        background-color: {{ $theme['submenu_background'] }} !important;
    @endif
}
.menu-link{
    @if (isset($theme['navigation_links_color']))
        color: {{ $theme['navigation_links_color'] }} !important;
    @endif
}
.menu-link:hover, .menu-link:active, .menu-link:focus{
    @if (isset($theme['navigation_links_active_color']))
        color: {{ $theme['navigation_links_active_color'] }} !important;
    @endif
}
.submenu-header{
    @if (isset($theme['submenu_headers_color']))
        color: {{ $theme['submenu_headers_color'] }} !important;
    @endif
}
.submenu-header:hover, .submenu-header:active, .submenu-header:focus{
    @if (isset($theme['submenu_headers_active_color']))
        color: {{ $theme['submenu_headers_active_color'] }} !important;
    @endif
}
.submenu-link{
    @if (isset($theme['submenu_links_color']))
        color: {{ $theme['submenu_links_color'] }} !important;
    @endif
}
.submenu-link:hover, .submenu-link:active, .submenu-link:focus{
    @if (isset($theme['submenu_links_active_color']))
        color: {{ $theme['submenu_links_active_color'] }} !important;
    @endif
}

{{-- HOMEPAGE TOP SECTION --}}
.front-top-header-text{
    @if (isset($theme['homepage_top_section_title_color']))
        color: {{ $theme['homepage_top_section_title_color'] }} !important;
    @endif
}

.top-header-button{
    @if (isset($theme['homepage_button_border_color']))
        border-color: {{ $theme['homepage_button_border_color'] }} !important;
        color: {{ $theme['homepage_button_border_color'] }} !important;
    @endif
}
.top-header-button:focus:focus, .top-header-button:active, .top-header-button:hover{
    @if (isset($theme['homepage_button_active_border_color']))
        border-color: {{ $theme['homepage_button_active_border_color'] }} !important;
        background-color: {{ $theme['homepage_button_active_border_color'] }} !important;
        @if (isset($theme['homepage_button_active_text_color']))
            color: {{ $theme['homepage_button_active_text_color'] }}  !important;
        @endif
    @endif
}

.top-header-button.bottom {
    @if (isset($theme['homepage_bottom_button_border_color']))
        border-color: {{ $theme['homepage_bottom_button_border_color'] }} !important;
        color: {{ $theme['homepage_bottom_button_border_color'] }} !important;
    @endif
}

.top-header-button.bottom:hover, .top-header-button.bottom:active, .top-header-button.bottom:focus {
    @if (isset($theme['homepage_bottom_button_active_border_color']))
        border-color: {{ $theme['homepage_bottom_button_active_border_color'] }} !important;
        background-color: {{ $theme['homepage_bottom_button_active_border_color'] }} !important;
        @if (isset($theme['homepage_bottom_button_active_text_color']))
            color: {{ $theme['homepage_bottom_button_active_text_color'] }} !important;
        @endif

    @endif
}

.front-bottom-header{
    @if (isset($theme['homepage_home_top_title_color']))
        color: {{ $theme['homepage_home_top_title_color'] }} !important;
    @endif
}
.bottom-section-header{
    @if (isset($theme['homepage_home_top_headers_color']))
        color: {{ $theme['homepage_home_top_headers_color'] }} !important;
    @endif
}
.bottom-section-header:focus, .bottom-section-header:hover, .bottom-section-header:active{
    @if (isset($theme['homepage_home_top_headers_active_color']))
        color: {{ $theme['homepage_home_top_headers_active_color'] }} !important;
    @endif
}
.bottom-section-link{
    @if (isset($theme['homepage_home_top_links_color']))
        color: {{ $theme['homepage_home_top_links_color'] }} !important;
    @endif
}
.bottom-section-link:hover, .bottom-section-link:focus, .bottom-section-link:active{
    @if (isset($theme['homepage_home_top_links_active_color']))
        color: {{ $theme['homepage_home_top_links_active_color'] }} !important;
    @endif
}

