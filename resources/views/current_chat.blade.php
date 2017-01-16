@extends('layouts.parent')

@section('total_requests')
    @if(count($total_requests) > 0)
        <p id="count_requests">{{ count($total_requests) }}</p>
    @else
        <p id="count_requests" style="display: none;"></p>
    @endif
@endsection

@section('languages')
    <ul class="languages">
        <li id="eng"><a @if (App::getLocale() == 'en')
                        style="color:#216a94"
                        @endif href="{{url('en').'/'.explode('/',Request::path())[1].'/'.explode('/',Request::path())[2]}}">Eng</a></li>
        <li id="arm"><a @if (App::getLocale() == 'am')
                        style="color:#216a94"
                        @endif href="{{url('am').'/'.explode('/',Request::path())[1].'/'.explode('/',Request::path())[2]}}">Հայ</a></li>
    </ul>
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
        <div id="parent_update_message">
            <div id="update_message">
                <textarea id="changed_message"></textarea>
                <input type="text" id="message_id" value="">
                <div id="chage_buttons">
                    <button id="cancel_update">Cancel</button>
                    <button id="confirm_update">Confirm</button>
                </div>
            </div>
        </div>
        <div id="conversation">
            <div id="mail_conv">
                <input type="email" placeholder="Whom to send..." id="send_mail">
                <div>
                    <input value="Cancel" type="button" id="cancel_sending">
                    <button id="submit_sending">Send</button>
                </div>
            </div>
            <div id="conversation_info">
                <h4>{{ $current_friend[0]['name'] }}</h4>
                <button id="show_mail_div">Mail Conversation</button>
                <input id="current_friend_conversation" type="text" value="{{ $current_friend[0]['id'] }}">
                <button id="delete_current_friend_conversation">Delete conversation</button>
            </div>
            <div id="show_conversation">
                @if($answer == 'You have no conversation!')
                    <b id="no_conversation">{{$answer}}</b>
                @else
                    @for($i=0;$i<count($answer);$i++)
                        @if($answer[$i]['sender_id'] == $current_friend[0]['id'])
                            @if($answer[$i]['message'])
                            <div id='message_{{ $answer[$i]['id'] }}' class="message">
                                <b>{{ $current_friend[0]['name'] }}</b>
                                <div>
                                     <p>{{ $answer[$i]['message'] }}</p>
                                     <h6>{{ $answer[$i]['created_at'] }}</h6>
                                </div>
                            </div>
                            @endif
                            @if($answer[$i]['image'])
                                    <div id='image_{{ $answer[$i]['id'] }}' class="sended_image">
                                        <b>{{ $current_friend[0]['name'] }}</b>
                                        <div>
                                            <img src="{{  url('/') }}/images/sended_images/{{ $answer[$i]['image'] }}">
                                            <h6>{{ $answer[$i]['created_at'] }}</h6>
                                        </div>
                                    </div>
                            @endif
                        @else
                            @if($answer[$i]['message'])
                                <div id='message_{{ $answer[$i]['id'] }}' class="message">
                                <b>Me</b>
                                <div>
                                    <p>{{ $answer[$i]['message'] }}</p>
                                    <div>
                                        <div class="message_settings">
                                            <button class="delete_message">Delete</button>
                                            <input type="text" value="{{ $answer[$i]['id'] }}">
                                            <button class="update_message">Change</button>
                                        </div>
                                        <h6>{{ $answer[$i]['created_at'] }}</h6>
                                    </div>
                                </div>
                            </div>
                            @endif
                            @if($answer[$i]['image'])
                                <div id='image_{{ $answer[$i]['id'] }}' class="sended_image">
                                        <b>Me</b>
                                        <div>
                                            <img src="{{  url('/') }}/images/sended_images/{{ $answer[$i]['image'] }}">
                                            <div>
                                                <div class="image_settings">
                                                    <button class="delete_message">Delete</button>
                                                    <input type="text" value="{{ $answer[$i]['id'] }}">
                                                </div>
                                                <h6>{{ $answer[$i]['created_at'] }}</h6>
                                            </div>
                                        </div>
                                    </div>
                            @endif
                        @endif
                    @endfor
                @endif
            </div>
            <div id="send_message">
                <textarea id="message_area"></textarea>
                <input type="text" id="recipient" value="{{ $current_friend[0]['id'] }}">
                <div>
                    <div class="fileUpload btn btn-primary">
                        <span>Send image</span>
                        <input id="send_image" type="file" class="upload" />
                    </div>
                    <input value="Send" type="button" id="submit">
                </div>
            </div>
        </div>
        <div>
            <div id="friends">
                <h3 id='no_such_friends'>No such friends!</h3>
                @for($i=0;$i<count($friends);$i++)
                    <div id='friend_{{ $friends[$i]['id'] }}' class="friend">
                        <a href="{{ url(App::getLocale().'/chat').'/'.$friends[$i]['id'] }}">{{ $friends[$i]['name'] }}</a>
                        @if($friends[$i]['online']==0)
                            <div class="online" style="background:red"></div>
                        @else
                            <div class="online" style="background:green"></div>
                        @endif
                        <input class="fore_note" type="text" value="{{ $friends[$i]['id'] }}">
                        <p class="notifications"></p>
                    </div>
                @endfor
            </div>
            <input id="friend_list_search"  type="search" placeholder="Search in friend list...">
        </div>
    </div>
    <script>
        $( document ).ready(function() {
            $("#send_mail").keypress(function (e) {
                if (e.keyCode == 13) {
                    $('#submit_sending').trigger('click');
                }
            });
            function check_live() {
                var from = {{ $current_friend[0]['id'] }};
                $.ajax({
                    type: 'post',
                    url: '/check_live',
                    cache: false,
                    data: {from : from},
                    headers: {'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr('content')},
                    success: function (answer)
                    {
                        if(answer['new_messages']!='No message')
                        {
                            $("#no_conversation").hide();
                            for(var i=0;i<answer['new_messages'].length;i++)
                            {
                                if(answer['new_messages'][i]['message'])
                                {
                                    $('#show_conversation').append("<div class='message'>" +
                                            "<b>"+"{{ $current_friend[0]['name'] }}"+"</b>" +
                                            "<div>" +
                                            "<p>"+answer['new_messages'][i]['message']+"</p>" +
                                            "<h6>"+answer['new_messages'][i]['created_at']+"</h6>" +
                                            "</div>" +
                                            "</div>");
                                }
                                if(answer['new_messages'][i]['image'])
                                {
                                    $('#show_conversation').append( "<div id='image_"+answer['id']+"' class='sended_image'>" +
                                            "<b>"+"{{ $current_friend[0]['name'] }}"+"</b>" +
                                            "<div>" +
                                                "<img src='/images/sended_images/"+answer['new_messages'][i]['image']+"'>" +
                                                "<h6>"+answer['new_messages'][i]['created_at']+"</h6>" +
                                            "</div>" +
                                            "</div>");
                                }
                                $('#show_conversation').scrollTop($('#show_conversation').prop('scrollHeight'));
                            }
                        }
                        if(answer['changed_messages']!='No changed')
                        {
                            for(var i=0;i<answer['changed_messages'].length;i++)
                            {
                                $('#show_conversation').find('#message_'+answer['changed_messages'][i]['id']).find('div').find('p').html(answer['changed_messages'][i]['message']);
                            }
                        }
                    }
                });
            }
//            setInterval(check_live, 2000);
            $('#show_conversation').scrollTop($('#show_conversation').prop('scrollHeight'));
            $('#submit').click(function(){
                var message = $("#message_area").val();
                var new_image = $('#send_image')[0].files[0];
                var to = $('#recipient').val();
                if(new_image!=undefined || message.length>0)
                {
                    var formData = new FormData();
                    formData.append('to',to);
                    if(message.length>0)
                    {
                        formData.append('message',message);
                    }
                    if(new_image!=undefined)
                    {
                        formData.append('new_image',new_image);
                    }
                    $.ajax({
                        type: 'post',
                        url: '/send_message',
                        cache: false,
                        ectype: 'multipart/form-data',
                        data: formData,
                        processData: false,
                        contentType: false,
                        headers: {'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr('content')},
                        success: function (answer)
                        {
                            if(answer['message'])
                            {
                                $("#no_conversation").css({"display":"none"});
                                $("#message_area").val('');
                                $('#show_conversation').append(
                                        "<div id='message_"+answer['id']+"' class='message'>" +
                                        "<b>Me</b>" +
                                        "<div>" +
                                        "<p>"+answer['message']+"</p>" +
                                        "<div>" +
                                        "<div class='message_settings'>" +
                                        "<button class='delete_message'>Delete</button>" +
                                        "<input type='text' value='"+answer['id']+"'>" +
                                        "<button class='update_message'>Change</button>" +
                                        "</div>" +
                                        "<h6>"+answer['created_at']+"</h6>" +
                                        "</div>" +
                                        "</div>" +
                                        "</div>");
                                $('#show_conversation').scrollTop($('#show_conversation').prop('scrollHeight'));
                            }
                            if(answer['image'])
                            {
                                $("#no_conversation").css({"display":"none"});
                                $("#message_area").val('');
                                $('#show_conversation').append(
                                        "<div id='image_"+answer['id']+"' class='sended_image'>" +
                                        "<b>Me</b>" +
                                        "<div>" +
                                            "<img src='/images/sended_images/"+answer['image']+"'>" +
                                            "<div>" +
                                                "<div class='image_settings'>" +
                                                    "<button class='delete_message'>Delete</button>" +
                                                    "<input type='text' value='"+answer['id']+"'>" +
                                                "</div>" +
                                                "<h6>"+answer['created_at']+"</h6>" +
                                            "</div>" +
                                        "</div>" +
                                        "</div>");
                                $('#send_image').val('');
                                $('#show_conversation').scrollTop($('#show_conversation').prop('scrollHeight'));
                            }
                        }
                    });
                }
                else
                {
                    alert('Add a message or an image!');
                }
            });
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
                            if(answer[i]['online']==0)
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
//            setInterval(online, 3000);
            $(document).on( "click", ".delete_message", function() {
                var chat_id = $(this).next().val();
                $.ajax({
                    type: 'post',
                    url: '/delete_message',
                    cache: false,
                    data: {chat_id : chat_id},
                    headers: {'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr('content')},
                    success: function (answer)
                    {
                        $('#message_'+chat_id).remove();
                        $('#image_'+chat_id).remove();
                    }
                });
            });
            $(document).on( "click", "#delete_current_friend_conversation", function(){
                var button = $(this);
                var friend = button.prev().val();
                $.ajax({
                    type: 'post',
                    url: '/delete_conversation',
                    cache: false,
                    data: {friend : friend},
                    headers: {'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr('content')},
                    success: function (answer)
                    {
                        if(answer==1)
                        {
                            $('.message').remove();
                            $('.sended_image').remove();
                            $("#no_conversation").show();
                        }
                    }
                });
            });
            $(document).on( "click", ".update_message", function(){
                var chat_id = $(this).prev().val();
                var upt_mes = $(this);
                var message = upt_mes.parent().parent().prev().html();
                $('#changed_message').val(message);
                $('#message_id').val(chat_id);
                $('#parent_update_message').fadeIn('slow');
            });
            $(document).on( "click", "#cancel_update", function(){
                $('#parent_update_message').fadeOut('slow');
                $('#changed_message').val('');
                $('#message_id').val('');
            });
            $(document).on( "click", "#confirm_update", function(){
                var message_id = $('#message_id').val();
                var changed_message = $('#changed_message').val();
                $.ajax({
                    type: 'post',
                    url: '/change_message',
                    cache: false,
                    data: {changed_message : changed_message,message_id : message_id},
                    headers: {'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr('content')},
                    success: function (answer)
                    {
                        $('#show_conversation').find('#message_'+answer['id']).find('div').find('p').html(answer['message']);
                        $('#parent_update_message').fadeOut('slow');
                        $('#changed_message').val('');
                        $('#message_id').val('');
                    }
                });
            });
            $("#friend_list_search").keyup(function (){
                var friend = $("#friend_list_search").val();
                if(friend.length>0)
                {
                    $('.friend').hide();
                    $.ajax({
                        type: 'post',
                        url: '/search_in_friend_list',
                        cache: false,
                        data: {friend: friend},
                        headers: {'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr('content')},
                        success: function (answer) {
                            $( ".searched_friends" ).remove();
                            if(answer.length==0)
                            {
                                $('#no_such_friends').show();
                            }
                            else
                            {
                                $('#no_such_friends').hide();
                                for(var i=0;i<answer.length;i++)
                                {
                                     $('#friends').append("<div class='searched_friends'>" +
                                             "<a href='{{url(App::getLocale())}}/chat/"+answer[i]['id']+"'>"+answer[i]['name']+"</a>" +
                                     "</div>");
                                }
                            }
                        }
                    });

                }
                else
                {
                    $( ".searched_friends" ).remove();
                    $('.friend').show();
                    $('#no_such_friends').hide();
                }
            });
            $('#show_mail_div').click(function () {
                $('#mail_conv').show('slow');
            });
            $('#cancel_sending').click(function (){
                $('#send_mail').val('');
                $('#mail_conv').hide('slow');
            });
            $('#submit_sending').click(function () {
                var mail = $('#send_mail').val();
                var user = $('#current_friend_conversation').val();
                if(mail.length>0)
                {
                    $.ajax({
                        type: 'post',
                        url: '/mail_conversation',
                        cache: false,
                        data: {user : user, mail : mail},
                        headers: {'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr('content')},
                        success: function (answer)
                        {
                            if(answer==1)
                            {
                                $('#send_mail').val('');
                                $('#mail_conv').hide('slow');
                            }
                        }
                    });
                }
                else
                    {
                        alert('Add mail!');
                    }
            });
        });
    </script>
@endsection

