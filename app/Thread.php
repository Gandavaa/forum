<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Filters\ThreadFilters;

class Thread extends Model
{
    protected $guarded =[];

    protected $with = ['creator', 'channel'];

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope('replyCount', function ($builder) {
            $builder->withCount('replies');
        });

        //when deleteing thread its should be deleting replies too
        static::deleting(function ($thread){
            $thread->replies()->delete();
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

    public function addReply($reply)
    {
    	$this->replies()->create($reply);
    }
    
    public function scopeFilter($query, ThreadFilters $filters){
        return $filters->apply($query);
    }

}
