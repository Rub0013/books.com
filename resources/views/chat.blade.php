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
    <div class="main_chat">
        <div id="conversation">
            <h1>Choose friend!</h1>
        </div>
        <div id="friends">
            @foreach($friends as $friend)
                <div id='friend_{{ $friend['id'] }}' class="friend">
                    <a href="{{ url(App::getLocale().'/chat').'/'.$friend['id'] }}">{{ $friend['name'] }}</a>
                    @if($friend->isOnline())
                        <div class="online" style="background:green"></div>
                    @else
                        <div class="online" style="background:red"></div>
                    @endif
                    <input class="fore_note" type="text" value="{{ $friend['id'] }}">
                    <p class="notifications"></p>
                </div>
            @endforeach
        </div>
    </div>
    <script>
        $(document).ready(function(){
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
            function current_friend_notes() {
                var data1 = true;
                $.ajax({
                    type: 'post',
                    url: '/current_friends_notes',
                    cache: false,
                    data: {data1 : data1},
                    headers: {'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr('content')},
                    success: function (answer)
                    {
                        if(answer.length>0)
                        {
                            for(var i=0;i<answer.length;i++)
                            {
                                if(answer[i]['message_count']>0)
                                {
                                    $('#friends').find('#friend_'+answer[i]['id']).find('.notifications').html(answer[i]['message_count']);
                                    $('#friends').find('#friend_'+answer[i]['id']).find('.notifications').show('slow');
                                }
                                else
                                    {
                                        $('#friends').find('#friend_'+answer[i]['id']).find('.notifications').hide('slow');
                                    }
                            }
                        }
                        else
                        {
                            $('.notifications').hide('slow');
                        }
                    }
                });
            }
//            setInterval(current_friend_notes, 2000);
            function online(){
                var data1 = true;
                $.ajax({
                    type: 'post',
                    url: '/online',
                    cache: false,
                    data: {data1 : data1},
                    headers: {'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr('content')},
                    success: function (answer)
                    {
                        for(var i=0;i<answer.length;i++)
                        {
                            if(answer[i]['online']===false)
                            {
                                $('#friends').find('#friend_'+answer[i]['id']).find('.online').css({'background':'red'});
                            }
                            else
                            {
                                $('#friends').find('#friend_'+answer[i]['id']).find('.online').css({'background':'green'});
                            }
                        }
                    }
                });
            }
            setInterval(online, 3000);
        });
    </script>
@endsection
