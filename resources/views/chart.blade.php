@extends('layouts.parent')

@section('content')
    <?php
    var_dump(Session()->all());
    ?>
    <div class="container">
        <div class="row">
            <div class="col-xs-8 col-xs-offset-2">

                <div>

                        <div class="col-xs-3">
                            Name
                        </div>
                        <div class="col-xs-2">
                            Price
                        </div>
                        <div class="col-xs-2">
                            Quantity
                        </div>
                        <div class="col-xs-3">
                            Sum
                        </div>
                    <div class="col-xs-2">
                        del
                    </div>

                </div>


                    @foreach($products as $key => $prod)
                    <div class = "current" id = "{{$prod['id']}}">
                        <div class="col-xs-3">
                            {{$prod['name']}}
                        </div>
                        <div class="col-xs-2">
                            {{$prod['price']}}
                        </div>
                        <div class="col-xs-2">
                            {{$prod['quantity']}}
                        </div>
                            <div class="col-xs-3">
                                {{$prod['small_total']}}
                            </div>
                            <div class="col-xs-2">
                                <button class="btn btn-danger col-xs-3 delete_current">
                                    X
                                </button>
                            </div>
                    </div>
                    @endforeach

                </div>
        </div>
                <div class="container">
                    <div class="row">

                        <div class="col-xs-offset-6">
                            <a href="{{ url(App::getLocale().'/delete_all_from_chars') }}">
                                <button class="btn btn-danger col-xs-3 delete_all">
                                    Delete All
                                </button>
                            </a>

                                <button class="btn btn-info col-xs-3" id="finish_chart">
                                    Buy Totol: {{$big_total}}
                                </button>
                        </div>
                    </div>
                </div>

    </div>

    <div class="chart_pop_up">
        <div id ="charge-error" class="alert alert-danger {{!session()->has('error' ? 'hidden' : "")}}">
            {{session()->get('error')}}
            </div>
        <form action = '{{url('/en/finish_chart')}}' method = "post" id="chart_form">
            <div class="form-group">
                <label for="exampleInputPassword1">Cart Number</label>
                <input type="text" class="form-control" id="card-number" placeholder="0000000000" name="category">
            </div>
            <div class="form-group">
                <label for="exampleInputEmail1">Exp. Month</label>
                <input type="text" class="form-control" id="card-expiry-month" placeholder="1-12" name="movie_name">
            </div>
            <div class="form-group">
                <label for="exampleInputPassword1">Exp. Year</label>
                <input type="text" class="form-control" id="card-expiry-year" placeholder="2017/2018/2019" name="act_name">
            </div>
            <div class="form-group">
                <label for="exampleInputPassword1">CVC</label>
                <input type="text" class="form-control" id="card-cvc" placeholder="123" name="act_name">
            </div>
            {{csrf_field()}}
            <button type = "submit" id="extra_finish" class="btn btn-success">Finish Chart</button>
        </form>

        <button id="cancel_finish" class="btn btn-default">Cancel</button>
    </div>
    <script>
        $( document ).ready(function() {
            $(document).on("click",".delete_current",function() {
                var id = $(this).parents('.current').attr('id');
                $.ajax({
                    type:'post',
                    url:'/del_current_from_chart',
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    data:{
                        id:id,
                    },
                    success:function(data){
                        if(data == 1){
                            $('#'+id).fadeOut('slow', function(){
                                $('#'+id).remove();
                            })
                            if($('.current').length<=1){
                                window.location = "/en/lybrary";
                            }

                        }


                    }
                })
            });

            $(document).on("click","#finish_chart",function() {
                $('.chart_pop_up').fadeIn();

            });

            $(document).on("click","#cancel_finish",function() {
                $('.chart_pop_up').fadeOut();

            });

        });
    </script>
    <script type="text/javascript" src="https://js.stripe.com/v2/"></script>
    <script type="text/javascript" src="/js/checkout.js"></script>
@endsection