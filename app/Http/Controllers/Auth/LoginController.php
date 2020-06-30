<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use URL;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string 
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
    

    // select * from `father_registrars` where `username` = 'sugeng' and exists (select * from `student_registrars` inner join `father_registrars_student_registrars` on `student_registrars`.`id` = `father_registrars_student_registrars`.`student_registrars_id` where `father_registrars`.`id` = `father_registrars_student_registrars`.`father_registrars_id` and `student_registrars`.`status` = 'Qualified') and `father_registrars`.`deleted_at` is null

}
