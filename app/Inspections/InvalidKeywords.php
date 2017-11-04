<?php

namespace App\Inspections;

class InvalidKeywords
{
	protected $invalidWords = [
		'yahoo customer support',
	];
    public function detect($text) {
    	foreach ($this->invalidWords as $invalidWord) {
    		if (stripos($text, $invalidWord) !== false) {
                 return true;
    		}
    	}
    }
}
