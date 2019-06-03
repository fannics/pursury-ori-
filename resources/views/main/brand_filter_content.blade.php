<ul class="filter-tag-list">
    @foreach($brands as $brand)
        @if ($brand->brand)
            <li data-var-name="brand" data-var-value="{{ $brand->brand }}">{{ $brand->brand }} {{ $brand->amount ? '('.$brand->amount.')' : ''}}</li>
        @endif
    @endforeach
</ul>
