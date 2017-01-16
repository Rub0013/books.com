<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Laravel</title>
        <style>
            *{
                background-color: #fff;
                font-family: sans-serif;
                padding: 0;
                margin: 0;
                box-sizing: border-box;
            }
            .flex-center {
                  display: flex;
                  justify-content: space-between;
                  margin: 25px;
              }
            ul
            {
                display: flex;
                list-style-type: none;
            }
            .links>li
            {
                margin-left: 20px;
            }
            .links
            {
                font-weight: bold;
            }
            .languages>li
            {
                margin-right: 20px;
                font-weight: bold;
            }
            li a
            {
                text-decoration: none;
                color: black;
            }
            .content {
                text-align: center;
            }
            .title {
                font-size: 84px;
            }
            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 12px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }
            .m-b-md {
                margin-bottom: 30px;
            }
        </style>
    </head>
    <body>
        <div class="flex-center position-ref full-height">
            <ul class="languages">
                <li id="eng"><a @if (App::getLocale() == 'en')
                                style="color:#216a94"
                                @endif href="{{ url('/en')}}">Eng</a></li>
                <li id="arm"><a @if (App::getLocale() == 'am')
                                style="color:#216a94"
                                @endif href="{{ url('/am')}}">Հայ</a></li>
            </ul>
                <ul class="links">
                    @if(App::getLocale()!=null)
                        <li><a href="{{ url(App::getLocale().'/login') }}">{{ trans('nav_bar.login') }}</a></li>
                        <li><a href="{{ url(App::getLocale().'/register') }}">{{ trans('nav_bar.register') }}</a></li>
                     @else
                        <li><a href="{{ url('en/login') }}">{{ trans('nav_bar.login') }}</a></li>
                        <li><a href="{{ url('en/register') }}">{{ trans('nav_bar.register') }}</a></li>
                    @endif
                </ul>
        </div>
        <div class="content">
            <div class="title m-b-md">
                {{ trans('nav_bar.hello') }}
            </div>
            <div class="links">
                @if(App::getLocale()!=null)
                    <a href="{{ url(App::getLocale().'/lybrary') }}">{{ trans('nav_bar.lybrary') }}</a>
                    <a href="{{ url(App::getLocale().'/chat') }}">{{ trans('nav_bar.chat') }}</a>
                    <a href="{{ url(App::getLocale().'/about') }}">{{ trans('nav_bar.about') }}</a>
                @else
                    <a href="{{ url('en/lybrary') }}">{{ trans('nav_bar.lybrary') }}</a>
                    <a href="{{ url('en/chat') }}">{{ trans('nav_bar.chat') }}</a>
                    <a href="{{ url('en/about') }}">{{ trans('nav_bar.about') }}</a>
                @endif
            </div>
        </div>
    </body>
</html>
