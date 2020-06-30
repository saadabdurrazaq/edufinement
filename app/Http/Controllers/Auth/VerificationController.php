<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Http\Request;
use App\User;
use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Events\Verified;
use app\VerifyUser;
use Auth;

class VerificationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Email Verification Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling email verification for any
    | user that recently registered with the application. Emails may also
    | be re-sent if the user didn't receive the original email message.
    |
    */

    use VerifiesEmails;

    /**
     * Where to redirect users after verification.
     *
     * @var string
     */
    //protected $redirectTo = '/home';

    protected function redirectTo()
    {
        //generate URL dynamicaly .
        return '/login'; // return dynamicaly generated URL.
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() 
    {
        //$this->middleware('auth');
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }

    public function verify(Request $request)
    {
        if ($request->route('id') != $request->user()->getKey()) {
            throw new AuthorizationException;
        }

        if ($request->user()->hasVerifiedEmail()) {
            //return redirect()->intended('/');
            return redirect('login')->with(Auth::logout());
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        //return redirect()->intended('/login')->with('verified', true);
        $msg = 'Congratulations! You are verified. Please login to continue';
        $request->session()->flash('success',  $msg);
        return redirect('login')->with(Auth::logout());
    }

}
