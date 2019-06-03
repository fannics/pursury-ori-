<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width">
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>{{ settings('app.app_title') . ' -' }} {{ trans('emails.master.notifications') }}</title>
    </head>
    <body style="font-family: 'Helvetica Neue','Helvetica',Helvetica,Arial,sans-serif; ">
        <table bgcolor="#FAFAFA" border="0" cellpadding="0" cellspacing="0" width="100%" align="center">
            <tbody>
            <tr>
                <td>
                    <div style="font-size:10px;line-height:10px"> </div>
                </td>
            </tr>
            </tbody>
        </table>
        <table bgcolor="#FAFAFA" border="0" cellpadding="0" cellspacing="0" width="100%" align="center">
            <tbody>
            <tr>
                <td align="center">
                    <table style="margin:0 auto" bgcolor="#FFFFFF" border="0" cellpadding="0" cellspacing="0" width="700" align="center">
                        <tbody>
                        <tr>
                            <td width="700" align="center">
                                <table style="margin:0 auto" bgcolor="#FFFFFF" border="0" cellpadding="0" cellspacing="0" width="700" align="center">
                                    <tbody>
                                    <tr>
                                        <td bgcolor="#fafafa" width="14"></td>
                                        <td bgcolor="#f9f9f9" width="2"></td>
                                        <td bgcolor="#f7f7f7" width="2"></td>
                                        <td bgcolor="#f3f3f3" width="2"></td>
                                        <td bgcolor="#FFFFFF" valign="top" width="660">
                                            <table style="border-top:2px solid #f3f3f3" bgcolor="#FFFFFF" border="0" cellpadding="0" cellspacing="0" width="660">
                                                <tbody>
                                                <tr>
                                                    <td width="660" align="center"><a name="-1952761908_2109252053_Logo" style="display:block" href="https://www.shazam.com/" target="_blank">
                                                        <a href="{{ route('homepage', null, true) }}">
                                                            <img src="{{ asset(settings('app.route_prefix').'/images/gcs_logo_large.png') }}" style="display:block; margin: 10px auto;" alt="{{ settings('app.app_title')}}" height="90" border="0" ></a>
                                                        </a>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                        <td bgcolor="#f3f3f3" width="2"></td>
                                        <td bgcolor="#f7f7f7" width="2"></td>
                                        <td bgcolor="#f9f9f9" width="2"></td>
                                        <td bgcolor="#fafafa" width="14"></td>
                                    </tr>
                                    <tr>
                                        <td bgcolor="#fafafa" width="14"></td>
                                        <td bgcolor="#f9f9f9" width="2"></td>
                                        <td bgcolor="#f7f7f7" width="2"></td>
                                        <td bgcolor="#f3f3f3" width="2"></td>
                                        <td bgcolor="#FFFFFF" valign="top" width="660">
                                            @yield('template_content')
                                        </td>
                                        <td bgcolor="#f3f3f3" width="2"></td>
                                        <td bgcolor="#f7f7f7" width="2"></td>
                                        <td bgcolor="#f9f9f9" width="2"></td>
                                        <td bgcolor="#fafafa" width="14"></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            </tbody>
        </table>
        <table bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0" width="100%" align="center">
            <tbody>
            <tr>
                <td valign="top" align="center">
                    <table style="margin:0 auto" bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0" width="700" align="center">
                        <tbody>
                        <tr>
                            <td bgcolor="#1a1c21" valign="top" width="20">
                                <div style="font-size:44px;line-height:44px"> </div>
                            </td>
                            <td alt="" border="0" height="40" bgcolor="#FFFFFF" valign="top" width="660"></td>
                            <td bgcolor="#1a1c21" valign="top" width="20">
                                <div style="font-size:44px;line-height:44px"> </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <table style="margin:0 auto" bgcolor="#1a1c21" border="0" cellpadding="0" cellspacing="0" width="700" align="center">
                        <tbody>
                        <tr>
                            <td valign="top" align="center">
                                <table bgcolor="#1a1c21" border="0" cellpadding="0" cellspacing="0" width="700" align="center">
                                    <tbody>
                                    <tr>
                                        <td valign="top" align="center">
                                            <table bgcolor="#1a1c21" border="0" cellpadding="0" cellspacing="0" width="700" align="center">
                                                <tbody>
                                                <tr>
                                                    <td bgcolor="#1a1c21" valign="top" width="660">
                                                        <div style="font-size:39px;line-height:39px"> </div>
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                            <table bgcolor="#1a1c21" border="0" cellpadding="0" cellspacing="0" width="700" align="center">
                                                <tbody>
                                                <tr>
                                                    <td valign="top" width="20"> </td>
                                                    <td bgcolor="#1a1c21" valign="top" width="660" align="center">
                                                        <a style="display:block" href="{{ route('homepage') }}" target="_blank"><img src="{{ asset(settings('app.route_prefix').'/images/gcs_logo.png') }}" alt="" style="display:block" border="0" width="64" class="CToWUd"></a>
                                                    </td>
                                                    <td valign="top" width="20"> </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                            <table bgcolor="#1a1c21" border="0" cellpadding="0" cellspacing="0" width="700" align="center">
                                                <tbody>
                                                <tr>
                                                    <td valign="top" width="20"> </td>
                                                    <td bgcolor="#1a1c21" valign="top" width="660" align="center">
                                                        <div style="font-size:5px;line-height:5px"> </div>
                                                    </td>
                                                    <td valign="top" width="20"> </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                            <table bgcolor="#1a1c21" border="0" cellpadding="0" cellspacing="0" width="700" align="center">
                                                <tbody>
                                                <tr>
                                                    <td bgcolor="#1a1c21" valign="top" width="660" align="center">
                                                        <div style="font-size:25px;line-height:25px"> </div>
                                                    </td>

                                                </tr>
                                                </tbody>
                                            </table>
                                            <table bgcolor="#1a1c21" border="0" cellpadding="0" cellspacing="0" width="700" align="center">
                                                <tbody>
                                                <tr>
                                                    <td valign="top" width="700">
                                                        <table bgcolor="#1a1c21" border="0" cellpadding="0" cellspacing="0" width="700" align="center">
                                                            <tbody>
                                                            <tr>
                                                                <td>
                                                                </td>
                                                            </tr>
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                            <table bgcolor="#1a1c21" border="0" cellpadding="0" cellspacing="0" width="700" align="center">
                                                <tbody>
                                                <tr>
                                                    <td bgcolor="#1a1c21" valign="top" width="660" align="center">
                                                        <div style="font-size:25px;line-height:25px"> </div>
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                            <table bgcolor="#1a1c21" border="0" cellpadding="0" cellspacing="0" width="700" align="center">
                                                <tbody>
                                                <tr>
                                                    <td bgcolor="#1a1c21" valign="top" width="620" align="center">
                                                        <table border="0" cellpadding="0" cellspacing="0" width="93%" align="center">
                                                            <tbody>
                                                            <tr>
                                                                <td height="1" align="center">
                                                                    <table border="0" cellpadding="0" cellspacing="0" width="660" align="center">
                                                                        <tbody>
                                                                        <tr>
                                                                            <td height="1" bgcolor="#646464" align="center">
                                                                                <div style="font-size:1px;line-height:1px">
                                                                                     </div>
                                                                            </td>
                                                                        </tr>
                                                                        </tbody>
                                                                    </table>
                                                                </td>
                                                            </tr>
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                            <table bgcolor="#1a1c21" border="0" cellpadding="0" cellspacing="0" width="700" align="center">
                                                <tbody>
                                                <tr>
                                                    <td bgcolor="#1a1c21" valign="top" width="660" align="center">
                                                        <div style="font-size:25px;line-height:25px"> </div>
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                            <table bgcolor="#1a1c21" border="0" cellpadding="0" cellspacing="0" width="700" align="center">
                                                <tbody>
                                                <tr>
                                                    <td valign="top" width="700">
                                                        <table bgcolor="#1a1c21" border="0" cellpadding="0" cellspacing="0" width="700" align="center">
                                                            <tbody>
                                                            <tr>
                                                                <td>
                                                                    <table bgcolor="#1a1c21" border="0" cellpadding="0" cellspacing="0" width="700">
                                                                        <tbody>
                                                                        <tr>
                                                                            <td style="font-family:Helvetica,arial,helv,sans-serif;font-size:10px;color:#949494;font-weight:bold;padding-left:20px;padding-right:20px" bgcolor="#1a1c21" align="center">
                                                                              <br><br>    
                                                                              {{ trans('emails.master.all_rights_reserved') }}                            
                                                                              <br>
                                                                                <br>
                                                                                <br>
                                                                                <br></td>
                                                                        </tr>
                                                                        </tbody>
                                                                    </table>
                                                                </td>
                                                            </tr>
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            </tbody>
        </table>
    </body>
</html>