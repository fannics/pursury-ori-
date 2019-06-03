@if (count($colors) > 0)
  <ul class="filter-tag-list color-tags">
    @foreach($colors as $color)
        <li data-var-name="tag" data-var-value="{{ $color->id }}">
            @if($color->tag_value != settings('app.multicolor_value'))
              <div class="color-preview left" style="{{ !$color->color_code || strpos($color->color_code, 'transparent') !== false ? 'display: none' : '' }}">
                <?php
                    if ($color->tag_value !== settings('app.multicolor_value')) {
                        if (isset($color->color_code) && strpos($color->color_code, 'transparent') === false) {
                            $parts = explode('/', $color->color_code);
                            $part_size = 100 / count($parts);
                            foreach($parts as $key=>$color_value){
                            ?>

                                <div class="color-sample" style="background-color: <?php echo $color_value ?>; width: <?php echo $part_size.'%' ?>"></div>

                            <?php
                            }
                        }
                    }
                ?>
              </div>
            @else
                <img class="left" src="/images/multicolor.png" width="18" height="18" style="margin-right: 5px;" />
            @endif
            <span class="left">{{ $color->tag_value }}{{ $color->amount ? '('.$color->amount.')' : '' }}</span>
        </li>
    @endforeach
  </ul>
@else
  <p class="text-center">{{ trans('main.color_tag_content.no_more_filters') }}</p>
@endif