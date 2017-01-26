@extends('layouts.parent')

@section('content')

<?php
            //dd($roles);
            ?>
    <link href="https://cdn.datatables.net/1.10.13/css/dataTables.bootstrap.min.css" rel="stylesheet">
    <div class="col-xs-2 pop_up_success_role">

    </div>
    <div class="container">
        <div class="row">

            <div class="col-xs-8 col-md-offset-2">
                <table id="example" class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead>
                    <tr>
                        <th>User_id</th>
                        <th>User_name</th>
                        @foreach($roles as $role)
                            <th>{{$role->name}}</th>
                            @endforeach

                        <th>Save</th>
                        <th>Login AS</th>
                    </tr>
                    </thead>
                    <tfoot>
                    <tr>
                        <th>User_id</th>
                        <th>User_name</th>
                        @foreach($roles as $role)
                            <th>{{$role->name}}</th>
                        @endforeach
                        <th>Save</th>
                        <th>Login AS</th>

                    </tr>
                    </tfoot>
                    <tbody>
                        @foreach($users as $user)
                            <tr id="{{$user->id}}" class="parent_tr">
                            <td>{{$user->id}}</td>
                            <td>{{$user->name}}</td>

                                @if($user->roles_name == "HeadAdmin")
                                    <td><input type="radio" data-type="headadmin" name="role_{{$user->id}}" checked></td>
                                    @else
                                    <td class="cant">can't</td>
                                @endif

                            <td><input type="radio" data-type="admin" name="role_{{$user->id}}"
                                       @if($user->roles_name == "Admin")
                                       checked
                                        @endif></td>
                            <td><input type="radio" data-type="blocked" name="role_{{$user->id}}"
                                       @if($user->roles_name == "Blocked")
                                       checked
                                        @endif></td>
                            <td><input type="radio" data-type="user" name="role_{{$user->id}}"
                                       @if(is_null($user->roles_name))
                                       checked
                                        @endif></td>
                                <td><button class="btn btn-danger save_role">Save</button></td>
                                <td><button class="btn btn-info login_as_user">as {{$user->name}}</button></td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script>
        $( document ).ready(function() {
            $(document).ready(function() {
                $('#example').DataTable();
            } );


            $(".save_role").on("click",function() {
                var id = $(this).parents('.parent_tr').attr('id');
                var role = $(this).parents('.parent_tr').find('input:checked').data('type')

               $.ajax({
                    type:'post',
                    url:location.pathname,
                    data:{
                        id:id,
                        role:role,
                    },
                    dataType: "json",
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },

                    success:function(data){
                        if(data == 0){
                            $('.pop_up_success_role').css( "background", "red" );
                            $('.pop_up_success_role').fadeIn('slow', function(){
                                $('.pop_up_success_role').html("Something was wrong");

                            }).delay('1000').fadeOut();
                        }else if(data == 1){
                            $('.pop_up_success_role').css( "background", "green" );
                            $('.pop_up_success_role').fadeIn('slow', function(){
                                $('.pop_up_success_role').html("The Role Changed");

                            }).delay('1000').fadeOut();
                        }
                    }
                })
            });

            $(".login_as_user").on("click",function() {
                var id = $(this).parents('.parent_tr').attr('id');
                $.ajax({
                    type:'post',
                    url:'/login_as',
                    data:{
                        id:id,
                    },
                    dataType: "json",
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },

                    success:function(data){
                        if(data==1){
                            location.replace("/en/profile");
                        }
                    }
                })

            });

        });
    </script>
@endsection