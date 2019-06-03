@extends('admin')

@section('main_content')
    <div class="row">
        <div class="col-xs-12">
            <div class="panel">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-xs-6">
                            <h2>{{ trans('translation.index.translations') }}</h2>
                            <p>
                                {{ trans('translation.index.summary') }}
                            </p>
                        </div>
                        <div class="col-xs-6 text-right">
                            <a class="btn btn-info" target="_blank" href="{{ route('admin_translation_export') }}">{{ trans('translation.index.template') }}</a>
                            <a class="btn btn-success" href="{{ route('admin_translations_import') }}">{{ trans('translation.index.import') }}</a>
                        </div>
                    </div>
                    <div class="row data-table-container">
                        <div class="col-xs-12">
                            <table class="table table-striped" id="translations-table">
                                <thead>
                                <tr>
                                    <th>{{ trans('translation.index.group') }}</th>
                                    <th>{{ trans('translation.index.item') }}</th>
                                    <th>{{ trans('translation.index.text') }}</th>
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

            var createTranslationsTable = function(){

                var table = $('#translations-table');
                table.dataTable({
                    ajax: {
                        url: $(this).attr('data-feed')
                    },
                    language: {
                        "sProcessing":     "{{ trans('category.index.processing') }}",
                        "sLengthMenu":     "{{ trans('category.index.show_records') }}",
                        "sZeroRecords":    "{{ trans('category.index.no_results') }}",
                        "sEmptyTable":     "{{ trans('translation.index.no_data_available') }}",
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
                    ],
                    columns: [
                        {
                            data: 'group'
                        },
                        {
                            data: 'item',
                        },
                        {
                            data: 'text',
                        },
                        {
                            className: 'table-actions text-center',
                            orderable: false,
                            render: function( data, type, full, meta){

                                return '<a href="' + window.app_prefix + '/admin/translations/edit/' + full.id + '"><i class="fa fa-edit fa-lg"></i></a>' +
                                        '&nbsp;&nbsp;&nbsp;<a href="#" class="global-action" data-action="remove" data-otype="Translation" data-oid="' + full.id + '" data-confirm="{{ trans('translation.index.confirm_delete_single') }}"  ><i class="fa fa-trash fa-lg"></i></a>';
                            }
                        }
                    ]
                });
            };

            createTranslationsTable();
        });
    </script>
@endsection