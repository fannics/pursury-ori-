@extends('admin')

@section('main_content')
    <div class="row">
        <div class="col-xs-12">
            <div class="panel">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-xs-6">
                            <h2>{{ trans('admin.imports.imported_files') }}</h2>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <form action="{{ route('admin_users_batch') }}" method="post">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                                <table class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th>{{ trans('admin.imports.user_name') }}</th>
                                        <th>{{ trans('admin.imports.email') }}</th>
                                        <th>{{ trans('admin.imports.import_type') }}</th>
                                        <th>{{ trans('admin.imports.date') }}</th>
                                        <th>{{ trans('admin.imports.imported_file') }}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($imports as $import)
                                            <tr>
                                                <td>{{ $import->name }}</td>
                                                <td>{{ $import->email }}</td>
                                                <td>{{ $import->type == 'categories' ? trans('admin.imports.categories') : trans('admin.imports.products') }}</td>
                                                <td>{{ $import->created_at }}</td>
                                                <td><a target="_blank" href="{{ route('feed_url', ['type' => $import->type, 'feed' => $import->filename]) }}">{{ urldecode($import->filename) }}</a></td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4">{{ trans('admin.imports.no_imports_yet') }}</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="5" class="text-right">
                                                {!! $imports->render() !!}
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection