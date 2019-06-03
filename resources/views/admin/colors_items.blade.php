<tr>
    <td>
        <b>{{ $color->value }}</b>
    </td>
    <td class="color-preview-column">
        <div class="color-preview">
            <?php

                $parts = explode('/', $color->color_code);

                $part_size = 100 / count($parts);

                foreach($parts as $key=>$color_value){
                    ?>

                    <div class="color-sample" style="background-color: <?php echo $color_value ?>; width: <?php echo $part_size.'%' ?>"></div>

                    <?php
                }
            ?>
        </div>
    </td>
    <td data-color-name="{{ $color->value }}">
        {{--{{ dump($color->color_code) }}--}}
        @foreach(explode('/', $color->color_code) as $key=>$color_value)
            @if ($key == 0 )
                <input type="text" class="form-control color-holder single-color-holder" value="{{ $color_value != 'transparent' ? $color_value : '' }}" />
            @else
                <div class="input-group color-input-group">
                    <input type="text" class="form-control color-holder" value="{{ $color_value != 'transparent' ? $color_value : '' }}" />
                    <span class="input-group-addon color-component-remove" id="basic-addon1"><i class="fa fa-minus"></i></span>
                </div>
            @endif
        @endforeach
        <div class="new-color left">
            <button class="btn btn-primary new-color-btn"><i class="fa fa-plus"></i></button>
        </div>
    </td>
</tr>