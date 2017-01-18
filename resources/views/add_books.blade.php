<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Add books</title>
    <link href="/css/addbooks.css" rel="stylesheet">
    <script src="/js/jquery-3.1.0.min.js"></script>
</head>
<body>
    <div class='issues_parent'>
        <div class="issues"></div>
    </div>
    <div class="main_div">
        @if(isset($messages))
        @foreach($messages as $message)
            $message;
            @endforeach
        @endif
        <div class="form">
            <label>
                <p>Name of the book</p>
                <input onkeyup="" id="book_name" value="">
            </label>
            <label>
                <p>Author</p>
                <input onkeyup="" id="author_name" value="">
            </label>
            <label>
                <p>Genre</p>
                <input onkeyup="" id="genre" value="">
            </label>
            <input id="hidden" type="text" value="{{ Auth::user()->id }}">
            <label>
                <p>Image</p>
                <input id='book_image' type="file" accept=".jpg,.png,.gif">
            </label>
            <input type="button" value="Add" id="add_button">
        </div>
    </div>
    <script>
        $( document ).ready(function() {
            $('#add_button').click(function() {
                var book_name = $("#book_name").val();
                var author_name = $("#author_name").val();
                var genre = $("#genre").val();
                var user_id = $("#hidden").val();
                var file = document.getElementById('book_image').files[0];
                var errors = [];
                if(book_name.length==0)
                {
                    errors.push('Add book_name!');
                }
                if(author_name.length==0)
                {
                    errors.push('Add author name!');
                }
                if(genre.length==0)
                {
                    errors.push('genre!');
                }
                if(file==undefined)
                {
                    errors.push('choose image!');
                }
                $('.issues').html(errors[0]);
                if(errors.length==0)
                {
                    var formData = new FormData();
                    formData.append('book_name',book_name);
                    formData.append('author_name',author_name);
                    formData.append('genre',genre);
                    formData.append('user_id',user_id);
                    formData.append('image',file);
                    $.ajax({
                        type:'post',
                        url:'/add_books',
                        cache: false,
                        ectype: 'multipart/form-data',
                        data:formData,
                        processData: false,
                        contentType: false,
                        headers: {'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr('content')},
                        success:function(answer)
                        {
                            if(answer=='added')
                            {
                                $('.issues').html('Book is added');
                                $('.issues').css({'background':'rgba(29, 206, 29, 0.7)','visibility':'visible','color':'rgb(255, 255, 255)'});
                                function l_href()
                                {
                                    location.assign('http://books.com/{{App::getLocale()}}/lybrary');
                                }
                                setTimeout(l_href,1500);
                            } if(answer=='notadded'){
                                $('.issues').html('You have no such permission');
                                $('.issues').css({'background':'red','visibility':'visible','color':'rgb(255, 255, 255)'});
                            }
                        }
                    });
                }
                else
                {
                    $('.issues').css({'visibility':'visible'});
                    $('.issues').html(errors[0]);
                }
            });
            $("#book_name").keyup(function (){ $('.issues').css({'visibility':'hidden'});});
            $("#author_name").keyup(function (){ $('.issues').css({'visibility':'hidden'});});
            $("#genre").keyup(function (){ $('.issues').css({'visibility':'hidden'});});
        });
    </script>
</body>
</html>
