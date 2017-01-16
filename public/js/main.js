$( document ).ready(function(){
    function check_notes_main()
    {
        var data1 = true;
        $.ajax({
            type: 'post',
            url: '/all_notes',
            cache: false,
            data: {data1 : data1},
            headers: {'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr('content')},
            success: function (answer)
            {
                if(answer['total_messages']>0)
                {
                    $('#count_notes').empty();
                    $('#count_notes').append(answer['total_messages']);
                    $('#count_notes').fadeIn();
                }
                else
                {
                    $('#count_notes').fadeOut();
                }
                if(answer['total_requests'].length!=0)
                {
                    $('#count_requests').empty();
                    $('#count_requests').append(answer['total_requests'].length);
                    $('#count_requests').fadeIn();
                }
                else
                {
                    $('#count_requests').fadeOut();
                }
            }
        });
    }
    setInterval(check_notes_main, 3000);
});