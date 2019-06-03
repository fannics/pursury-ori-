@extends('admin')

@section('main_content')

    <div class="row">
        <div class="col-xs-12">
            <div class="panel">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-xs-12 col-sm-2">
                            <h3>{{ trans('admin.logs.files') }}</h3>
                            <ul class="log-list list-unstyled">
                                @foreach($files as $file)
                                    <li>
                                        <a href="?l={{ base64_encode($file) }}" class="list-group-item @if ($current_file == $file) llv-active @endif">
                                            {{$file}}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="col-xs-12 col-sm-10">
                            <h3>{{ trans('admin.logs.logs') }}</h3>
                            @if ($logs === null)
                                <div>
                                    {{ trans('admin.logs.cannot_show_log') }}
                                </div>
                            @else
                                @if ($log_type == 'regular_log')
                                    <table id="table-log" class="table table-striped table-container logs-table-container">
                                        <thead>
                                        <tr>
                                            <th>{{ trans('admin.logs.class') }}</th>
                                            <th>{{ trans('admin.logs.date') }}</th>
                                            <th>{{ trans('admin.logs.content') }}</th>
                                        </tr>
                                        </thead>
                                        <tbody>

                                        @foreach($logs as $key => $log)
                                            <tr>
                                                <td class="text-{{$log['level_class']}}"><span class="{{ translateLogLevelImage($log['level_img']) }}" aria-hidden="true"></span> &nbsp;{{$log['level']}}</td>
                                                <td class="date">{{$log['date']}}</td>
                                                <td class="text">
                                                    @if ($log['stack'])
                                                        <a class="pull-right expand btn btn-default btn-xs" data-display="stack{{$key}}">
                                                            <i class="fa fa-search"></i>
                                                        </a>
                                                    @endif
                                                        {{$log['text']}}
                                                    @if (isset($log['in_file']))
                                                            <br />{{$log['in_file']}}
                                                    @endif
                                                    @if ($log['stack'])
                                                            <div class="stack" id="stack{{$key}}" style="display: none; white-space: pre-wrap;">{{ trim($log['stack']) }}</div>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach

                                        </tbody>
                                    </table>
                                @elseif($log_type == 'import_log')
                                    <div>
                                        <?php
                                            echo utf8_encode(nl2br($logs))
                                        ?>
                                    </div>
                                @endif
                            @endif
                            <div class="text-right">
                                <a href="?dl={{ base64_encode($current_file) }}"><i class="fa fa-download"></i> {{ trans('admin.logs.download_file') }}</a>
                                -
                                <a id="delete-log" href="?del={{ base64_encode($current_file) }}"><span class="fa fa-trash-o"></span> {{ trans('admin.logs.delete_file') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

