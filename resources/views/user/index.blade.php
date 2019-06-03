@extends('admin')

@section('main_content')
    <div class="row">
        <div class="col-xs-12">
            <div class="panel">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-xs-6">
                            <h2>{{ trans('user.index.users') }}</h2>
                            <p>
                                {{ trans('user.index.users_hint') }}
                            </p>
                        </div>
                        <div class="col-xs-6 text-right">
                            <a class="btn btn-success" href="{{ route('admin_users_edit_form') }}"><i class="fa fa-plus"></i> {{ trans('user.index.create_user') }}</a>
                        </div>
                    </div>
                    <div class="row data-table-container">
                        <div class="col-xs-12">
                            <table class="table table-striped" id="users-table">
                                <thead>
                                    <tr>
                                        <th>{{ trans('user.index.name') }}</th>
                                        <th>{{ trans('user.index.email') }}</th>
                                        <th>{{ trans('user.index.genre') }}</th>
                                        <th>{{ trans('user.index.active') }}</th>
                                        <th>{{ trans('user.index.newsletter') }}</th>
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
    <script>
      var global_errowWhenDoingAction = "{{ trans('javascript.errowWhenDoingAction') }}";
    </script>
@endsection

@section('javascripts')
    <script type="text/javascript">
        $(function(){

            var createProductsTable = function(){

                var table = $('#users-table');

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
                    ],
                    columns: [
                        {
                            data: 'name'
                        },
                        {
                            data: 'email'
                        },
                        {
                            data: 'gender'
                        },
                        {
                            data: 'active',
                            className: 'text-center',
                            render: function(data, type, full, meta){
                                if (full.active == true){
                                    return '<a href="#" class="universal-toggler global-action"  data-action="toggle" data-otype="User" data-oid="' + full.id + '"><i class="fa fa-check-circle fa-lg"></i></a>';
                                } else {
                                    return '<a href="#" class="universal-toggler global-action"  data-action="toggle" data-otype="User" data-oid="' + full.id + '"><i class="fa fa-times-circle fa-lg"></i></a>';
                                }
                            }
                        },
                        {
                            data: 'newsletter',
                            className: 'text-center',
                            render: function(data, type, full, meta){
                                if (full.newsletter == true){
                                    return '<a href="#" class="universal-toggler"><i class="fa fa-check-circle fa-lg"></i></a>';
                                } else {
                                    return '<a href="#" class="universal-toggler"><i class="fa fa-times-circle fa-lg"></i></a>';
                                }
                            }
                        },
                        {
                            className: 'table-actions text-center',
                            orderable: false,
                            render: function( data, type, full, meta){
                                return '<a href="' + window.app_prefix + '/admin/users/edit/' + full.id + '"><i class="fa fa-edit fa-lg"></i></a>' +
                                        '&nbsp;&nbsp;&nbsp;<a href="#" class="global-action" data-action="remove" data-otype="User" data-oid="' + full.id + '" data-confirm="{{ trans('user.index.confirm_delete_single') }}"  ><i class="fa fa-trash fa-lg"></i></a>';
                            }
                        }
                    ]
                });
            };

            createProductsTable();
        });
    </script>
@endsection