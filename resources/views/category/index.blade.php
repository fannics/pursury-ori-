@extends('admin')

@section('main_content')
    <div class="row">
        <div class="col-xs-12">
            <div class="panel">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-xs-6">
                            <h2>{{ trans('category.index.categories') }}</h2>
                            <p>
                                {{ trans('category.index.summary') }}
                            </p>
                        </div>
                        <div class="col-xs-6 text-right">
                            <a class="btn btn-primary" target="_blank" href="{{ route('admin_category_export') }}">{{ trans('category.index.export') }}</a>
                            <a class="btn btn-success" href="{{ route('admin_categories_import') }}">{{ trans('category.index.import') }}</a>
                            <a class="btn btn-info" target="_blank" href="{{ route('admin_category_import_starter') }}">{{ trans('category.index.blank_template') }}</a>
                            <a class="btn btn-danger" href="{{ route('admin_category_sorting') }}">{{ trans('category.index.sort_categories') }}</a>
                        </div>
                    </div>
                    <div class="row data-table-container">
                        <div class="col-xs-12">
                            <table class="table table-striped" id="categories-table">
                                <thead>
                                <tr>
                                    <th>{{ trans('category.index.name') }}</th>
                                    <th>{{ trans('category.index.parent_category') }}</th>
                                    <th>Url</th>
                                    <th>{{ trans('category.index.filters') }}</th>
                                    <th>{{ trans('category.index.visible') }}</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascripts')
    <script type="text/javascript">
        $(function(){

            var createProductsTable = function(){

                var table = $('#categories-table');
                table.dataTable({
                    ajax: {
                        url: $(this).attr('data-feed')
                    },
                    language: {
                        "sProcessing":     "{{ trans('category.index.processing') }}",
                        "sLengthMenu":     "{{ trans('category.index.show_records') }}",
                        "sZeroRecords":    "{{ trans('category.index.no_results') }}",
                        "sEmptyTable":     "{{ trans('category.index.no_data_available') }}",
                        "sInfo":           "{{ trans('category.index.show_from_to') }}",
                        "sInfoEmpty":      "{{ trans('category.index.show_from_to_zero') }}",
                        "sInfoFiltered":   "{{ trans('category.index.filter_from_total') }}",
                        "sInfoPostFix":    "",
                        "sSearch":         "{{ trans('category.index.search') }}",
                        "sUrl":            "",
                        "sInfoThousands":  ",",
                        "sLoadingRecords": "{{ trans('category.index.loading') }}",
                        "oPaginate": {
                            "sFirst":    "{{ trans('category.index.first') }}",
                            "sLast":     "{{ trans('category.index.latest') }}",
                            "sNext":     "{{ trans('category.index.next') }}",
                            "sPrevious": "{{ trans('category.index.previous') }}"
                        },
                        "oAria": {
                            "sSortAscending":  "{{ trans('category.index.sort_asc') }}",
                            "sSortDescending": "{{ trans('category.index.sort_desc') }}"
                        }
                    },
                    serverSide: true,
                    searching: true,
                    select: true,
                    dom: '<lf<t>ip><"clearfix"><B>',
                    buttons: [
                        {
                            text: "{{ trans('category.index.delete') }}",
                            className: 'btn btn-danger',
                            extend: 'selected',
                            available: function(){
                                return true;
                            },
                            action: function(e, dt, node, config){

                                var rows = dt.rows({selected: true});
                                var data = dt.rows({selected: true}).data();

                                var ids = [];

                                for (var i = 0; i< rows.count(); i++){
                                    ids.push(data[i].id);
                                }

                                bootbox.confirm("{{ trans('category.index.confirm_categories_delete') }}", function(res){
                                    $.post(window.app_prefix + '/admin/categories/batch', {action: 'remove', ids: ids})
                                            .success(function(res){
                                                dt.ajax.reload(null, true);
                                                bootbox.alert(res.message);
                                            })
                                            .fail(function(){
                                                bootbox.alert("{{ trans('category.index.error_happened') }}");
                                            });
                                });

                            }
                        },
                        {
                            text: "{{ trans('category.index.change_visibility') }}",
                            className: 'btn btn-success',
                            extend: 'selected',
                            available: function(){
                                return true;
                            },
                            action: function(e, dt, node, config){

                                var rows = dt.rows({selected: true});
                                var data = dt.rows({selected: true}).data();

                                var ids = [];

                                for (var i = 0; i< rows.count(); i++){
                                    ids.push(data[i].id);
                                }

                                bootbox.confirm("{{ trans('category.index.confirm_categories_visibility') }}", function(res){
                                    $.post(window.app_prefix + '/admin/categories/batch', {action: 'toggle', ids: ids})
                                            .success(function(res){
                                                dt.ajax.reload(null, true);
                                                bootbox.alert(res.message);
                                            })
                                            .fail(function(){
                                                bootbox.alert("{{ trans('category.index.error_happened') }}");
                                            });
                                });

                            }
                        }
                    ],
                    columns: [
                        {
                            data: 'title'
                        },
                        {
                            data: 'parent_title',
                        },
                        {
                            data: 'url_key',
                            render: function(data, type, full, meta){
                                return '<a href="' + full.url_key + '">' + full.url_key + '</a>'
                            }
                        },
                        {
                            data: 'filters',
                            className: 'text-center'
                        },
                        {
                            data: 'is_visible',
                            className: 'text-center',
                            render: function(data, type, full, meta){
                                if (full.is_visible == true){
                                    return '<a href="#" class="universal-toggler global-action" data-action="toggle" data-otype="Category" data-oid="' + full.id + '"><i class="fa fa-check-circle fa-lg"></i></a>';
                                } else {
                                    return '<a href="#" class="universal-toggler global-action" data-action="toggle" data-otype="Category" data-oid="' + full.id + '"><i class="fa fa-times-circle fa-lg"></i></a>';
                                }
                            }
                        },
                        {
                            className: 'table-actions text-center',
                            orderable: false,
                            render: function( data, type, full, meta){

                                return '<a href="' + window.app_prefix + '/admin/categories/edit/' + full.id + '"><i class="fa fa-edit fa-lg"></i></a>' +
                                        '<a href="#" class="global-action" data-action="remove" data-otype="Category" data-oid="' + full.id + '" data-confirm="{{ trans('category.index.confirm_delete_single') }}"  ><i class="fa fa-trash fa-lg"></i></a>';
                            }
                        }
                    ]
                });
            };

            createProductsTable();
        });
    </script>
@endsection