@extends('admin')

@section('main_content')
    <div class="row">
        <div class="col-xs-12">
            <div class="panel">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-xs-6">
                            <h2>{{ trans('search_engine.index.search_engine') }}</h2>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            @if (isset($search_engine_available) && $search_engine_available == true)
                                @if ($error)
                                    <h3>{{ trans('search_engine.index.error_engine_status') }}</h3>
                                    <p>{{ trans('search_engine.index.error_possible_causes') }}:</p>
                                    <ul>
                                        <li>{{ trans('search_engine.index.engine_not_configured') }}</li>
                                        <li>{{ trans('search_engine.index.engine_not_started') }}</li>
                                        <li>{{ trans('search_engine.index.url_bad_configuration') }}</li>
                                        <li>{{ trans('search_engine.index.index_map_error') }} <a href="{{ route('search_engine_refresh') }}">{{ trans('search_engine.index.here') }}</a> </li>
                                    </ul>
                                @else
                                    <div class="row">
                                        <div class="col-xs-12 col-sm-9">
                                            <h2>{{ trans('search_engine.index.info') }}</h2>
                                            <div class="cluster-info {{ $info['status'] }}">
                                                <p>
                                                    <b>{{ trans('search_engine.index.cluster_status') }}: </b>
                                                    @if($info['status'] == 'red')
                                                        {{ trans('search_engine.index.red_status') }}
                                                    @elseif($info['status'] == 'yellow')
                                                        {{ trans('search_engine.index.yellow_status') }}
                                                    @elseif($info['status'] == 'green')
                                                        {{ trans('search_engine.index.green_status') }}
                                                    @endif
                                                </p>
                                                <p><b>{{ trans('search_engine.index.cluster_name') }}: </b>{{ $info['cluster_name'] }}</p>
                                                <p><b>{{ trans('search_engine.index.amount_nodes') }}: </b>{{ $info['number_of_nodes'] }}</p>
                                                <p><b>{{ trans('search_engine.index.active_primary_shards') }}: </b>{{ $info['active_primary_shards'] }}</p>
                                                <p><b>{{ trans('search_engine.index.active_shards') }}: </b>{{ $info['active_shards'] }}</p>
                                                <p><b>{{ trans('search_engine.index.not_assigned_shards') }}: </b>{{ $info['unassigned_shards'] }}</p>
                                                <p><b>{{ trans('search_engine.index.indexed_products') }}: </b>{{ isset($stats['docs']) ? $stats['docs']['count'] : 0 }}</p>
                                                <p><b>{{ trans('search_engine.index.removed_products') }}: </b>{{ isset($stats['docs']) ? $stats['docs']['deleted'] : 0 }}</p>
                                                <p><b>{{ trans('search_engine.index.index_size') }}: </b>{{ isset($stats['store']) ? $stats['store']['size_in_bytes'] : 0 }}</p>
                                            </div>
                                        </div>
                                        <div class="col-xs-12 col-sm-3">
                                            <h2>Acciones</h2>
                                            <ul class="list-unstyled search-engine-actions">
                                                <li>
                                                    <a href="{{ route('search_engine_refresh') }}" class="btn btn-lg btn-primary"><i class="fa fa-refresh"></i> {{ trans('search_engine.index.update_index') }}</a>
                                                </li>
                                                <li>
                                                    <a href="{{ route('search_engine_empty') }}" class="btn btn-lg btn-danger"><i class="fa fa-trash-o"></i> {{ trans('search_engine.index.empty_index') }}</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                @endif
                            @else
                                <h3>{{ trans('search_engine.index.engine_not_configured') }}</h3>
                                <p>{{ trans('search_engine.index.engine_not_configured_hint') }} <a href="{{ route('admin_settings') }}">{{ trans('search_engine.index.here') }}</a></p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @if (!$error)
                <div class="panel">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-xs-12">
                                <h2>Nodos</h2>
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>{{ trans('search_engine.index.name') }}</th>
                                            <th>{{ trans('search_engine.index.host') }}</th>
                                            <th>{{ trans('search_engine.index.ip') }}</th>
                                            <th>{{ trans('search_engine.index.cluster') }}</th>
                                            <th>{{ trans('search_engine.index.version') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($nodes as $node)
                                        <tr>
                                            <td>{{ $node['name'] }}</td>
                                            <td>{{ $node['host'] }}</td>
                                            <td>{{ $node['ip'] }}</td>
                                            <td>{{ $node['settings']['cluster']['name'] }}</td>
                                            <td>{{ $node['version'].' build: '.$node['build'] }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection