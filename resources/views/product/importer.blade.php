@extends('admin')

@section('main_content')
    <div class="row product-import">
        <div class="col-xs-12 col-sm-8 col-sm-offset-2">
            <div class="panel">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-xs-12">
                            <h2>{{ $productHeaderTrans }}</h2>
                            <p>
                                {{ trans('product.importer.welcome') }}
                            </p>
                        </div>
                    </div>
                    <div class="row" style="margin-top: 50px;">
                        <div class="col-xs-12 text-center">
                            <span class="btn btn-success fileinput-button btn-lg" id="upload-btn">
                                <i class="icon-plus icon-white"></i>
                                <span data-loading-text="{{ trans('product.importer.uploading') }}">{{ trans('product.importer.select_file') }}</span>
                                <!-- The file input field used as target for the file upload widget -->
                                <input importtype="{{$importType}}" id="fileupload" type="file" name="file_to_import">
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
                    <h4 class="modal-title">{{ trans('product.importer.ongoing_process') }}</h4>
                </div>
                <div class="modal-body">
                    <div class="infinite-progress">
                        <div class="progress">
                            <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
                            </div>
                        </div>
                    </div>
                    <div class="persistent-messages">

                    </div>
                    <div class="message-wrapper">

                    </div>
                    <div class="close-button-wrapper text-center" style="margin-top: 25px; display: none;">
                        <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('product.importer.close') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection