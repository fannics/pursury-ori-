@extends('admin')

@section('main_content')
    <div class="row">
        <div class="col-xs-12">
            <div class="panel">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-xs-6">
                            <h2>{{ trans('search_engine.latest.searches') }}</h2>
                            <p>
                                {{ trans('search_engine.latest.searches_hint') }}
                            </p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>{{ trans('search_engine.latest.used_keyword') }}</th>
                                    <th>{{ trans('search_engine.latest.found_results') }}</th>
                                    <th>{{ trans('search_engine.latest.user') }}</th>
                                    <th>{{ trans('search_engine.latest.date') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($searches as $search)
                                    <tr>
                                        <td>{{ $search->used_term }}</td>
                                        <td>{{ $search->results_found }}</td>
                                        <td>
                                            @if ($search->name)
                                                {{ $search->name.' ( '.$search->email.' )' }}
                                            @else
                                                {{ trans('search_engine.latest.anonymous_user') }}
                                            @endif
                                        </td>
                                        <td>{{ $search->created_at }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="4" class="text-right">
                                            {!! $searches->render() !!}
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

