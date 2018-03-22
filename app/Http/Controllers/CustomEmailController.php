<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\PleaseConfirmYourEmail;

class CustomEmailController extends Controller
{
    public function fire()
    {
        // Mail::to($event->user)->cc(['jgvillalba@dentix.es','estudiodentix@outlook.com'])->send(new PleaseConfirmYourEmail($event->user));
        Mail::to("jgvillalba@mozodealmacen.com")
                ->cc(['jgvillalba@dentix.es','estudiodentix@outlook.com'])
                ->send(new PleaseConfirmYourEmail(auth()->user()));
        return back();
    }
}
