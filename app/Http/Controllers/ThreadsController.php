<?php

namespace App\Http\Controllers;

use App\Filters\ThreadFilters;
use App\Thread;
use App\Channel;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ThreadsController extends Controller
{
    /**
     * Create a new ThreadsController instance
     */
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']); // zuvhun store uildeld auth shaardana
    }

    /**
     * Display a listing of the resource
     * 
     * @!
     */

    public function index(Channel $channel, ThreadFilters $filters)
    {
        $threads = $this->getThreads($channel, $filters);       
        
        return view('threads.index', compact('threads'));
    }


    public function create()
    {
        return view('threads.create');
    }

   
    public function store(Request $request)
    {
        // dd(request()->all());
        $this->validate($request, [
            'title' => 'required|spamfree',
            'body' => 'required|spamfree',
            'channel_id' => 'required|exists:channels,id'
        ]);

        $thread = Thread::create([
            'user_id' => auth()->id(), 
            'channel_id' => request('channel_id'), 
            'title' => request('title'),
            'body' => request('body')
        ]);

        return redirect($thread->path())
        ->with('flash', 'Таны нийтлэл нийтлэгдлээ!');
         
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function show($channelId, Thread $thread)
    {
        // auth()->user()->read($thread);
        // $key = sprintf("users.%s.visists.%s", auth()->id(), $thread->id);
        // cache()->forever($key, Carbon::now());

        if (auth()->check()){
            auth()->user()->read($thread);
        }
        
        return view('threads.show', compact('thread'));
        // 38 removed
        // [ 
        // 'thread' => $thread,
            // 'replies' => $thread->replies()->paginate(10)
        // ]);
    }

    public function destroy($channelId, Thread $thread)
    {
        // authorize thread when user_id is matched logged in user_id
        $this->authorize('update', $thread);
 
        $thread->delete();

        if(request()->wantsJson()){
            return response([], 204);
        }

        return redirect('/threads');
    }


    protected function getThreads(Channel $channel, ThreadFilters $filters){

        $threads = Thread::latest()->filter($filters);

        if( request()->wantsJson()){
            return $threads;
        }
       
        if($channel->exists){
            $threads->where('channel_id', $channel->id);
        }

        return $threads = $threads->get();
       
    }
}
