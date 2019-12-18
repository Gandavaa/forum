<?php

namespace App\Http\Controllers;

use App\Thread;
use Illuminate\Http\Request;

class RepliesController extends Controller
{
    public function __construct()
	{
		$this->middleware('auth');

	}

    function store($channelId, Thread $thread){
		
		$this->validate($request, ['body' => 'required']);

        $thread->addReply([

			'body' => request('body'), 
			
			'channel_id' => $channelId,

			'user_id' => auth()->id()
		
		]);

		return back();

    }
}
