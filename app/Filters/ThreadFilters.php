<?php

namespace App\Filters;

use App\User;
use Illuminate\Http\Request;

class ThreadFilters extends Filters {

	protected $filters = ['by','popularity', 'unreplied'];

	/**
	 * Filter the Query by a given username on URI('by')
	 * @param string $username
	 * @return mixed	
	 */

	protected function by($username) {

		$user = \App\User::where('name',$username)->firstOrFail();

        return $this->builder->where('user_id',$user->id);

	}

	/**
	 * Filter the Query by the number of replies or popularity
	 * @return mixed	
	 */

	protected function popularity($count = null) {

		// Reset any order apply to the query	
		$this->builder->getQuery()->orders = [];

        return $this->builder->orderBy('replies_count', 'desc');

	}

	protected function unreplied() {

        return $this->builder->where('replies_count', 0);

	}

}