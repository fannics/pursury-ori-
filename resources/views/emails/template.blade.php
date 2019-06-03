<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="initial-scale=1.0"/>
    <meta name="format-detection" content="telephone=no"/>
    <style>
        body {
            background-color: #ffffff;
            margin: 0px;
            font-size: 16px;;
        }
        table tr td{
            vertical-align: middle;
        }
        table.table-header, table.table-footer{
            width: 100%;
            background-color: #222222;
            height: 50px;
        }
        table.table-content{
            width: 100%;
            background-color: transparent;
        }
        table.table-header a{
            color: #fff;
            text-decoration: none;
            font-size: 20px;
        }
        table.table-footer span{
            color: #fff;
            font-size: 15px;
        }

        table.container{
            width: 580px;
            margin: 0 auto;
        }

        .text-center{
            text-align: center;
        }
        .text-left{
            text-align: left;
        }
        .table-content{
            margin-top: 80px;
            margin-bottom: 80px;
        }

        .social-icon{
            background: url('{{ \Config::get('app')['url'] }}images/social_for_mail.png') no-repeat 0 0 scroll transparent;
            height: 25px;
            display: inline-block;
            margin-left: 4px;
        }

        .social-facebook{
            width: 18px;
            background-position: 0 0;
        }
        .social-google-plus{
            width: 39px;
            background-position: -56px 0;
        }
        .social-twitter{
            width: 27px;
            background-position: -23px 0;
        }
    </style>
</head>
<body style="padding: 0 !important;">
    <table class="table-header" cellpadding="0" cellspacing="0">
        <tr>
            <td>
                <table class="container">
                    <tr>
                        <td class="text-left">
                            <a href="{{ route('homepage',array(), true) }}">{{ \Config::get('app')['app_title'] }}</a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <table class="table-content">
        <tr>
            <td>
                <table class="container">
                    <tr>
                        <td>
                            @yield('main_content')
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <table class="table-footer" cellpadding="0" cellspacing="0">
        <tr>
            <td>
                <table class="container">
                    <tr>
                        <td class="text-left">
                            <span>{{ settings('app.app_title') }}. {{ trans('emails.template.all_rights_reserved') }} &copy; <?php echo date('Y'); ?></span>
                        </td>
                        <td class="text-right" style="color: #fff;">
                            <a href="#" class="social-icon social-facebook"></a>
                            <a href="#" class="social-icon social-twitter"></a>
                            <a href="#" class="social-icon social-google-plus"></a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>