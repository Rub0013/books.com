$( document ).ready(function() {
    $('#add_comment').click(function(){
        var comment = $("#comment_area").val();
        if(comment.length>0)
        {
            $.ajax({
                type: 'post',
                url: '/comments',
                cache: false,
                data: {comment: comment},
                headers: {'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr('content')},
                success: function (answer) {
                        $("#comment_area").val('');
                    console.log(answer);
                    $('.show_comments').eq(0).append("<div class='show_comment'><p></p><div><b class='b_comment'>"+answer['comments']+"</b></div></div>");
                }
            });
        }
        else
            {
                alert('Add comment!');
            }
    });
    $('.change').click(function(){
        var change_button = $(this);
        $('#comment_id').val(change_button.prev().val());
        $('#reg_user_comment').val(change_button.parent().prev().html());
        $('#change_comment').css({'display':'flex'});
        change_button.parent().next().css({'display':'block'});
    });
    $('#cancel_change').click(function(){
        $('#change_comment').css({'display':'none'});
    });
    $('#confirm_change').click(function(){
        var reg_user_comment = $('#reg_user_comment').val();
        var comment_id = $('#comment_id').val();
        if(reg_user_comment.length>0)
        {
            $.ajax({
                type: 'post',
                url: '/change_comment',
                cache: false,
                data: {reg_user_comment:reg_user_comment,comment_id:comment_id},
                headers: {'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr('content')},
                success: function (answer) {
                    if(answer=='changed')
                    {
                        location.reload();
                    }
                }
            });
        }
        else
        {
            alert('Add comment');
        }
    });
});
