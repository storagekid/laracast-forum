<?php

namespace App\Inspections;
use Exception;

class Spam
{
    protected $inspections = [
        InvalidKeyWords::class,
        KeyHeldDown::class,
    ];

    public function detect($text) {
        foreach ($this->inspections as $inspection) {
            if(app($inspection)->detect($text)) {
                throw new Exception('Your reply contains spam.');
            }
        }
    	return false;
    }
}
