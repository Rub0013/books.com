<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Auth;
use App\User;
use App\Chat;
use App\Friend;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use PDF;
use Mail;
use Illuminate\Support\Facades\Redirect;
use App;

class ChatController extends MainController
{
    public function chat($local,$id = null)
    {
        $lang = new LanguageController();
        if($id)
        {
            $if_friend = Friend::where('request_to_id','=',Auth::user()->id)
                ->where('request_from_id','=',$id)
                ->orWhere('request_from_id','=',Auth::user()->id)
                ->where('request_to_id','=',$id)
                ->get();
            if(isset($if_friend[0]))
            {
                if($if_friend[0]['answer']==1)
                {
                    $conversation = Chat::select('*')
                        ->where('deleted_by','<>',Auth::user()->id)
                        ->where('sender_id','=',$id)
                        ->where('recipient_id','=',Auth::user()->id)
                        ->orWhere('sender_id','=',Auth::user()->id)
                        ->where('deleted_by','<>',Auth::user()->id)
                        ->where('deleted','=',0)
                        ->where('recipient_id','=',$id)
                        ->get();
                    if(count($conversation)>0)
                    {
                        $answer = $conversation;
                    }
                    else
                    {
                        $answer = 'You have no conversation!';
                    }
                }
            }
            else
            {
                return Redirect::route('error', array('local' => App::getLocale()));
            }
            $friends = DB::table('users')->select('users.id','name','answer','users.online')
            ->join('friends', function($join)
            {
                $join->on('users.id', '=', 'request_from_id')
                    ->where('request_to_id','=',Auth::user()->id)
                    ->where('answer','=',1)
                    ->orOn('users.id', '=', 'request_to_id')
                    ->Where('request_from_id','=',Auth::user()->id)
                    ->where('answer','=',1);
            })
            ->get();
            Chat::where('sender_id','=',$id)
                ->where('recipient_id','=',Auth::user()->id)
                ->where('seen','=',0)
                ->orWhere('seen','=',2)
                ->update(['seen' => 1]);
            $current_friend = User::select('id','name')->where('id','=',$id)->get();
            $array = array('answer'=>$answer,
                'friends'=>$friends,
                'total_notes_count' => $this->total_notes_count(),
                'total_requests' => $this->total_requests(),
                'current_friend'=>$current_friend);
            return view('current_chat',$array);
        }
        else
        {
            $friends = DB::table('users')->select('users.id','name','answer','users.online')
                ->join('friends', function($join)
                {
                    $join->on('users.id', '=', 'request_from_id')
                        ->Where('request_to_id','=',Auth::user()->id)
                        ->where('answer','=',1)
                        ->orOn('users.id', '=', 'request_to_id')
                        ->Where('request_from_id','=',Auth::user()->id)
                        ->where('answer','=',1);
                })
                ->get();
            $array = array('friends' => $friends,
                'total_notes_count' => $this->total_notes_count(),
                'total_requests' => $this->total_requests());
            return view('chat',$array);
        }
    }
    public function add_chat(Request $request)
    {
        if(!isset($request['new_image']) and isset($request['message']))
        {
            $answer = Chat::firstOrCreate([
                'sender_id' => Auth::user()->id,
                'recipient_id' => $request['to'],
                'message' => $request['message']
            ]);
        }
        if(isset($request['new_image']) and isset($request['message']))
        {
            $rules = [
                'new_image' => 'image',
            ];
            $this->validate($request, $rules);
            $filename = time().'.'.$request['new_image']->getClientOriginalExtension();
            Storage::disk('upload')->put($filename,File::get($request['new_image']));
            $answer = Chat::firstOrCreate([
                'sender_id' => Auth::user()->id,
                'recipient_id' => $request['to'],
                'message' => $request['message'],
                'image' => $filename
            ]);
        }
        if(isset($request['new_image']) and !isset($request['message']))
        {
            $rules = [
                'new_image' => 'image',
            ];
            $this->validate($request, $rules);
            $filename = time().'.'.$request['new_image']->getClientOriginalExtension();
            Storage::disk('upload')->put($filename,File::get($request['new_image']));
            $answer = Chat::firstOrCreate([
                'sender_id' => Auth::user()->id,
                'recipient_id' => $request['to'],
                'image' => $filename
            ]);
        }
        return $answer;
    }
    public function live_chat(Request $request)
    {
        $changed_messages = Chat::select('*')
            ->where('sender_id','=',$request['from'])
            ->where('recipient_id','=',Auth::user()->id)
            ->where('seen','=',2)
            ->get();
        if(count($changed_messages)>0)
        {
            $changed = $changed_messages;
            Chat::where('sender_id','=',$request['from'])
                ->where('recipient_id','=',Auth::user()->id)
                ->where('seen','=',2)
                ->update(['seen' => 1]);
        }
        else
        {
            $changed = 'No changed';
        }
        $conversation = Chat::select('*')
            ->where('sender_id','=',$request['from'])
            ->where('recipient_id','=',Auth::user()->id)
            ->where('seen','=',0)
            ->get();
        if(count($conversation)>0)
        {
                $answer = $conversation;
                Chat::where('sender_id','=',$request['from'])
                ->where('recipient_id','=',Auth::user()->id)
                ->where('seen','=',0)
                ->update(['seen' => 1]);
        }
        else
        {
            $answer = 'No message';
        }
        $total = array(
            'new_messages' => $answer,
            'changed_messages' => $changed);
        return $total;
    }
    public  function  notifications(Request $request)
    {
        $notes = DB::table('chats')->select('sender_id','recipient_id',DB::raw('count(*) as total'))
            ->where('recipient_id','=',$request['id'])
            ->where('seen','=',0)
            ->groupBy('sender_id')
            ->get();
        return $notes;
    }
    public  function  current_friends_notes(Request $request)
    {
        $notes = DB::table('users')->select(DB::raw('count(users.id) as message_count, users.name, users.id'))->groupBy('users.id')
            ->join('chats', function($join)
            {
                $join->on('users.id', '=', 'sender_id')
                    ->Where('recipient_id','=',Auth::user()->id)
                    ->where('seen','=',0);
            })
            ->get();
        return $notes;
    }
    public  function  delete_message(Request $request)
    {
        Chat::where('id','=',$request['chat_id'])
            ->update(['deleted' => 1]);
        return 1;
    }
    public function delete_conversation(Request $request)
    {
        Chat::where('deleted_by','=',0)
        ->where('sender_id','=',$request['friend'])
        ->where('recipient_id','=',Auth::user()->id)
        ->orWhere('sender_id','=',Auth::user()->id)
        ->where('recipient_id','=',$request['friend'])
        ->where('deleted_by','=',0)
        ->update(['deleted_by' => Auth::user()->id]);
        $images = Chat::select('image')->where('deleted_by','=',$request['friend'])
            ->where('sender_id','=',$request['friend'])
            ->where('recipient_id','=',Auth::user()->id)
            ->orWhere('sender_id','=',Auth::user()->id)
            ->where('recipient_id','=',$request['friend'])
            ->where('deleted_by','=',$request['friend'])
            ->get();
        if(count($images)>0)
        {
            for($i=0;$i<count($images);$i++)
            {
                Storage::disk('upload')->delete($images[$i]['image']);
            }
        }
        Chat::where('deleted_by','=',$request['friend'])
            ->where('sender_id','=',$request['friend'])
            ->where('recipient_id','=',Auth::user()->id)
            ->orWhere('sender_id','=',Auth::user()->id)
            ->where('recipient_id','=',$request['friend'])
            ->where('deleted_by','=',$request['friend'])
            ->delete();
        return 1;
    }
    public function  change_message(Request $request)
    {
        Chat::where('id','=',$request['message_id'])
            ->update(['seen' => 2,'message' => $request['changed_message']]);
        $changed_message = Chat::find($request['message_id']);
        return $changed_message;
    }
    public function mail_conversation(Request $request)
    {
        $conversation = Chat::select('message','sender_id','recipient_id','created_at')
            ->where('deleted_by','<>',Auth::user()->id)
            ->where('sender_id','=',$request['user'])
            ->where('recipient_id','=',Auth::user()->id)
            ->orWhere('sender_id','=',Auth::user()->id)
            ->where('deleted_by','<>',Auth::user()->id)
            ->where('deleted','=',0)
            ->where('recipient_id','=',$request['user'])
            ->get();
        $me = User::find(Auth::user()->id);
        $friend = User::find($request['user']);
        $whole = array(
            'me' => $me,
            'friend' => $friend,
            'conversation' => $conversation
        );
        $data = array('whole'=>$whole);
        $pdf = PDF::loadview('mail', $data);
        $pdf_chat = $pdf->download('mail');
        $user_mail=$request['mail'];
        $auth_mail = Auth::user()->email;

        Mail::send(['html' =>'mail'], $data, function($message) use($pdf_chat, $user_mail,$auth_mail){
            $message->from($auth_mail);
            $message->attachData($pdf_chat,'chat.pdf');
            $message->to($user_mail)->subject('Mail From My Profile');
        });
        return 1;
    }
}
