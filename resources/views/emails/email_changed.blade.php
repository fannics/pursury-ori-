@extends('emails/template2_converted')

@section('template_content')
    <p style="font-family: 'Helvetica Neue','Helvetica',Helvetica,Arial,sans-serif; font-size: 14px; line-height: 1.6em; font-weight: normal; margin: 0 0 20px; padding: 0;">
        <b style="font-family: 'Helvetica Neue','Helvetica',Helvetica,Arial,sans-serif; font-size: 100%; line-height: 1.6em; margin: 0; padding: 0;">{{ trans('emails.email_changed.email_change') }}</b>
        <br style="font-family: 'Helvetica Neue','Helvetica',Helvetica,Arial,sans-serif; font-size: 100%; line-height: 1.6em; margin: 0; padding: 0;">
        {{ trans('emails.email_changed.email_confirm_change') }}
        <b style="font-family: 'Helvetica Neue','Helvetica',Helvetica,Arial,sans-serif; font-size: 100%; line-height: 1.6em; margin: 0; padding: 0;">{{ $email_address }}.</b>
    </p>
@endsection