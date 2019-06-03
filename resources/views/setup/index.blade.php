@extends('admin')

@section('main_content')
    <div class="row">
        <div class="col-xs-12">
            <div class="panel">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-xs-6">
                            <h2>{{ trans('setup.index.master_setup') }}</h2>
                            <p>
                              <b>{{ trans('setup.index.master_setup_hint') }}</b>
                              <br> 
                              <small>{{ trans('setup.index.master_setup_subhint') }}</small> 
                            </p>
                        </div>
                        <div class="col-xs-6 text-right">
                            <a class="btn btn-success" href="{{ route('admin_setups_create_form') }}"><i class="fa fa-plus"></i> {{ trans('setup.index.add_setup') }}</a>
                        </div>
                    </div>
                    <div class="row data-table-container">
                        <div class="col-xs-12">
                            <table class="table table-striped" id="setups-table">
                                <thead>
                                <tr>
                                    <th>{{ trans('setup.index.country') }}</th>
                                    <th>{{ trans('setup.index.language') }}</th>
                                    <th>{{ trans('setup.index.default_language') }}</th>
                                    <th>{{ trans('setup.index.currency') }}</th>
                                    <th>{{ trans('setup.index.curency_symbol') }}</th>
                                    <th>{{ trans('setup.index.symbol_position') }}</th>
                                    <th>{{ trans('setup.index.currency_decimal') }}</th>
                                    <th>{{ trans('setup.index.default_setup') }}</th>
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

            var createSetupsTable = function(){

                var table = $('#setups-table');

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
                        "sSearch":         "{{ trans('category.index.search') }}:",
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
                            data: 'country_abre',
                        },
                        {
                            data: 'language_abre',
                        },
                        {
                            data: 'default_language',
                        },
                        {                                                                                
                            data: 'currency',
                        },
                        {
                            data: 'currency_symbol',
                        },
                        {
                            data: 'before_after',
                        },
                        {
                            data: 'currency_decimal',
                        },
                        {
                            data: 'default_setup',
                        },                                                                                              
                        {
                            className: 'table-actions text-center',
                            orderable: false,
                            render: function( data, type, full, meta)
                            {
                              if ( (full.country_abre != '{{ get_current_country() }}') || (full.language_abre != '{{ get_current_language() }}')  ) {
                                return '<a href="' + window.app_prefix + '/admin/setups/edit/' + full.id + '"><i class="fa fa-edit fa-lg"></i></a>' +
                                        '&nbsp;&nbsp;&nbsp;<a href="#" class="global-action" data-action="remove" data-otype="Setup" data-oid="' + full.id + '" data-confirm="{{ trans('setup.index.confirm_delete_single') }}"  ><i class="fa fa-trash fa-lg"></i></a>';
                              }
                              else {
                                return '<b><small class="redText">{{ trans('setup.index.using_this_now') }}</small></b>';
                              }
                            }
                        }
                    ]
                });
                
            };

            createSetupsTable();
        });
    </script>
@endsection