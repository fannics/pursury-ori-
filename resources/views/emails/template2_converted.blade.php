<!DOCTYPE html>
<html style="font-family: 'Helvetica Neue','Helvetica',Helvetica,Arial,sans-serif; font-size: 100%; line-height: 1.6em; margin: 0; padding: 0;">
<head>
    <meta name="viewport" content="width=device-width">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>{{ settings('app.app_title') . ' -' }} {{ trans('emails.template2_converted.notifications') }}</title>
</head>
<body bgcolor="#f6f6f6" style="font-family: 'Helvetica Neue','Helvetica',Helvetica,Arial,sans-serif; font-size: 100%; line-height: 1.6em; -webkit-font-smoothing: antialiased; height: 100%; -webkit-text-size-adjust: none; width: 100% !important; margin: 0; padding: 0;">

<table class="body-wrap" bgcolor="#f6f6f6" style="font-family: 'Helvetica Neue','Helvetica',Helvetica,Arial,sans-serif; font-size: 100%; line-height: 1.6em; width: 100%; margin: 0; padding: 20px;">
    <tr style="font-family: 'Helvetica Neue','Helvetica',Helvetica,Arial,sans-serif; font-size: 100%; line-height: 1.6em; margin: 0; padding: 0;">
        <td style="font-family: 'Helvetica Neue','Helvetica',Helvetica,Arial,sans-serif; font-size: 100%; line-height: 1.6em; margin: 0; padding: 0;"></td>
        <td class="container" bgcolor="#FFFFFF" style="font-family: 'Helvetica Neue','Helvetica',Helvetica,Arial,sans-serif; font-size: 100%; line-height: 1.6em; clear: both !important; display: block !important; max-width: 600px !important; margin: 0 auto; padding: 20px; border: 1px solid #f0f0f0;">

            <div class="content" style="font-family: 'Helvetica Neue','Helvetica',Helvetica,Arial,sans-serif; font-size: 100%; line-height: 1.6em; display: block; max-width: 600px; margin: 0 auto; padding: 0;">
                <table style="font-family: 'Helvetica Neue','Helvetica',Helvetica,Arial,sans-serif; font-size: 100%; line-height: 1.6em; width: 100%; margin: 0; padding: 0;">
                    <tr style="font-family: 'Helvetica Neue','Helvetica',Helvetica,Arial,sans-serif; font-size: 100%; line-height: 1.6em; margin: 0; padding: 0;">
                        <td style="font-family: 'Helvetica Neue','Helvetica',Helvetica,Arial,sans-serif; font-size: 100%; line-height: 1.6em; margin: 0; padding: 0;">
                            <table style="font-family: 'Helvetica Neue','Helvetica',Helvetica,Arial,sans-serif; font-size: 100%; line-height: 1.6em; width: 100%; margin: 0 0 20px; padding: 0;">
                                <tr style="font-family: 'Helvetica Neue','Helvetica',Helvetica,Arial,sans-serif; font-size: 100%; line-height: 1.6em; margin: 0; padding: 0;">
                                    <td align="center" style="font-family: 'Helvetica Neue','Helvetica',Helvetica,Arial,sans-serif; font-size: 100%; line-height: 1.6em; margin: 0; padding: 0;">
                                        <a href="{{ route('homepage') }}" title="{{ settings('app.app_title') }}" style="font-family: 'Helvetica Neue','Helvetica',Helvetica,Arial,sans-serif; font-size: 100%; line-height: 1.6em; color: #348eda; margin: 0; padding: 0;">
                                            <img height="50px" src="{{asset(settings('app.route_prefix').'/images/watermarksincom_resized.png')}}" alt="{{ settings('app.app_title') }}" style="font-family: 'Helvetica Neue','Helvetica',Helvetica,Arial,sans-serif; font-size: 100%; line-height: 1.6em; max-width: 600px; width: auto; margin: 0; padding: 0;">
                                        </a>
                                    </td>
                                </tr>
                            </table>
                            @yield('template_content')
                        </td>
                    </tr>
                </table>
            </div>

        </td>
        <td style="font-family: 'Helvetica Neue','Helvetica',Helvetica,Arial,sans-serif; font-size: 100%; line-height: 1.6em; margin: 0; padding: 0;"></td>
    </tr>
</table>


<table class="footer-wrap" style="font-family: 'Helvetica Neue','Helvetica',Helvetica,Arial,sans-serif; font-size: 100%; line-height: 1.6em; clear: both !important; width: 100%; margin: 0; padding: 0;">
    <tr style="font-family: 'Helvetica Neue','Helvetica',Helvetica,Arial,sans-serif; font-size: 100%; line-height: 1.6em; margin: 0; padding: 0;">
        <td style="font-family: 'Helvetica Neue','Helvetica',Helvetica,Arial,sans-serif; font-size: 100%; line-height: 1.6em; margin: 0; padding: 0;"></td>
        <td class="container" style="font-family: 'Helvetica Neue','Helvetica',Helvetica,Arial,sans-serif; font-size: 100%; line-height: 1.6em; clear: both !important; display: block !important; max-width: 600px !important; margin: 0 auto; padding: 0;">

            <div class="content" style="font-family: 'Helvetica Neue','Helvetica',Helvetica,Arial,sans-serif; font-size: 100%; line-height: 1.6em; display: block; max-width: 600px; margin: 0 auto; padding: 0;">
                <table style="font-family: 'Helvetica Neue','Helvetica',Helvetica,Arial,sans-serif; font-size: 100%; line-height: 1.6em; width: 100%; margin: 0; padding: 0;">
                    <tr style="font-family: 'Helvetica Neue','Helvetica',Helvetica,Arial,sans-serif; font-size: 100%; line-height: 1.6em; margin: 0; padding: 0;">
                        <td align="center" style="font-family: 'Helvetica Neue','Helvetica',Helvetica,Arial,sans-serif; font-size: 100%; line-height: 1.6em; margin: 0; padding: 0;">
                            <p style="font-family: 'Helvetica Neue','Helvetica',Helvetica,Arial,sans-serif; font-size: 12px; line-height: 1.6em; color: #666666; font-weight: normal; margin: 0 0 10px; padding: 0;">
                                {{ settings('app.app_title') . ' -' }}. Copyright 2016 ©
                            </p>
                        </td>
                    </tr>
                </table>
            </div>

        </td>
        <td style="font-family: 'Helvetica Neue','Helvetica',Helvetica,Arial,sans-serif; font-size: 100%; line-height: 1.6em; margin: 0; padding: 0;"></td>
    </tr>
</table>

</body>
</html>