$(document).ready(function(){
    $("#searching").keyup(function (){
        if($(this).val().length>=3)
        {
            var book_name = $(this).val();
            $('#search_show').fadeIn("slow");
            $.ajax({
                type: 'post',
                url: '/search_book',
                cache: false,
                data: {book_name: book_name},
                headers: {'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr('content')},
                success: function (answer) {
                    console.log(answer);
                    $('#search_show').empty();
                    if(typeof(answer) == 'string')
                    {
                        $('#search_show').append('<b>'+answer+'</b>');
                        $('.show_books').eq(0).empty();
                    }
                    else
                        {
                            $('.show_books').eq(0).empty();
                            for(var i=0;i<answer.books.length;i++)
                            {
                                $('#search_show').append("<a href='http://books.com/choose_one/"+answer.books[i]['id']+"'>"+answer.books[i]['name']+'</a>');
                            }
                            for(var i=0;i<answer.books.length;i++)
                            {
                                $('.show_books').eq(0).append("<div>" +
                                    "<img  width='200' height='300' src='http://books.com/images/add_books/books_images/"+answer.books[i]['image']+"'>" +"<div>" +
                                   "<b>"+answer.books[i]['name']+"</b><p>"+answer.books[i]['author']+"</p><p>"+answer.books[i]['genre']+"</p>" + "<div class='likes'>" +
                                    "<b class='likes_count'>"+answer.books[i]['likes']+"</b>" +
                                    "</div>" +
                                     "</div>" +
                                    "</div>");
                            }
                        }
                }
            });
        }
        else
            {
                $('#search_show').html('');
                $('#search_show').slideUp("slow")
            }
    });
    $('.button').click(function() {
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
});
