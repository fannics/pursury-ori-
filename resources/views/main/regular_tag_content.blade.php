@if (count($tag_values) > 0)
    <ul class="filter-tag-list">
        @foreach($tag_values as $tag_value)
            <li data-var-name="tag" data-var-value="{{ $tag_value->id }}">{{ $tag_value->tag_value }}{{ $tag_value->amount ? '('.$tag_value->amount.')' : '' }}</li>
        @endforeach
    </ul>
@else
    <p class="text-center">{{ trans('main.color_tag_content.no_more_filters') }}</p>
@endif
