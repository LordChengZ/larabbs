<?php

namespace App\Models;
/*use App\Models\Category;
use App\Models\User;*/

class Topic extends Model
{
    protected $fillable = ['title', 'body', 'category_id', 'excerpt', 'slug'];

    public function category()
    {
    	return $this->belongsTo(Category::class);
    }

    public function user()
    {
    	return $this->belongsTo(User::class);
    }

    public function scopeWithOrder($query, $order)
    {
    	//用范围函数 scope 定义的时候，实例化调用的时候，去掉scope，然后首字母小写
    	switch ($order) {
    		case 'recent':
    			$query->recent();
    			break;
    		
    		default:
    			$query->recentReplied();
    			break;
    	}

    	return $query->with('user', 'category');
    }

    public function scopeRecentReplied($query)
    {
    	return $query->orderBy('updated_at', 'desc');
    }

    public function scopeRecent($query)
    {
    	return $query->orderBy('created_at', 'desc');
    }

    public function link($params = [])
    {
    	return route('topics.show', array_merge([$this->id, $this->slug], $params));
    }

    public function replies()
    {
    	return $this->hasMany(Reply::class);
    }
}
