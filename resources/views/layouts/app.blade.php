<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="/js/jquery-3.1.0.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Books</title>
    <link href="/css/app.css" rel="stylesheet">
    <link href="/css/main.css" rel="stylesheet">
    <link href="/css/form.css" rel="stylesheet">
    <script>
        window.Laravel = <?php echo json_encode([
            'csrfToken' => csrf_token(),
        ]); ?>
    </script>
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-default navbar-static-top">
            <div class="container">
                <div class="collapse navbar-collapse" id="app-navbar-collapse">
                    <div class="nav_parent">
                        <ul class="nav_home">
                            <li><a href="{{ url('/') }}">Home</a></li>
                            <li><a href="{{ url('/chat') }}">Chat</a></li>
                            <li><a href="{{ url('/home') }}">Comments</a></li>
                            <li><a href="{{ url('/lybrary') }}">Lybrary</a></li>
                            <li><a href="{{ url('/add_books') }}">Add books</a></li>
                            <li><a href="{{ url('/about') }}">About</a></li>
                        </ul>
                        <ul class="nav navbar-nav">
                            <!-- Authentication Links -->
                            @if (Auth::guest())
                                <li><a href="{{ url('/login') }}">Login</a></li>
                                <li><a href="{{ url('/register') }}">Register</a></li>
                            @else
                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                        {{ Auth::user()->name }} <span class="caret"></span>
                                    </a>
                                    <ul class="dropdown-menu" role="menu">
                                        <li>
                                            <a href="{{ url('/logout') }}"
                                               onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                                Logout
                                            </a>
                                            <form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
                                                {{ csrf_field() }}
                                            </form>
                                        </li>

                                    </ul>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </nav>
        @yield('content')
    </div>
    <!-- Scripts -->
    <script src="/js/app.js"></script>
</body>
</html>
