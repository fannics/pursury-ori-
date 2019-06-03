@extends('emails/master')

@section('template_content')
    <table style="border-top:2px solid #f3f3f3" bgcolor="#FFFFFF" border="0" cellpadding="0" cellspacing="0" width="660">
        <tbody>
        <tr>
            <td>
                <div style="font-size:30px;line-height:30px"> </div>
            </td>
        </tr>
        <tr>
            <td width="660" align="center"><img src="{{ asset(settings('app.route_prefix').'/images/email_templates/key.png') }}" style="display:block" border="0" width="34)" class="CToWUd"></td>
        </tr>
        <tr>
            <td>
                <div style="font-size:30px;line-height:30px"> </div>
            </td>
        </tr>
        <tr>
            <td style="font-family:helvetica,arial,sans-serif;color:#646464;font-size:14px;line-height:22px" valign="top" align="center"><span style="font-size:22px;font-weight:bold;line-height:26px">{{ trans('emails.activation.almost_finished') }}<br></span></td>
        </tr>
        </tbody>
    </table>
    <table bgcolor="#FFFFFF" border="0" cellpadding="0" cellspacing="0" width="660" align="center">
        <tbody>
        <tr>
            <td>
                <div style="font-size:20px;line-height:20px"> </div>
            </td>
        </tr>
        </tbody>
    </table>
    <table bgcolor="#FFFFFF" border="0" cellpadding="0" cellspacing="0" width="660" align="center">
        <tbody>
        <tr>
            <td style="font-family:helvetica,arial,sans-serif;color:#646464;font-size:16px;line-height:22px;padding-left:20px;padding-right:20px" valign="top" align="center">
                {{ trans('emails.activation.confirm_activate') }}<b>.</b></td>
        </tr>
        </tbody>
    </table>

    <table bgcolor="#FFFFFF" border="0" cellpadding="0" cellspacing="0" width="660" align="center">
        <tbody>
        <tr>
            <td>
                <div style="font-size:20px;line-height:20px"> </div>
            </td>
        </tr>
        </tbody>
    </table>

    <table bgcolor="#FFFFFF" border="0" cellpadding="0" cellspacing="0" width="660" align="center">
        <tbody>

        <tr>
            <td style="padding-left:20px;padding-right:20px" valign="top" width="600" align="center">
                <a href="{{ $activation_link }}" style="text-decoration: none; padding: 5px 20px; border: 1px solid #1a1c21; color: #fff; background-color: #1a1c21">
                  {{ trans('emails.activation.confirm') }}
                </a>
            </td>
        </tr>
        </tbody>
    </table>

    <table bgcolor="#FFFFFF" border="0" cellpadding="0" cellspacing="0" width="660" align="center">

        <tbody>
        <tr>
            <td>
                <div style="font-size:20px;line-height:19px"> </div>
            </td>
        </tr>
        <tr>
            <td width="660">
                <div style="border-bottom:1px solid #e0e0e0;width:660px;min-height:2px"></div>
            </td>
        </tr>
        <tr>
            <td>
                <div style="font-size:20px;line-height:40px"> </div>
            </td>
        </tr>
        <tr>
            <td>
                <table style="color:#949494" bgcolor="#FFFFFF" border="0" cellpadding="0" cellspacing="0" width="660" align="center">
                    <tbody>

                    <tr>
                        <td align="center">
                            <span style="font-family:helvetica,arial;font-weight:bold;font-size:16px">
                                {{ trans('emails.activation.where_my_password') }}                            
                             </span>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <div style="font-size:15px;line-height:15px"> </div>
                        </td>
                    </tr>

                    <tr>
                        <td align="center">
                          <span style="font-family:helvetica,arial;font-size:15px;line-height:22px;display:block">
                            {{ trans('emails.activation.password_encrypted') }}
                            <br>
                          </span>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <div style="font-size:20px;line-height:0px"> </div>
                        </td>
                    </tr>

                    </tbody>
                </table>
            </td>
        </tr>
        </tbody>
    </table>
@endsection