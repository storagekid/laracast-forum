<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Activity;

class ProfileController extends Controller
{

	protected $user;

	public function __construct() {

		$this->middleware('auth');

	}

    public function show(User $user) {

    	return view('profiles.show', [

			'userProfile' => $user,
			// 'threads' => $user->threads()->paginate(10),
			'activities' => Activity::feed($user),

		]);

    }
}
