<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;
use App\Chat;
 class SmsBirthday extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sms:birth';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $birth = date('-m-d');
        $friends = User::select('users.id as user_id','friends.request_from_id as from_id','friends.request_to_id as to_id')
            ->where('answer','=',1)
            ->where('birth_date','like','%'.$birth)
            ->join('friends', function($join)
            {
                $join->on('users.id', '=', 'request_from_id')
                    ->orOn('users.id', '=', 'request_to_id');
            })
            ->get();
        $data= array();
        foreach($friends as $sender)
        {
            if($sender['user_id']==$sender['from_id'])
            {
                $data[] = array('sender_id'=>$sender['to_id'], 'recipient_id'=>$sender['user_id'], 'message'=> 'Happy birthday bro');
            }
            else
            {
                $data[] = array('sender_id'=>$sender['from_id'], 'recipient_id'=>$sender['user_id'], 'message'=> 'Happy birthday bro');
            }
        }
        Chat::insert($data);
    }
}
