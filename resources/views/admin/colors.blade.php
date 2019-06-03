@extends('admin')

@section('main_content')

    <div class="row">
        <div class="col-xs-12">
            <div class="panel">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-xs-6">
                            <h2>{{ trans('admin.colors.colors') }}</h2>
                            <p>
                                {{ trans('admin.colors.summary') }}
                            </p>
                        </div>
                        <div class="col-xs-6 text-right">
                            <a class="btn btn-warning" href="#">{{ trans('admin.colors.existing_colors') }} ({{ $current_colors->total() }})</a>
                            <a class="btn btn-primary" href="{{ route('admin_product_export') }}">{{ trans('admin.colors.incomplete_colors') }} ({{ $incomplete_color_count }})</a>
                            <a class="btn btn-success" href="{{ route('admin_product_export') }}">{{ trans('admin.colors.configured_colors') }} ({{ $complete_color_count }})</a>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>{{ trans('admin.colors.color_name') }}</th>
                                        <th>{{ trans('admin.colors.preview') }}</th>
                                        <th>{{ trans('admin.colors.composed_by') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <label class="control-label">{{ trans('admin.colors.multicolor') }}</label>
                                        </td>
                                        <td>
                                            <img src="{{ asset(settings('app.route_prefix')).'/images/multicolor.png' }}" width="28" height="28" />
                                        </td>
                                        <td>
                                            <input class="form-control" type="text" id="multicolor" name="multicolor" value="{{ settings('app.multicolor_value') }}" style="width: 200px;"/>
                                            <p class="help-block">{{ trans('admin.colors.composed_by') }}</p>
                                              {{ trans('admin.colors.multicolor_summary') }}
                                            </p>
                                        </td>
                                    </tr>
                                    @foreach($current_colors as $color)
                                        @include('admin/colors_items', ['color' => $color])
                                    @endforeach
                                </tbody>
                                <tfoot>
                                <tr>
                                    <td colspan="3" class="text-right">
                                        {!! $current_colors->render() !!}
                                    </td>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascripts')
    <script>
        $(function(){

            var updatePreview = function(parent, parentRow){

                var preview = parentRow.find('.color-preview');

                var colorInputs = parent.find('input.color-holder');

                var sampleWidth = 100 / colorInputs.size();

                preview.empty();

                var colorValues = [];

                colorInputs.each(function(){

                    colorValues.push($(this).val());

                    preview.append('<div class="color-sample" style="width: ' + sampleWidth + '%; background-color: ' + $(this).val() + '"></div>')
                });

                return colorValues;

            };

            var saveChanges = function(colorName, colorValues){
                $.post(window.app_prefix + '/admin/color-codes', {color_name: colorName, components: colorValues})
                        .success(function(res){
                            if (res.needs_reload == true){
                                window.location.href = '';
                            }
                        })
                        .fail(function(){

                        });
            };

            $(document).on('keyup', '.color-holder', function(e){

                if (e.which !== 0 && !e.ctrlKey && !e.metaKey && !e.altKey && !e.shiftKey){

                    var color = $(this).val();
                    var regex = /^#[0-9A-F]{6}$/i;

                    if (regex.test(color)){
                        var parent = $(this).closest('td');
                        var parentRow = $(this).closest('tr');

                        var colorValues = updatePreview(parent, parentRow);

                        saveChanges(parent.attr('data-color-name'), colorValues);

                    }
                }
            });

            $(document).on('click', '.new-color-btn', function(){
                $('<div class="input-group color-input-group">' +
                        '<input type="text" class="form-control color-holder" />' +
                        '<span class="input-group-addon color-component-remove" id="basic-addon1"><i class="fa fa-minus"></i></span>' +
                        '</div>')
                        .insertBefore(this);
            });

            $(document).on('click', '.color-input-group span', function(){
                var parentCol = $(this).closest('td');

                if (parentCol.find('.color-input-group').size() > 0){

                    var parentRow = $(this).closest('tr');
                    var parent = $(this).closest('td')

                    $(this).closest('div').remove();

                    colorValues = updatePreview(parent, parentRow);

                    saveChanges(parent.attr('data-color-name'), colorValues);
                }
            });

            $(document).on('keyup', '#multicolor', function(){

                setTimeout(function(){
                    $.post(window.app_prefix + '/admin/set-multicolor', {multicolor: $('#multicolor').val()})
                            .success(function(){

                            })
                            .fail(function(){

                            });
                }, 1000);

            });
        });
    </script>
@endsection