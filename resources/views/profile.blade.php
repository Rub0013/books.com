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
    <div id="main_profile">
        <div id="div_update">
            <input value="{{$info['birth_date']}}" id="input_birth" type="date">
            <input value="{{$info['email']}}" id="input_email" type="mail">
            <div>
                <button id="cancel_info">Cancel</button>
                <button id="submit_info">Submit</button>
            </div>
        </div>
        <img>
        <div id="general_info">
            <h2>{{$info['name']}}</h2>
            <div id="email_info">
                <p>Email</p>
                @if($info['email'])
                    <b>{{$info['email']}}</b>
                @else
                    <b>No email added</b>
                @endif
            </div>
            <div id="birth_info">
                <p>Date of birth</p>
                @if($info['birth_date'])
                    <b>{{$info['birth_date']}}</b>
                @else
                    <b>No birth date added</b>
                @endif
            </div>
            <button id="update_profile">Update profile</button>
        </div>
    </div>
    <script>
        $(document).ready(function(){
            $('#update_profile').click(function () {
                 $('#div_update').fadeIn('slow');
            });
            $('#cancel_info').click(function () {
                $('#div_update').fadeOut('slow');
            });
            $('#submit_info').click(function () {
                var birth = $('#input_birth').val();
                var email = $('#input_email').val();
                $.ajax({
                    type: 'post',
                    url: '/update_info',
                    cache: false,
                    data: {birth: birth,email: email},
                    headers: {'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr('content')},
                    success: function (answer) {
                        $('#div_update').fadeOut('slow');
                        $('#email_info b').text(answer['email']);
                        $('#birth_info b').text(answer['birth_date']);
                    }
                });
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
        });
    </script>
@endsection