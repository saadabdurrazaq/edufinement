<?php

namespace App\Http\Controllers;

use App\Applicant;
use App\VerifyUser;
use App\mail\VerifyMail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Auth\Events\Registered;
use App\Notifications\MailForApplicant;
use Spatie\Permission\Models\Role;
use DB;
use App\Notifications\ApprovedApplicant;
use App\Notifications\RejectedApplicant;
use App\Notifications\ApplicantOnHold;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Mail\ClientMail;
use Session;
use View;
use Exception;
use App\Mail\SendEmail;
use App\Mail\RejectedEmail;
use App\Mail\OnHoldEmail;
use Carbon\Carbon;
use Config;

class MotherRegistrarsController extends Controller
{
    
    /**
    * Create a new controller instance.
    *
    * @return void
    */
    public function __construct()
    {
       
    }

    public function register()
    {
        return view('mother-registrars.register');
    }

    public function store(Request $request) 
    {
         $validation = \Validator::make($request->all(),[
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users', 'unique:mother_registrars', 'unique:student_registrars', 'unique:father_registrars', 'unique:guardianmale_registrars', 'unique:guardianfemale_registrars'],
            'phone' => ['required', 'digits_between:10,12', 'unique:users', 'unique:mother_registrars', 'unique:student_registrars', 'unique:father_registrars', 'unique:guardianmale_registrars', 'unique:guardianfemale_registrars'],
            'username' => ['required','min:5', 'max:20', 'unique:users', 'unique:mother_registrars', 'unique:student_registrars', 'unique:father_registrars', 'unique:guardianmale_registrars', 'unique:guardianfemale_registrars', 'regex:/^\S*$/u'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'account-key' => 'min:6|required_with:account-key_confirmation|same:account-key_confirmation', 
            'account-key_confirmation' => 'min:6'
        ])->validate();
        
        $new_user = new \App\MotherRegistrars(); //Panggil model User
        $new_user->name = $request->get('name');
        $new_user->email = $request->get('email');
        $new_user->phone = $request->get('phone');
        $new_user->username = $request->get('username');
        $new_user->gender = $request->get('gender');
        $new_user->password = \Hash::make($request->get('password'));
        $new_user->account_key = \Hash::make($request->get('account-key'));
        $new_user->assignRole('Parent');
        $new_user->save();

        $motherUsername = $request->get('username');
        $getMotherUsername = \App\MotherRegistrars::where('username',  $motherUsername)->get();

        $getMotherID = \App\MotherRegistrars::where('username',  $motherUsername)->first()->id;

        $getSelectedUser = \App\GuardianFemaleRegistrars::where('username', function($query) use ($getMotherUsername) { //retrieve a collection of users from users table where username in table users. (continue below)
            $query->select('username')->from('mother_registrars')->where('username', $getMotherUsername); //$query(where usernames in table users) like selected usernames in student_registrars table. (To get selected usernames in student_registrars table, use where('id', $id) parameter.)
        });

        if($getSelectedUser) {
            $getSelectedUser->forceDelete(); 
        } 

        $new_user = new \App\GuardianFemaleRegistrars(); //Panggil model User
        $new_user->id = $getMotherID;
        $new_user->name = $request->get('name');
        $new_user->email = $request->get('email');
        $new_user->phone = $request->get('phone');
        $new_user->username = $request->get('username');
        $new_user->gender = $request->get('gender');
        $new_user->password = \Hash::make($request->get('password'));
        $new_user->account_key = \Hash::make($request->get('account-key'));
        $new_user->assignRole('Parent');
        $new_user->save();

        //$new_user->notify(new MailForApplicant($new_user)); 

        return redirect()->route('mother-regis')->with('status', 'Registration successfull. Thank you for registration! You will be notified if your child is approved');
    }

    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function index(Request $request) {
        $filterKeyword = $request->get('keyword');
        $status = $request->get('status');

        $eligibleStatus = \App\MotherRegistrars::where('status', "Eligible")->count();
        $activeStatus = \App\MotherRegistrars::where('status', "Qualified")->count();
        $inactiveStatus = \App\MotherRegistrars::where('status', "Rejected")->count();
        $countTrash = \App\MotherRegistrars::onlyTrashed()->count();
        $countPending = \App\MotherRegistrars::where('status', "Pending")->count();

        $count = \App\MotherRegistrars::count();
        $items = $request->items ?? 5;
        $page    = $request->has('name') ? $request->get('name') : 1;
        $showingTotal  = $page * $items; 
        $admins= \App\MotherRegistrars::paginate($items);
        $showingStarted = $admins->currentPage(); 

        $showData = "Showing $showingStarted to $showingTotal of $count";

        if($status) {
            $data = \App\MotherRegistrars::where('status', $status)->paginate($items);
        } else {
            $data = \App\MotherRegistrars::paginate($items);
        }

        if($filterKeyword) {
            if($status) {
                $data = \App\MotherRegistrars::where('name', 'LIKE', "%$filterKeyword%")->orWhere('email', 'LIKE', "%$filterKeyword%")->where('status', $status)->paginate($items);
            } else {
                $data = \App\MotherRegistrars::where('name', 'LIKE', "%$filterKeyword%")->orWhere('email', 'LIKE', "%$filterKeyword%")->paginate($items);
            }
        } 
    
        return view('mother-registrars.index', compact('data'))->with(array('countPending' => $countPending, 'showData' => $showData, 'count' => $count, 'eligibleStatus' => $eligibleStatus, 'activeStatus' => $activeStatus, 'inactiveStatus' => $inactiveStatus, 'countTrash' => $countTrash))->withItems($items); //admin mengacu ke table admin di phpmyadmin
    }

    public function destroy($id) {
        \App\MotherRegistrars::find($id)->delete();
        return redirect()->route('mother-registrars.index')->with('success','Applicant deleted successfully');
    }

    public function destroyMultiple(Request $request) {
        $get_ids = $request->ids;
        $ids = explode(',', $get_ids);
        $users = \App\MotherRegistrars::whereIn('id', $ids);
        $users->delete(); 

        return response()->json(['success' => "Applicants successfully moved to trash."]);
    }

    public function trash(Request $request) {
        $filterKeyword = $request->get('keyword');
        $status = $request->get('status');

        $count = \App\MotherRegistrars::count();
        $eligibleStatus = \App\MotherRegistrars::where('status', "Eligible")->count();
        $activeStatus = \App\MotherRegistrars::where('status', "Qualified")->count();
        $inactiveStatus = \App\MotherRegistrars::where('status', "Rejected")->count();
        $countTrash = \App\MotherRegistrars::onlyTrashed()->count();
        $countPending = \App\MotherRegistrars::where('status', "Pending")->count();

        $count = \App\MotherRegistrars::count();
        $items = $request->items ?? 5;
        $page    = $request->has('name') ? $request->get('name') : 1;
        $showingTotal  = $page * $items;
        $admins= \App\MotherRegistrars::paginate($items);
        $showingStarted = $admins->currentPage(); 

        $showData = "Showing $showingStarted to $showingTotal of $countTrash";

        if($status) {
            $data = \App\MotherRegistrars::where('status', $status)->paginate($items);
        } else {
            $data = \App\MotherRegistrars::onlyTrashed()->paginate($items);
        }

        if($filterKeyword) {
            if($status) {
                $data = \App\MotherRegistrars::onlyTrashed()->where('name', 'LIKE', "%$filterKeyword%")->orWhere('email', 'LIKE', "%$filterKeyword%")->where('status', $status)->paginate($items);
            } else {
                $data = \App\MotherRegistrars::onlyTrashed()->where('name', 'LIKE', "%$filterKeyword%")->orWhere('email', 'LIKE', "%$filterKeyword%")->paginate($items);
            }
        } 

        return view('mother-registrars.trash', compact('data'))->with(array('countPending' => $countPending, 'showData' => $showData, 'count' => $count, 'eligibleStatus' => $eligibleStatus, 'activeStatus' => $activeStatus, 'inactiveStatus' => $inactiveStatus, 'countTrash' => $countTrash))->withItems($items); //admin mengacu ke table admin di phpmyadmin
    }

    public function restore($id) {
        $category = \App\MotherRegistrars::withTrashed()->findOrFail($id);

        if($category->trashed()) {
            $category->restore(); 
        } else {
            return redirect()->route('mother-registrars.trash')->with('status', 'Applicant is not in trash');
        }  
      
        return redirect()->route('mother-registrars.trash')->with('status', 'Applicant successfully restored');
    }

    public function restoreMultiple(Request $request) {
        $get_ids = $request->ids;
        $ids = explode(',', $get_ids);
        $users = \App\MotherRegistrars::whereIn('id', $ids);
        $users->restore();
        
        return response()->json(['success' => "Applicants successfully restored"]);
    }

    public function deletePermanent($id) {
        $category = \App\MotherRegistrars::withTrashed()->findOrFail($id);

        $selectedUsers = \App\User::where('username', function($query) use ($id) { //retrieve a collection of users from users table whereIn usernames. (continue below)
            $query->select('username')->from('mother_registrars')->where('id', $id); //$query(whereIn usernames in table users) like selected usernames in applicants table. (To get selected usernames in applicants table, use whereIn('id', $ids) parameter.)
        });

        if(!$category->trashed()){
            return redirect()->route('mother-registrars.trash')->with('status', 'Can not delete permanent applicant');
        } 
        else {
            if($selectedUsers) {
                $selectedUsers->forceDelete(); 
                $category->forceDelete(); 
            } else {
                $category->forceDelete();
            }
            return redirect()->route('mother-registrars.trash')->with('status', 'Applicant permanently deleted');
        }
    }

    public function deleteMultiple(Request $request) {
        $get_ids = $request->ids;
        $ids = explode(',', $get_ids);
        $users = \App\MotherRegistrars::whereIn('id', $ids);

        $selectedUsers = \App\User::whereIn('username', function($query) use ($ids) { //retrieve a collection of users from users table whereIn usernames. (continue below)
            $query->select('username')->from('mother_registrars')->whereIn('id', $ids); //$query(whereIn usernames in table users) like selected usernames in applicants table. (To get selected usernames in applicants table, use whereIn('id', $ids) parameter.)
        });

        if($selectedUsers) {
            $selectedUsers->forceDelete(); 
            $users->forceDelete(); 
        } else {
            $users->forceDelete(); 
        }

        return response()->json(['success' => "Applicants successfully permanently deleted"]);
    }

    public function showEligible(Request $request) {
        $filterKeyword = $request->get('keyword');
        $status = $request->get('status');

        $count = \App\MotherRegistrars::count();
        $eligibleStatus = \App\MotherRegistrars::where('status', "Eligible")->count();
        $activeStatus = \App\MotherRegistrars::where('status', "Qualified")->count();
        $inactiveStatus = \App\MotherRegistrars::where('status', "Rejected")->count();
        $countTrash = \App\MotherRegistrars::onlyTrashed()->count();
        $countPending = \App\MotherRegistrars::where('status', "Pending")->count();

        $count = \App\MotherRegistrars::count();
        $items = $request->items ?? 5;
        $page    = $request->has('name') ? $request->get('name') : 1;
        $showingTotal  = $page * $items;
        $admins= \App\MotherRegistrars::paginate($items);
        $showingStarted = $admins->currentPage(); 

        $showData = "Showing $showingStarted to $showingTotal of $countPending";

        if($status) {
            $data = \App\MotherRegistrars::where('status', $status)->paginate($items);
        } else {
            $data = \App\MotherRegistrars::where('status', "Eligible")->paginate($items);
        }

        if($filterKeyword) {
            if($status) {
                $data = \App\MotherRegistrars::where('name', 'LIKE', "%$filterKeyword%")->orWhere('email', 'LIKE', "%$filterKeyword%")->where('status', $status)->paginate($items);
            } else {
                $data = \App\MotherRegistrars::where('name', 'LIKE', "%$filterKeyword%")->orWhere('email', 'LIKE', "%$filterKeyword%")->paginate($items);
            }
        } 

        return view('mother-registrars.show-eligible', compact('data'))->with(array('countPending' => $countPending, 'showData' => $showData, 'count' => $count, 'eligibleStatus' => $eligibleStatus, 'activeStatus' => $activeStatus, 'inactiveStatus' => $inactiveStatus, 'countTrash' => $countTrash))->withItems($items); //admin mengacu ke table admin di phpmyadmin
    }

    public function pending(Request $request) {
        $filterKeyword = $request->get('keyword');
        $status = $request->get('status');

        $count = \App\MotherRegistrars::count();
        $eligibleStatus = \App\MotherRegistrars::where('status', "Eligible")->count();
        $activeStatus = \App\MotherRegistrars::where('status', "Qualified")->count();
        $inactiveStatus = \App\MotherRegistrars::where('status', "Rejected")->count();
        $countTrash = \App\MotherRegistrars::onlyTrashed()->count();
        $countPending = \App\MotherRegistrars::where('status', "Pending")->count();

        $count = \App\MotherRegistrars::count();
        $items = $request->items ?? 5;
        $page    = $request->has('name') ? $request->get('name') : 1;
        $showingTotal  = $page * $items;
        $admins= \App\MotherRegistrars::paginate($items);
        $showingStarted = $admins->currentPage(); 

        $showData = "Showing $showingStarted to $showingTotal of $countPending";

        if($status) {
            $data = \App\MotherRegistrars::where('status', $status)->paginate($items);
        } else {
            $data = \App\MotherRegistrars::where('status', "Pending")->paginate($items);
        }

        if($filterKeyword) {
            if($status) {
                $data = \App\MotherRegistrars::where('name', 'LIKE', "%$filterKeyword%")->orWhere('email', 'LIKE', "%$filterKeyword%")->where('status', $status)->paginate($items);
            } else {
                $data = \App\MotherRegistrars::where('name', 'LIKE', "%$filterKeyword%")->orWhere('email', 'LIKE', "%$filterKeyword%")->paginate($items);
            }
        } 

        return view('mother-registrars.pending', compact('data'))->with(array('countPending' => $countPending, 'showData' => $showData, 'count' => $count, 'eligibleStatus' => $eligibleStatus, 'activeStatus' => $activeStatus, 'inactiveStatus' => $inactiveStatus, 'countTrash' => $countTrash))->withItems($items); //admin mengacu ke table admin di phpmyadmin
    }

    public function approve($id) {
        $applicant = \App\MotherRegistrars::where('id', $id);
        $applicant->update(['status' => 'Qualified']);

        //replicate the data to users table
        $find_one = \App\MotherRegistrars::where('id', $id)->firstOrFail();
        $find_one->makeHidden(['status', 'id', 'email_sent']);
        $new_user = $find_one->replicate();
        $new_user = $find_one->toArray();

        $getSelectedUser = \App\User::where('username', function($query) use ($id) { //retrieve a collection of users from users table where username in table users. (continue below)
            $query->select('username')->from('mother_registrars')->where('id', $id); //$query(where usernames in table users) like selected usernames in applicants table. (To get selected usernames in applicants table, use where('id', $id) parameter.)
        });

        if($getSelectedUser) {
            $getSelectedUser->forceDelete(); 
            $user = \App\User::firstOrCreate($new_user);
            $user->assignRole('Mother Registrars');
            $user->password = $find_one->password;
            $user->save();
        } else {
            $user = \App\User::firstOrCreate($new_user);
            $user->assignRole('Mother Registrars');
            $user->password = $find_one->password;
            $user->save();
        }

        $user->notify(new ApprovedApplicant($user)); 
      
        return redirect()->route('mother-registrars.pending')->with('status', 'User successfully approved');
    }

    public function approveMultiple(Request $request) {
        $get_ids = $request->ids;
        $ids = explode(',', $get_ids); 

        $users = \App\MotherRegistrars::whereIn('id', $ids);
        $users->update(['status' => 'Qualified']); 

        $find_selected = \App\MotherRegistrars::whereIn('id', $ids)->get();
        $find_selected->makeHidden(['status', 'id', 'email_sent']);
        $find_selected->makeVisible(['password']);
        $new_users = $find_selected->toArray();

        $selectedUsers = \App\User::whereIn('username', function($query) use($ids) { //retrieve a collection of users from users table whereIn usernames. (continue below)
            $query->select('username')->from('student_registrars')->whereIn('id', $ids); //$query(whereIn usernames in table users) like selected usernames in applicants table. (To get selected usernames in applicants table, use whereIn('id', $ids) parameter.)
        });

        if($selectedUsers) {
           $selectedUsers->forceDelete(); //prevent duplicate data in users table
           $bulkUsers = \App\User::insert($new_users);
        } else {
            $bulkUsers = \App\User::insert($new_users);
        }

        $getSelectedUsers = \App\User::whereIn('username', function($query) use($ids) { //retrieve a collection of users from users table whereIn usernames. (continue below)
            $query->select('username')->from('student_registrars')->whereIn('id', $ids); //$query(whereIn usernames in table users) like selected usernames in applicants table. (To get selected usernames in applicants table, use whereIn('id', $ids) parameter.)
        })->get();
        
        foreach($getSelectedUsers as $user) {
            $user->assignRole('mother Registrars');
            $user->save();
        }

        $users->each(function($user) {
            Mail::to($user)->send(new SendEmail());
        });

        if(count(Mail::failures()) > 0) {
            //The Mail::failures() will return an array of failed emails.
            foreach(Mail::failures() as $sent_status) { 
                $newData = \App\SentStatus::create(['email' => $sent_status]);
                $newData->save();
            }
        } 

        return response()->json(['success' => "Selected applicant(s) successfully approved."]);
    }

    public function showApproved(Request $request) {
        $filterKeyword = $request->get('keyword');
        $status = $request->get('status');

        $getDate = \App\StudentRegistrars::select('approved_date')->where('status', "Qualified")->get();
        $echoDate = Carbon::parse(strtotime($getDate))->timezone(Config::get('app.timezone'))->format('l j F Y');

        $count = \App\MotherRegistrars::count();
        $eligibleStatus = \App\MotherRegistrars::where('status', "Eligible")->count();
        $activeStatus = \App\MotherRegistrars::where('status', "Qualified")->count();
        $inactiveStatus = \App\MotherRegistrars::where('status', "Rejected")->count();
        $countTrash = \App\MotherRegistrars::onlyTrashed()->count();
        $countPending = \App\MotherRegistrars::where('status', "Pending")->count();

        $count = \App\MotherRegistrars::count();
        $items = $request->items ?? 5;
        $page    = $request->has('name') ? $request->get('name') : 1;
        $showingTotal  = $page * $items;
        $admins= \App\MotherRegistrars::paginate($items);
        $showingStarted = $admins->currentPage(); 

        $showData = "Showing $showingStarted to $showingTotal of $countPending";

        if($status) {
            $data = \App\MotherRegistrars::where('status', $status)->paginate($items);
        } else {
            $data = \App\MotherRegistrars::where('status', "Qualified")->paginate($items);
        }

        if($filterKeyword) {
            if($status) {
                $data = \App\MotherRegistrars::where('name', 'LIKE', "%$filterKeyword%")->orWhere('email', 'LIKE', "%$filterKeyword%")->where('status', $status)->paginate($items);
            } else {
                $data = \App\MotherRegistrars::where('name', 'LIKE', "%$filterKeyword%")->orWhere('email', 'LIKE', "%$filterKeyword%")->paginate($items);
            }
        } 

        return view('mother-registrars.show-approved', compact('data'))->with(array('countPending' => $countPending, 'showData' => $showData, 'count' => $count, 'eligibleStatus' => $eligibleStatus, 'activeStatus' => $activeStatus, 'inactiveStatus' => $inactiveStatus, 'countTrash' => $countTrash, 'echoDate' => $echoDate))->withItems($items); //admin mengacu ke table admin di phpmyadmin
    }

    public function showRejected(Request $request) {
        $filterKeyword = $request->get('keyword');
        $status = $request->get('status');

        $count = \App\MotherRegistrars::count();
        $eligibleStatus = \App\MotherRegistrars::where('status', "Eligible")->count();
        $activeStatus = \App\MotherRegistrars::where('status', "Qualified")->count();
        $inactiveStatus = \App\MotherRegistrars::where('status', "Rejected")->count();
        $countTrash = \App\MotherRegistrars::onlyTrashed()->count();
        $countPending = \App\MotherRegistrars::where('status', "Pending")->count(); 

        $count = \App\MotherRegistrars::count();
        $items = $request->items ?? 5;
        $page    = $request->has('name') ? $request->get('name') : 1;
        $showingTotal  = $page * $items;
        $admins= \App\MotherRegistrars::paginate($items);
        $showingStarted = $admins->currentPage(); 

        $showData = "Showing $showingStarted to $showingTotal of $countPending";

        if($status) {
            $data = \App\MotherRegistrars::where('status', $status)->paginate($items);
        } else {
            $data = \App\MotherRegistrars::where('status', "Rejected")->paginate($items);
        }

        if($filterKeyword) {
            if($status) {
                $data = \App\MotherRegistrars::where('name', 'LIKE', "%$filterKeyword%")->orWhere('email', 'LIKE', "%$filterKeyword%")->where('status', $status)->paginate($items);
            } else {
                $data = \App\MotherRegistrars::where('name', 'LIKE', "%$filterKeyword%")->orWhere('email', 'LIKE', "%$filterKeyword%")->paginate($items);
            }
        } 

        return view('mother-registrars.show-rejected', compact('data'))->with(array('countPending' => $countPending, 'showData' => $showData, 'count' => $count, 'eligibleStatus' => $eligibleStatus, 'activeStatus' => $activeStatus, 'inactiveStatus' => $inactiveStatus, 'countTrash' => $countTrash))->withItems($items); //admin mengacu ke table admin di phpmyadmin
    }

    public function reject($id) {
        $applicant = \App\MotherRegistrars::where('id', $id);
        $applicant->update(['status' => 'Rejected']);

        $getSelectedUser = \App\User::where('username', function($query) use ($id) { //retrieve a collection of users from users table where username in table users. (continue below)
            $query->select('username')->from('mother_registrars')->where('id', $id); //$query(where usernames in table users) like selected usernames in applicants table. (To get selected usernames in applicants table, use where('id', $id) parameter.)
        });

        if($getSelectedUser) {
            $getSelectedUser->forceDelete(); 
        }

        $user = \App\MotherRegistrars::where('id', $id)->firstOrFail();
        $user->notify(new RejectedApplicant($user)); 
      
        return redirect()->route('mother-registrars.pending')->with('status', 'User successfully rejected');

    }

    public function rejectMultiple(Request $request) {
        $get_ids = $request->ids;
        $ids = explode(',', $get_ids); 

        $users = \App\MotherRegistrars::whereIn('id', $ids);
        $users->update(['status' => 'Rejected']); 

        $selectedUsers = \App\User::whereIn('username', function($query) use($ids) { //retrieve a collection of users from users table whereIn usernames. (continue below)
            $query->select('username')->from('mother_registrars')->whereIn('id', $ids); //$query(whereIn usernames in table users) like selected usernames in applicants table. (To get selected usernames in applicants table, use whereIn('id', $ids) parameter.)
        });

        if($selectedUsers) {
           $selectedUsers->forceDelete(); //prevent duplicate data in users table
        }

        $users->each(function($user) {
            Mail::to($user)->send(new RejectedEmail());
        });

        if(count(Mail::failures()) > 0) {
            //The Mail::failures() will return an array of failed emails.
            foreach(Mail::failures() as $sent_status) { 
                $newData = \App\SentStatus::create(['email' => $sent_status]);
                $newData->save();
            }
        } 

        return response()->json(['success' => "Selected applicant(s) successfully rejected."]);
    }

    public function hold($id) {
        $applicant = \App\MotherRegistrars::where('id', $id);
        $applicant->update(['status' => 'Pending']);

        $getSelectedUser = \App\User::where('username', function($query) use ($id) { //retrieve a collection of users from users table where username in table users. (continue below)
            $query->select('username')->from('mother_registrars')->where('id', $id); //$query(where usernames in table users) like selected usernames in applicants table. (To get selected usernames in applicants table, use where('id', $id) parameter.)
        });

        if($getSelectedUser) {
            $getSelectedUser->forceDelete(); 
        }

        $user = \App\MotherRegistrars::where('id', $id)->firstOrFail();
        $user->notify(new ApplicantOnHold($user)); 
      
        return redirect()->route('mother-registrars.pending')->with('status', 'User successfully on hold');
    }

    public function holdMultiple(Request $request) {
        $get_ids = $request->ids;
        $ids = explode(',', $get_ids); 

        $users = \App\MotherRegistrars::whereIn('id', $ids);
        $users->update(['status' => 'Pending']); 

        $selectedUsers = \App\User::whereIn('username', function($query) use($ids) { //retrieve a collection of users from users table whereIn usernames. (continue below)
            $query->select('username')->from('mother_registrars')->whereIn('id', $ids); //$query(whereIn usernames in table users) like selected usernames in applicants table. (To get selected usernames in applicants table, use whereIn('id', $ids) parameter.)
        });

        if($selectedUsers) {
           $selectedUsers->forceDelete(); //prevent duplicate data in users table
        }

        $users->each(function($user) {
            Mail::to($user)->send(new OnHoldEmail());
        });

        if(count(Mail::failures()) > 0) {
            //The Mail::failures() will return an array of failed emails.
            foreach(Mail::failures() as $sent_status) { 
                $newData = \App\SentStatus::create(['email' => $sent_status]);
                $newData->save();
            }
        } 

        return response()->json(['success' => "Selected applicant(s) successfully on Hold."]);
    }

    public function showMother($id)
    {
        $mother = \App\MotherRegistrars::withTrashed()->find($id);
        return view('mother-registrars.show', compact('mother'));
    }

}
