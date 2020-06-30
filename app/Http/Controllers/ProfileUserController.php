<?php

namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
use App\Logic\Image\ImageRepository;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;
use App\models\Image;
use PDF;
use DB;
use View;
use App\Exports\AdminExport;
use App\Exports\ActiveAdminExport;
use App\Exports\InactiveAdminExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use Illuminate\Validation\Validator; 
use Illuminate\Support\Facades\Gate;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\URL;
use App\Rules\MatchOldPassword;
use Illuminate\Support\Facades\Hash;
use App\User;
use App\Http\Requests\ChangePasswordRequest;
use Illuminate\Support\Facades\Auth;

class ProfileUserController extends Controller {

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function show($username, Request $request) { 
        $show['data'] = \App\User::where('username', auth()->user()->username)->first();
        $data = \App\User::where('username', auth()->user()->username)->first();

        $tab2 = "tab2";

        if($request->ajax()){
            return \Response::json($show);
        }
        return view('profile.show', ['users' => $data], compact(['tab2']));   
    }

    public function update(Request $request, $id)
    {
        $username = \App\User::select(['username'])->where('username', auth()->user()->username)->first();

        $validator = \Validator::make($request->all(), [
            "name" => "required|min:5|max:100",
        ]);

        if ($validator->fails()) {
            return redirect()->to('/profile/'.$username.'/#detail')->withInput()->withErrors($validator);
        } 

        $user = \App\User::findOrFail($id);

        $user->name = $request->get('name'); 
        
        if($request->file('file')) {
            if($user->avatar && file_exists(storage_path('app/public/' . $user->avatar) ) ) {
                \Storage::delete('public/'.$user->avatar);
            }
            $imagename = $request->file('file')->store('avatars', 'public');
            $user->avatar = $imagename;
        }

        $user->save();

        return redirect()->to('/profile/'.$username.'/#detail')->with('status', 'User succesfully updated');   
    }

    public function deleteAvatar($id) {
        $category = \App\User::where('id', $id);
        $category->update(['avatar' => null]);

        $id = \App\User::select(['username'])->where('username', auth()->user()->username)->first();
      
        return redirect()->to('/profile/'.$id.'/#detail')->with('status', 'Photo succesfully deleted');   
    }

    public function changePassword()
    {
        return view('profile.changepassword')->with(
            ['email' => Auth::user()->email]
        );
    }
 
    public function postChangePassword(Request $request) 
    {
        $username = \App\User::select(['username'])->where('username', auth()->user()->username)->first();

        $validator = \Validator::make($request->all(), [
            'password' => 'required|same:password|min:6',
            'password_confirmation' => 'required|same:password',
            'current_password' => 'required'
        ]);

        if ($validator->fails()) {
            return redirect()->to('/profile/'.$username.'/#changepassword')->withInput()->withErrors($validator);
        } 

    	if(Auth::Check())
  	    {	 
            if(\Hash::check($request->current_password, Auth::User()->password))
               {
    			$user = User::find(Auth::user()->id)->update(["password" => bcrypt($request->password)]);    	
  			}
  			else {
                  return redirect()->to('/profile/'.$username.'/#changepassword')->with('alert-danger', 'Incorrect current password!'); 
  			}
  		}
        $msg = 'Password was changed successfully. Please login to continue';
        $request->session()->flash('success',  $msg);
        return redirect('login')->with(Auth::logout());  
    }
    
}
