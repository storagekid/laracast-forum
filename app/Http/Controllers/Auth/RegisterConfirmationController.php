<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;

class RegisterConfirmationController extends Controller
{
    public function index() {
    	User::where('confirmation_token', request('token'))
    		->firstOrFail()
    		->confirm();
    	return redirect()
    		->route('home')
    		->with([
    			'flash' => 'Your account is now confirmed',
    			'flash-label' => 'success'
    		]);
    }
}
