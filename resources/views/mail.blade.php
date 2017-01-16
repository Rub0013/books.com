@foreach($whole['conversation'] as $message)
    @if($message['message'])
        @if($whole['me']['id']==$message['sender_id'])
            <div style="text-align: end;width: 500px;">
                <h3>{{$whole['me']['name']}}</h3>
                <b>{{$message['message']}}</b>
                <p>{{$message['created_at']}}</p>
            </div>
        @else
            <div style="text-align: start;width: 500px;">
                <h3>{{$whole['friend']['name']}}</h3>
                <b>{{$message['message']}}</b>
                <p>{{$message['created_at']}}</p>
            </div>
        @endif
    @endif
@endforeach