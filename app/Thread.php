<?php

namespace App;

use App\Filters\ThreadFilters;
use App\Notifications\ThreadWasUpdated;
use Illuminate\Database\Eloquent\Model;

class Thread extends Model
{
    use RecordsActivity;

    protected $guarded =[];

    protected $with = ['creator', 'channel'];
    
    // #40-42 added append is added method to class values
    protected $appends = ['isSubscribedTo'];

    protected static function boot()
    {
        parent::boot();
        // #39 git ride of this chunk no longer need it
        // static::addGlobalScope('replyCount', function ($builder) {
        //     $builder->withCount('replies');
        // });

        //when deleteing thread its should be deleting replies too
        static::deleting(function ($thread){
            // 28 updating here delete each reply also fires recrods activity delete called
            $thread->replies->each->delete();
            // $thread->replies()->each(function($reply){
            //     $reply->delete();
            // });
        });
        
    }

    public function path(){
        return "/threads/{$this->channel->slug}/{$this->id}";
    }
    
    public function replies(){
      
        return $this->hasMany(Reply::class)
                ->withCount('favorites')
                ->withCount('owner');

    }

    public function creator()
    {
    	return $this->belongsTo(User::class, 'user_id');
    }

    public function channel(){
        return $this->belongsTo(Channel::class);

    }

    /**
     * Add reply to the tread
     * @parem $reply
     * @return Model
     */
    public function addReply($reply)
    {
        $reply = $this->replies()->create($reply);
        
        // #43 prepare notifications for all subscribers.
        $this->subscriptions
            ->filter(function ($sub) use ($reply) {
                return $sub->user_id ! = $reply->user_id; 
            })
            ->each->notify($reply);
            // (function($sub) use ($reply) {
                // $sub->user->notify(new ThreadWasUpdated($thread, $reply));
            // });

            // foreach( $this->subscriptions as $subscription){
            //     // if subscription user_id not reply->user_id bval
            //     if($subscription->user_id != $reply->user_id)
            //       $subscription->user->notify(new ThreadWasUpdated($thread, $reply));
            // }

        return $reply; 

    }
    
    public function scopeFilter($query, ThreadFilters $filters){
        return $filters->apply($query);
    }

    public function subscribe($userId =null)
    {
        $this->subscriptions()->create([
            'user_id' => $userId ?: auth()->id()
        ]);
    }

    public function unsubscribe($userId = null)
    {
        $this->subscriptions()
             ->where('user_id', $userId ?: auth()->id())
             ->delete();
    }
    
    public function subscriptions()
    {
        return $this->hasMany(ThreadSubscription::class);
    }
    public function getIsSubscribedToAttribute()
    {
        // return boolean
        return $this->subscriptions()
                ->where('user_id', auth()->id())
                ->exists();
    }

}
