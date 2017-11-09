<?php

namespace App;
use Illuminate\Support\Facades\Redis;

class Visits {

	protected $model;

	public function __construct($model) {
		$this->model = $model;
	}

	public function reset() {
	    Redis::del($this->cacheKey());
	}
	public function record() {
	    return Redis::incr($this->cacheKey());
	}
	public function count() {
	    return Redis::get($this->cacheKey()) ?? 0;
	}
	public function cacheKey() {
	    return "threads.{$this->model->id}.visits";
	}	
}