<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="/js/jquery-3.1.0.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Books</title>
    <link href="/css/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <link href="/fullcalendar/fullcalendar.css" rel="stylesheet">
    <link href="/css/jquery-ui.css" rel="stylesheet">
    <link href="/css/app.css" rel="stylesheet">
    <link href="/css/main.css" rel="stylesheet">
    <link href="/css/friends.css" rel="stylesheet">
    <link href="/css/lybrary.css" rel="stylesheet">
    <link href="/css/main_chat.css" rel="stylesheet">
    <link href="/css/current_chat.css" rel="stylesheet">
    <script>
        window.Laravel = <?php echo json_encode([
                'csrfToken' => csrf_token(),
        ]); ?>
    </script>
</head>
<body>
    <div class="body">
       @section('navbar')
       <nav>
           <div class="nav_parent">
               @section('languages')
                   <ul class="languages">
                       <li id="eng"><a @if (App::getLocale() == 'en')
                                       style="color:#216a94"
                                       @endif href="{{url('en').'/'.explode('/',Request::path())[1]}}">Eng</a></li>
                       <li id="arm"><a @if (App::getLocale() == 'am')
                                       style="color:#216a94"
                                       @endif href="{{url('am').'/'.explode('/',Request::path())[1]}}">Հայ</a></li>
                   </ul>
               @show
               <ul class="nav_home">
                   <li><a href="{{ url(App::getLocale()) }}">{{ trans('nav_bar.home') }}</a></li>
                   @if(Auth::check())
                       <li><a href="{{ url(App::getLocale().'/profile') }}">{{ trans('nav_bar.profile') }}</a></li>
                   @endif
                   <li id="main_note">
                       <a href="{{ url(App::getLocale().'/chat') }}">{{ trans('nav_bar.chat') }}</a>
                       @section('total_notes')
                       <p id="count_notes"></p>
                       @show
                   </li>
                   <li id="main_request">
                       <a href="{{ url(App::getLocale().'/friends') }}">{{ trans('nav_bar.friends') }}</a>
                       @section('total_requests')
                           <p id="count_requests"></p>
                       @show
                   </li>
                   <li><a href="{{ url(App::getLocale().'/calendar') }}">{{ trans('nav_bar.calendar') }}</a></li>
                   <li><a href="{{ url(App::getLocale().'/lybrary') }}">{{ trans('nav_bar.lybrary') }}</a></li>
                   <li><a href="{{ url(App::getLocale().'/add_books') }}">{{ trans('nav_bar.add_books') }}</a></li>
                   <li><a href="{{ url(App::getLocale().'/about') }}">{{ trans('nav_bar.about') }}</a></li>
                   <li id="parent_search">
                       <div id="search_book">
                           <input name="book_name" id="searching"  placeholder="{{ trans('nav_bar.search') }}">
                       </div>
                       <div id="search_show"></div>
                   </li>
               </ul>
               <ul class="nav navbar-nav">
                   <!-- Authentication Links -->
                   @if (Auth::guest())
                       <li><a href="{{ url(App::getLocale().'/login') }}">{{ trans('nav_bar.login') }}</a></li>
                       <li><a href="{{ url(App::getLocale().'/register') }}">{{ trans('nav_bar.register') }}</a></li>
                   @else
                       <li class="dropdown">
                           <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                               {{ Auth::user()->name }} <span class="caret"></span>
                           </a>
                           <ul class="dropdown-menu" role="menu">
                               <li>
                                   <a href="{{ url(App::getLocale().'/logout') }}"
                                      onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                       {{ trans('nav_bar.logout') }}
                                   </a>
                                   <form id="logout-form" action="{{ url(App::getLocale().'/logout') }}" method="POST" style="display: none;">
                                       {{ csrf_field() }}
                                   </form>
                               </li>
                           </ul>
                       </li>
                   @endif
               </ul>
           </div>
       </nav>
       @show
       @section('content')
       @show
    </div>
<!-- Scripts -->
    <script src="/js/app.js"></script>
    @if(Auth::check())
        <script src="/js/main.js"></script>
        <script src="/fullcalendar/lib/moment.min.js"></script>
        <script src="/fullcalendar/fullcalendar.js"></script>
    @endif
</body>
</html>
