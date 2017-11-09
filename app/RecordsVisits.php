<?php

namespace App;
use Illuminate\Support\Facades\Redis;

trait RecordsVisits {

	public function recordVisit($model) {
	    return Redis::incr($this->visitsCacheKey($model));
	}
	public function visits($model) {
	    return Redis::get($this->visitsCacheKey($model)) ?? 0;
	}
	public function resetVisits($model) {
	    Redis::del($this->visitsCacheKey($model));
	}
	public function visitsCacheKey($model) {
	    return "{$model}.{$this->id}.visits";
	}	
}