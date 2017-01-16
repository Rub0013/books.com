@extends('layouts.parent')

@section('total_requests')
    @if(count($total_requests) > 0)
        <p id="count_requests">{{ count($total_requests) }}</p>
    @else
        <p id="count_requests" style="display: none;"></p>
    @endif
@endsection

@section('total_notes')
    @if($total_notes_count > 0)
        <p id="count_notes">{{ $total_notes_count }}</p>
    @else
        <p id="count_notes" style="display: none;"></p>
    @endif
@endsection

@section('content')
<div class="main_friends">
    <form class="find_friend">
        <div id="search_friend">
            <input name="friend_name" id="friend_name" type="search" placeholder="Find a friend...">
            <button type="submit" name="submit_friend_search"></button>
        </div>
        <div id="friends_search_show"></div>
    </form>
    <div id="friend_requests">
        <h3>Friend requests</h3>
        @if(count($total_requests) > 0)
            @for($i=0;$i<count($total_requests);$i++)
                <div class="request">
                    <p>Friend request from <span>{{ $total_requests[$i]['name'] }}</span></p>
                    <div>
                        <button class="accept">Accept</button>
                        <input type="text" value="{{ $total_requests[$i]['request_id'] }}">
                        <button class="deny">Deny</button>
                    </div>
                </div>
            @endfor
        @endif
    </div>
    <div class="friends">
        <h3>Friends</h3>
        @if(count($friends)>0)
            @for($n=0;$n<count($friends);$n++)
                <div class='current_friend'>
                    <p>{{ $friends[$n]['name'] }}</p>
                    <div>
                        <button class="write_message"><a href="{{ url(App::getLocale().'/chat').'/'.$friends[$n]['id'] }}">Message</a></button>
                        <button class="delete_friend">Delete</button>
                        <input  type="text" value="{{ $friends[$n]['request_id'] }}">
                        <input type="text" value="{{ $friends[$n]['id'] }}">
                    </div>
                </div>
            @endfor
        @else
            <p class="no_friends">You have no friends!</p>
        @endif
    </div>
</div>
<script>
    $( document ).ready(function() {
        $("#searching").keypress(function (e) {
            if (e.keyCode == 13) {
                var book = $('#selected a');
                if(book.attr('href')!=undefined)
                {
                    window.location.href = book.attr('href');
                }
            }
        });
        $("#searching").keyup(function (e){
            var key = e.which;
            if(key != 40 && key != 38 && key != 13)
            {
                if($(this).val().length>0)
                {
                    var book_name = $(this).val();
                    $.ajax({
                        type: 'post',
                        url: '/search_book',
                        cache: false,
                        data: {book_name: book_name},
                        headers: {'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr('content')},
                        success: function (answer) {
                            console.log(1);
                            if(typeof(answer) == 'string')
                            {
                                $('#search_show').fadeIn("slow");
                                $('#search_show').empty();
                                $('#search_show').append('<ul>' +
                                        '<li>'+answer+'<li>' +
                                        '</ul>');
                            }
                            else
                            {
                                $('#search_show').html('');
                                $('#search_show').fadeIn("slow");
                                console.log(answer);
                                for(var i=0;i<answer.books.length;i++)
                                {
                                    $('#search_show').append('<ul id="search_ul">' +
                                            '</ul>');
                                    $('#search_ul').append("<li value='"+i+"'>" +
                                            "<a href='{{url(App::getLocale())}}/search_one/"+answer.books[i]['id']+"'>"+answer.books[i]['name']+"</a>" +
                                            "</li>");
                                }
                            }
                        }
                    });
                }
                if($(this).val().length==0)
                {
                    $('#search_show').html('');
                    $('#search_show').slideUp("slow")
                }
            }
            else
            {
                var $listItems = $('#search_ul>li');
                var key = e.which,
                        $selected = $listItems.filter('#selected'),
                        $current;
                if ( key != 40 && key != 38 ) return;
                $listItems.removeAttr('id');
                if ( key == 40 ) // Down key
                {
                    if ( ! $selected.length || $selected.is(':last-child') ) {
                        $current = $listItems.eq(0);
                    }
                    else {
                        $current = $selected.next();
                    }
                }
                else if ( key == 38 ) // Up key
                {
                    if ( ! $selected.length || $selected.is(':first-child') ) {
                        $current = $listItems.last();
                    }
                    else {
                        $current = $selected.prev();
                    }
                }
                $current.attr('id', 'selected');
            }
        });
        $(".accept").click(function () {
            var this_accept = $(this);
            var request_id = $(this).next().val();
            $.ajax({
                type: 'post',
                url: '/accept_request',
                cache: false,
                data: {request_id: request_id},
                headers: {'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr('content')},
                success: function (answer) {
                    this_accept.parent().parent().css({'display':'none'});
                    $('.no_friends').css({'display':'none'})
                    $('.friends').append(
                            "<div class='current_friend'>" +
                                "<p>" +answer[0]['name']+ "</p>" +
                                "<div>" +
                                    "<button class='write_message'>" +
                                        "<a href='{{ url(App::getLocale().'/chat') }}/"+answer[0]['id']+"'>Message</a>" +
                                    "</button>" +
                                    "<button class='delete_friend'>Delete</button>" +
                                    "<input type='text' value='"+request_id+"'>" +
                                    "<input type='text' value='"+answer[0]['id']+"'>" +
                                "</div>" +
                            "</div>");
                }
            });
        });
        $("#friend_name").keyup(function (){
            if($(this).val().length>0)
            {
                var friend_name = $(this).val();
                $.ajax({
                    type: 'post',
                    url: '/find_friend',
                    cache: false,
                    data: {friend_name: friend_name},
                    headers: {'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr('content')},
                    success: function (answer) {
                        console.log(answer);
                        if(typeof(answer) == 'string')
                        {
                            $('#friends_search_show').fadeIn("slow");
                            $('#friends_search_show').empty();
                            $('#friends_search_show').append("<b class='found_no_friend'>"+answer+"</b>");
                        }
                        else
                        {
                            $('#friends_search_show').html('');
                            $('#friends_search_show').fadeIn("slow");
                            for(var i=0;i<answer.length;i++)
                            {
                                $('#friends_search_show').append("<div class='current_finded_friend'>" +
                                        "<a href='' class='current_finded_friend_name'>"+answer[i]['name']+"</a>" +
                                        "<div id='finded_friend_"+answer[i]['id']+"'></div>" +
                                        "</div>");
                                if(answer[i]['request_from_id'] != {{ Auth::user()->id }} && answer[i]['request_to_id'] != {{ Auth::user()->id }})
                                {
                                    $("#finded_friend_"+answer[i]['id']).append("<b class='sended_js'>Sended</b>" + "<b class='request_button'>Send request</b>" +
                                            "<input type='text' value='"+answer[i]['id']+"'>");
                                }
                                if((answer[i]['request_from_id'] == {{ Auth::user()->id }} || answer[i]['request_to_id'] == {{ Auth::user()->id }}) && answer[i]['answer']==1)
                                {
                                    $("#finded_friend_"+answer[i]['id']).append("<b class='remove_button'>Remove</b>" +
                                            "<input type='text' value='"+answer[i]['request_id']+"'>" +
                                            "<input type='text' value='"+answer[i]['id']+"'>");
                                }
                                if(answer[i]['request_from_id'] == {{ Auth::user()->id }} && answer[i]['answer']==0)
                                {
                                    $("#finded_friend_"+answer[i]['id']).append("<b class='request_sended'>Sended</b>");
                                }
                            }
                        }
                    }
                });
            }
            if($(this).val().length==0)
            {
                $('#friends_search_show').slideUp('slow');
                $('#friends_search_show').html('');
            }
        });
        $(document).on( "click", ".request_button", function(){
            var friend_id = $(this).next().val();
                    var accept = $(this);
                    $.ajax({
                        type: 'post',
                        url: '/send_request',
                        data: {friend_id: friend_id},
                        headers: {'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr('content')},
                        success: function (answer) {
                            accept.fadeOut(1);
                            accept.prev().fadeIn("slow");
                }
            });
        });
        $(document).on( "click", ".remove_button", function(){
            var request_id = $(this).next().val();
            var user_id = $(this).next().next().val();
            var check = 1;
            var remuve = $(this);
            $.ajax({
                type: 'post',
                url: '/remuve_friend',
                cache: false,
                data: {request_id:request_id,user_id:user_id,check:check},
                headers: {'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr('content')},
                success: function (answer) {
                    remuve.parent().html("<b class='sended_js'>Sended</b>" +
                            "<b class='request_button'>Send request</b>" +
                            "<input type='text' value='"+answer['id']+"'>");
                }
            });
        });
        $(document).on( "click", ".deny", function(){
            var deny = $(this);
            var request = $(this).prev().val();
            $.ajax({
                type: 'post',
                url: '/deny_request',
                cache: false,
                data: {request: request},
                headers: {'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr('content')},
                success: function (answer) {
                    deny.parent().parent().css({'display':'none'});
                    console.log(answer);
                }
            });
        });
        $(document).on( "click", ".delete_friend", function(){
            var request_id = $(this).next().val();
            var user_id = $(this).next().next().val();
            var remuve = $(this);
            remuve.parent().parent().css({'display':'none'});
            $.ajax({
                type: 'post',
                url: '/remuve_friend',
                cache: false,
                data: {request_id:request_id,user_id:user_id},
                headers: {'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr('content')},
                success: function (answer) {
                    remuve.parent().parent().css({'display':'none'});
                }
            });
        });
    });
</script>
@endsection
