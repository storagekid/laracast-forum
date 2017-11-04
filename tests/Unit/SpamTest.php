<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Inspections\Spam;

class SpamTest extends TestCase
{
	public function setUp() {
		parent::setUp();
		$this->spam = new Spam();
	}

    /** @test */
    public function it_check_for_invalid_keywords()
    {
    	$response = $this->spam->detect('Innocent reply here.');
        $this->assertFalse($response);
        $this->expectException('Exception');
        $response = $this->spam->detect('Patata yahoo customer support');
    }

    /** @test */
    public function it_check_for_any_key_being_held_down()
    {
    	$this->expectException('Exception');
    	$response = $this->spam->detect('Innocent reply here aaaaaaaaa.');
    }
}
