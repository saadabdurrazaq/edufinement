<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Validator,Redirect,Response,File;
use Socialite;
use App\User;
use Session;
use Auth;
use App\SocialFacebookAccount;
use Laravel\Socialite\Contracts\User as ProviderUser;
use App\Services\SocialFacebookAccountService;

class SocialController extends Controller
{
    /*public function __construct(Socialite $socialite){
        $this->socialite = $socialite;
    }*/

    public function redirect()
    {
        return Socialite::driver('facebook')->redirect();
    }

    /*public function callback($provider)
    {
        $uInfo = Socialite::driver($provider)->user(); 
        //$user = $this->findOrCreate($uInfo,$provider); 
        $user = $this->createOrGetUser($uInfo,$provider); 
        auth()->login($user); 
        return redirect()->to('/home');
    } */

    /**
    * Return a callback method from facebook api.
    * 
    * @return callback URL from facebook
    */
    public function callback(SocialFacebookAccountService $service)
    {
        $user = $service->createOrGetUser(Socialite::driver('facebook')->user());
        auth()->login($user);
        return redirect()->to('/home');
    }

}