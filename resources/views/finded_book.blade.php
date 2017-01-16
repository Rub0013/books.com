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
    <div class="main_books">
        @if(Auth::check())
            <div id="parent_book_update">
                <div id="book_update">
                    <label>
                        <p>Name of the book</p>
                        <input onkeyup="" id="book_name_update" value="">
                    </label>
                    <label>
                        <p>Author</p>
                        <input onkeyup="" id="author_name_update" value="">
                    </label>
                    <label>
                        <p>Genre</p>
                        <input onkeyup="" id="genre_update" value="">
                    </label>
                    <input id="hidden_update" type="text" value="{{ Auth::user()->id }}">
                    <label>
                        <p>Image</p>
                        <input id='book_image_update' type="file" accept=".jpg,.png,.gif">
                    </label>
                    <input type="text" id="image_name">
                    <input type="text" id="updating_books_id">
                    <div id="post_update_buttons">
                        <input type="button" value="Cancel" id="cancel_update_button">
                        <input type="button" value="Update" id="update_button">
                    </div>
                </div>
            </div>
        @endif
        <div class="show_books">
            @for($i=0;$i<count($books);$i++)
                <div id="current_book_{{ $books[$i]['id'] }}">
                    <img  width='200' height='300' src="{{  url('/') }}/images/add_books/books_images/{{ $books[$i]['image'] }}">
                    <div>
                        <b class="book_name">{{ $books[$i]['name'] }}</b>
                        <p class="book_author">{{ $books[$i]['author'] }}</p>
                        <p class="book_genre">{{ $books[$i]['genre'] }}</p>
                        <div class="likes">
                            <b class="likes_count">{{ $books[$i]['likes'] }}</b>
                            @if(Auth::check())
                                @if($books[$i]['liked_user_id'] == Auth::user()->id)
                                    <input class="button" type="button" value="Unlike">
                                @else
                                    <input class="button" type="button" value="Like">
                                @endif
                                <input class="books_id" type="text" value="{{ $books[$i]['id'] }}">
                                <input class="users_id" type="text" value="{{Auth::user()->id}}">
                            @endif
                        </div>
                        @if(Auth::check())
                            @if( Auth::user()->id==$books[$i]['user_id'])
                                <div class="delete_update">
                                    <a class="delete_post" href="{{ url('/delete_book').'/'.$books[$i]['id'].'/'.$books[$i]['image'] }}">Delete</a>
                                    <input id="hidden_image_{{ $books[$i]['id'] }}" type="text" value="{{ $books[$i]['image'] }}">
                                    <div class="update_post">Update</div>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            @endfor
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
            $(document).on( "click", "div.likes > .button", function() {
                var id_user=$('.users_id').val();
                var id_book=$(this).next().val();
                var like_button = $(this);
                $.ajax({
                    type:'post',
                    url:'/like_unlike',
                    cache: false,
                    data:{id_book:id_book,id_user:id_user},
                    headers: {'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr('content')},
                    success:function(answer)
                    {
                        like_button.prev(".likes_count").html(answer);
                    }
                });
                if($(this).val()=="Like")
                {
                    $(this).val("Unlike");
                }
                else
                {
                    $(this).val("Like");
                }
            });
            $(document).on( "click", ".update_post", function() {
                var this_button = $(this);
                var book_name = this_button.parent().parent().find('.book_name').html();
                var book_author = this_button.parent().parent().find('.book_author').html();
                var book_genre = this_button.parent().parent().find('.book_genre').html();
                var book_id = this_button.parent().parent().find('.likes').find('.books_id').val();
                var image_name = this_button.prev().val();
                $('#book_name_update').val(book_name);
                $('#author_name_update').val(book_author);
                $('#genre_update').val(book_genre);
                $('#updating_books_id').val(book_id);
                $('#image_name').val(image_name);
                $('#parent_book_update').fadeIn('slow');
            });
            $(document).on( "click", "#update_button", function() {
                var new_name = $('#book_name_update').val();
                var new_author =  $('#author_name_update').val();
                var new_genre =  $('#genre_update').val();
                var book_id = $('#updating_books_id').val();
                var new_image = $('#book_image_update')[0].files[0];
                var old_image =  $('#image_name').val();
                var errors = [];
                if(new_name.length==0)
                {
                    errors.push('Add book_name!');
                }
                if(new_author.length==0)
                {
                    errors.push('Add author name!');
                }
                if(new_genre.length==0)
                {
                    errors.push('genre!');
                }
                if(errors.length==0)
                {
                    var formData = new FormData();
                    formData.append('new_name',new_name);
                    formData.append('new_author',new_author);
                    formData.append('new_genre',new_genre);
                    formData.append('book_id',book_id);
                    formData.append('old_image',old_image);
                    if(new_image!=undefined)
                    {
                        formData.append('new_image',new_image);
                    }
                    $.ajax({
                        type:'post',
                        url:'/update_book',
                        cache: false,
                        ectype: 'multipart/form-data',
                        data: formData,
                        processData: false,
                        contentType: false,
                        headers: {'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr('content')},
                        success:function(answer)
                        {
                            $("#current_book_"+answer['id']).find('img').attr("src","/images/add_books/books_images/"+answer['image']);
                            $("#current_book_"+answer['id']).find('div').find('.book_name').html(answer['name']);
                            $("#current_book_"+answer['id']).find('div').find('.book_author').html(answer['author']);
                            $("#current_book_"+answer['id']).find('div').find('.book_genre').html(answer['genre']);
                            $("#current_book_"+answer['id']).find('div').find('.delete_update').find('.delete_post').attr("href","{{ url('/delete_book').'/'}}"+answer['id']+"/"+answer['image']);
                            $("#hidden_image_"+answer['id']).attr('value', answer['image']);
                            $('#parent_book_update').fadeOut(300);
                        }
                    });
                }
                else
                {
                    alert(errors[0]);
                }
            });
            $(document).on( "click", "#cancel_update_button", function() {
                $('#parent_book_update').fadeOut(300);
            });
        });
    </script>
@endsection