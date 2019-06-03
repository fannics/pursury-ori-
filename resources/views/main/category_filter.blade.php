<div class="filter-wrapper">
    <div class="clear-filter">
        <a href="#" class="clear-filter-btn"><i class="fa fa-times"></i> {{ trans('main.category_filter.remove_filter') }}</a>
    </div>
    <div class="filter-header">
        <input type="text" name="" id="" class="form-control" />
    </div>
    <div class="filter-values">
        <ul>
            @foreach($values as $value)
                <li class="filter-value" data-filter-value="{{ str_slug(strtolower($value->property_value)) }}"><a href="#">{{ $value->property_value }}</a></li>
            @endforeach
        </ul>
    </div>
</div>
