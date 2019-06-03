@extends('admin')

@section('main_content')
    <div class="row translation-import">
        <div class="col-xs-12 col-sm-8 col-sm-offset-2">
            <div class="panel">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-xs-12">
                            <h2>{{ trans('translation.importer.import_translations') }}</h2>
                            <p>
                                {{ trans('translation.importer.summary') }}
                            </p>
                        </div>
                    </div>
                    <div class="row" style="margin-top: 50px;" id="upload-button">
                        <div class="col-xs-12 text-center">
                            <span class="btn btn-success fileinput-button btn-lg" id="upload-btn">
                                <i class="icon-plus icon-white"></i>
                                <span data-loading-text="{{ trans('category.importer.uploading') }}">{{ trans('category.importer.select_file') }}</span>
                                <!-- The file input field used as target for the file upload widget -->
                                <input id="fileupload" type="file" name="file_to_import">
                            </span>
                        </div>
                    </div>
                    <div class="row" style="margin-top: 50px;">
                        <div class="col-xs-12 import-results">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade loading-modal" id="loading-modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{{ trans('category.importer.ongoing_operation') }}</h4>
                </div>
                <div class="modal-body">
                    <div class="infinite-progress">
                        <div class="progress">
                            <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
                                <span class="sr-only">{{ trans('category.importer.45_complete') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="persistent-messages">

                    </div>
                    <div class="message-wrapper">

                    </div>
                    <div class="close-button-wrapper text-center" style="margin-top: 25px; display: none;">
                        <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('category.importer.close') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

{{--@section('stylesheets')--}}
    {{--<link rel="stylesheet" href="{{ asset(settings('app.route_prefix').'/dist/css/file-upload.css') }}{{ cache_handle() }}">--}}
{{--@endsection--}}

{{--@section('javascripts')--}}
    {{--<script type="text/javascript" src="{{ asset(settings('app.route_prefix').'/dist/js/file-upload.js') }}{{ cache_handle() }}"></script>--}}
{{--@endsection--}}