<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;

class RegisterConfirmationController extends Controller
{
    public function index() {
    	try {
            User::where('confirmation_token', request('token'))
                ->firstOrFail()
                ->confirm();
        } catch (\Exception $e) {
            return redirect()
            ->route('home')
            ->with([
                'flash' => 'Unknown token',
                'flash-label' => 'danger'
            ]);
        }
    	return redirect()
    		->route('home')
    		->with([
    			'flash' => 'Your account is now confirmed',
    			'flash-label' => 'success'
    		]);
    }
}
