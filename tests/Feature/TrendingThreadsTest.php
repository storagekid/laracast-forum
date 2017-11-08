<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Trending;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class TrendingThreadsTest extends TestCase
{
    use DatabaseMigrations;    

    public function setUp() {
        parent::setUp();
        Trending::forget('trending_tests');
        $this->thread = create('App\Thread');
    }
    /** @test */
    public function it_increments_a_threads_score_each_time_it_is_read()
    {
        $thread2 = create('App\Thread');
        $d = Trending::get('trending_tests', 0, -1);
        $this->assertEmpty($d);

        $this->call('GET', $this->thread->path());
        $this->call('GET', $thread2->path());
        $this->call('GET', $thread2->path());

        $collect = Trending::doJson('trending_tests');
        $this->assertCount(2, $collect); // 2 Thread Visited
        // The less visited Thread is the last item in the array
        $this->assertEquals($this->thread->title, $collect[1]->title);
        // The most visited Thread is the first item in the array
        $this->assertEquals($thread2->title, $collect[0]->title);
    }
    /** @test */
    public function it_can_set_and_delete_new_trendings_topics()
    {
        Trending::set('trending_tests', 1, [
            'title' => 'Topic Title 1',
            'link' => 'Topic PAth 1'
        ]);
        $collect = Trending::doJson('trending_tests');
        $this->assertCount(1, $collect); // 2 Thread Visited
        Trending::forget('trending_tests');
        $collect = Trending::doJson('trending_tests');
        $this->assertCount(0, $collect); // 2 Thread Visited
    }
}




