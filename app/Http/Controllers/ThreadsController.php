<?php

namespace App\Http\Controllers;

use App\Filters\ThreadFilters;
use App\Thread;
use App\Channel;
use Illuminate\Http\Request;

class ThreadsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']); // zuvhun store uildeld auth shaardana
    }

    public function index(Channel $channel, ThreadFilters $filters)
    {
    //    $threads = 
    //    $this->getThreads($channel, $filters);

        $threads =Thread::latest()->filter($filters);

        if($channel->exists){
            $threads->where('channel_id', $channel->id);
        }

        $threads = $threads->get();
        
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
            'title' => 'required',
            'body' => 'required',
            'channel_id' => 'required|exists:channels,id'
        ]);

        $thread = Thread::create([
            'user_id' => auth()->id(), 
            'channel_id' => request('channel_id'), 
            'title' => request('title'),
            'body' => request('body')
        ]);

        return redirect($thread->path());
         
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($channelId, Thread $thread)
    {
        return view('threads.show', compact('thread'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        
    }

    protected function getThreads(Channel $channel, ThreadFilters $filters){

        $threads =Thread::latest()->filter($filters);

        if($channel->exists){
            $threads->where('channel_id', $channel->id);
        }

        return $threads = $threads->get();
       
    }
}
