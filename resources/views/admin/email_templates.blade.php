@extends('admin')

@section('main_content')
    <div class="row">
        <div class="col-xs-12">
            <div class="panel">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-xs-12">
                            <h2>{{ trans('admin.email.email_templates') }}</h2>
                            <p>{{ trans('admin.email.summary') }}</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 col-sm-2">
                            <h4>{{ trans('admin.email.template_files') }}</h4>
                            @if ($master)
                                <ul class="nav nav-pills nav-stacked">
                                    <li>
                                        <a href="{{ route('email_templates_config', ['template' => $master->label]) }}">{{ trans($master->name) }}</a>
                                    </li>
                                </ul>
                                <hr />
                            @endif
                            <ul class="nav nav-pills nav-stacked">
                                @foreach($templates as $t)
                                    <li>
                                        <a href="{{ route('email_templates_config', ['template' => $t->label]) }}">{{ trans($t->name) }}</a>
                                    </li>
                                @endforeach
                            </ul>
                            <ul class="nav nav-pills nav-stacked">
                                <li>
                                    <a href="#" data-toggle="modal" data-target="#email-modal">{{ trans('admin.email.test_email') }}</a>
                                </li>
                                <li>
                                    <a href="#" data-toggle="modal" data-target="#help-modal">{{ trans('admin.email.help') }}</a>
                                </li>
                            </ul>
                        </div>
                        <div class="col-xs-12 col-sm-10">
                            <h4>{{ trans('admin.email.template') }}</h4>
                            <form action="" method="post">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}"/>
                                <input type="hidden" name="template_id" value="{{ $template->id }}"/>
                                <div class="control-group">
                                    <p>{{ trans($template->reason) }}</p>
                                </div>
                                <div class="control-group">
                                    <textarea name="template_content" id="template_content" required cols="30" rows="20" class="form-control">{{ $template_content }}</textarea>
                                </div>
                                @if($template->available_variables)
                                    <div class="control-group">
                                        <label for="">{{ trans('admin.email.available_variables') }}: {{ $template->available_variables }}</label>
                                    </div>
                                @endif
                                <div class="control-group text-right">
                                    <button class="btn btn-primary" type="submit" style="margin-top: 10px;">{{ trans('admin.email.save_changes') }}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="help-modal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">{{ trans('admin.email.help_modal_title') }}</h4>
                </div>
                <div class="modal-body" style="max-height: 600px; overflow: auto;">
                    {!! trans('admin.email.help_modal_body') !!}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('admin.email.help_modal_close') }}</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="email-modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">{{ trans('admin.email.test_modal_test_email') }}</h4>
                </div>
                <div class="modal-body">
                    <p>{{ trans('admin.email.test_modal_summary') }}</p>
                    <form action="{{ route('admin_test_mail') }}" id="test-email-form">
                        <div class="alert" role="alert" style="display: none;"></div>
                        <div class="control-group">
                            <label for="email_address">{{ trans('admin.email.test_modal_email_address') }}</label>
                            <input type="email" name="email_address" id="email_address" class="form-control" />
                        </div>
                        <div class="control-group">
                            <label for="email_template">{{ trans('admin.email.test_modal_selected_template') }}</label>
                            <select name="email_template" id="email_template" class="form-control">
                                @foreach($templates as $t)
                                    <option value="{{ $t->label }}">{{ trans($t->name) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('admin.email.test_modal_close') }}</button>
                    <button type="button" class="btn btn-primary" id="send-mail-btn">{{ trans('admin.email.test_modal_send') }}</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    <script>
      var global_testEmailSucccess = "{{ trans('javascript.test_email_success') }}";
    </script>
@endsection
