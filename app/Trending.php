<?php

namespace App;

use Illuminate\Support\Facades\Redis;
use Illuminate\Database\Eloquent\Model;

class Trending extends Model
{
    public static function get($name, $start = 0, $end = -1) {
       return Redis::zrevrange($name, $start, $end, 'WITHSCORES');
    }

    public static function set($name, $amount = 1, $items = '') {
    	$name = app()->environment('testing') ? 'trending_tests' : $name;
    	if (is_array($items)) {
    		$newItems = json_encode($items);
    	} else {
    		$newItems = $items;
    	}
       Redis::zincrby($name, $amount, $newItems);
    }

    public static function forget($name) {
       Redis::del($name);
    }

    public static function doJson($name, $start = 0, $end = -1) {
    	$array = self::get($name, $start, $end);
    	$json = [];
        foreach ($array as $key => $value) {
            $item = [];
            $item[0] = json_decode($key);
            $item[0]->score = $value;
            $json[] = $item[0];
        }
        return $json;
    }
}
