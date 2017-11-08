<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Redis;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class TrendingThreadsTest extends TestCase
{
    use DatabaseMigrations;    

    public function setUp() {
        parent::setUp();
        Redis::del('trending_threads');
        $this->thread = create('App\Thread');
    }
    /** @test */
    public function it_increments_a_threads_score_each_time_it_is_read()
    {
        $thread2 = create('App\Thread');
        $d = Redis::zrevrange('trending_threads', 0, -1);
        $this->assertEmpty($d);
        $this->call('GET', $this->thread->path());
        $this->call('GET', $thread2->path());
        $this->call('GET', $thread2->path());
        $d = Redis::zrevrange('trending_threads', 0, -1, 'WITHSCORES');
        // Add Scores and create and array with objects
        $collect = [];
        foreach ($d as $key => $value) {
            $item = [];
            $item[0] = json_decode($key);
            $item[0]->score = $value;
            $collect[] = $item[0];
        }
        // dd($collect);
        $this->assertCount(2, $collect); // 2 Thread Visited
        // The less visited Thread is the last item in the array
        $this->assertEquals($this->thread->title, $collect[1]->title);
        // The most visited Thread is the first item in the array
        $this->assertEquals($thread2->title, $collect[0]->title);
    }

}




