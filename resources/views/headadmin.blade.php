@extends('layouts.parent')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-xs-8 col-xs-offset-2">
                <h3>
                    <a href="{{ url(App::getLocale().'/headadmin/all_user_roles') }}">All (User-Role)'s</a>
                </h3>
            </div>
        </div>
    </div>

@endsection