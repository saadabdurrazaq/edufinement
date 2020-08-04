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
use Illuminate\Support\Facades\Input;
use Carbon\Carbon;
use Config;

class StudentRegistrarsController extends Controller
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
        return view('student-registrars.register');
    }

    public function show($id)
    {
        $user = \App\StudentRegistrars::withTrashed()->find($id);
        return view('student-registrars.show', compact('user'));
    }
 
    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function index(Request $request) {
        $filterKeyword = $request->get('keyword');
        $status = $request->get('status');

        $eligibleStatus = \App\StudentRegistrars::where('status', "Eligible")->count();
        $activeStatus = \App\StudentRegistrars::where('status', "Qualified")->count();
        $inactiveStatus = \App\StudentRegistrars::where('status', "Rejected")->count();
        $countTrash = \App\StudentRegistrars::onlyTrashed()->count();
        $countPending = \App\StudentRegistrars::where('status', "Pending")->count();

        $count = \App\StudentRegistrars::count();
        $items = $request->items ?? 5;
        $page    = $request->has('name') ? $request->get('name') : 1;
        $showingTotal  = $page * $items;
        $admins= \App\StudentRegistrars::paginate($items);
        $showingStarted = $admins->currentPage(); 

        $showData = "Showing $showingStarted to $showingTotal of $count";

        if($status) {
            $data = \App\StudentRegistrars::where('status', $status)->paginate($items);
        } else {
            $data = \App\StudentRegistrars::paginate($items);
        }

        if($filterKeyword) {
            if($status) {
                $data = \App\StudentRegistrars::where('name', 'LIKE', "%$filterKeyword%")->orWhere('email', 'LIKE', "%$filterKeyword%")->where('status', $status)->paginate($items);
            } else {
                $data = \App\StudentRegistrars::where('name', 'LIKE', "%$filterKeyword%")->orWhere('email', 'LIKE', "%$filterKeyword%")->paginate($items);
            }
        } 
    
        return view('student-registrars.index', compact('data'))->with(array('countPending' => $countPending, 'showData' => $showData, 'count' => $count, 'activeStatus' => $activeStatus, 'eligibleStatus' => $eligibleStatus, 'inactiveStatus' => $inactiveStatus, 'countTrash' => $countTrash))->withItems($items); //admin mengacu ke table admin di phpmyadmin
    }

    public function destroy($id) { 
        $student = \App\StudentRegistrars::find($id);

        $book = \App\StudentRegistrars::findOrFail($id);
        $book->delete();

        return redirect()->route('student-registrars.index')->with('success','Applicant deleted successfully');
    }

    public function destroyMultiple(Request $request) { 
        $get_ids = $request->ids;
        $ids = explode(',', $get_ids);

        $users = \App\StudentRegistrars::whereIn('id', $ids);
        
        $users->delete(); 

        return response()->json(['success' => "Applicants successfully moved to trash."]);
    }

    public function trash(Request $request) {
        $filterKeyword = $request->get('keyword');
        $status = $request->get('status');

        $count = \App\StudentRegistrars::count();
        $eligibleStatus = \App\StudentRegistrars::where('status', "Eligible")->count();
        $activeStatus = \App\StudentRegistrars::where('status', "Qualified")->count();
        $inactiveStatus = \App\StudentRegistrars::where('status', "Rejected")->count();
        $countTrash = \App\StudentRegistrars::onlyTrashed()->count();
        $countPending = \App\StudentRegistrars::where('status', "Pending")->count();

        $count = \App\StudentRegistrars::count();
        $items = $request->items ?? 5;
        $page    = $request->has('name') ? $request->get('name') : 1;
        $showingTotal  = $page * $items;
        $admins= \App\StudentRegistrars::paginate($items);
        $showingStarted = $admins->currentPage(); 

        $showData = "Showing $showingStarted to $showingTotal of $countTrash";

        if($status) {
            $data = \App\StudentRegistrars::where('status', $status)->paginate($items);
        } else {
            $data = \App\StudentRegistrars::onlyTrashed()->paginate($items);
        }

        if($filterKeyword) {
            if($status) {
                $data = \App\StudentRegistrars::onlyTrashed()->where('name', 'LIKE', "%$filterKeyword%")->orWhere('email', 'LIKE', "%$filterKeyword%")->where('status', $status)->paginate($items);
            } else {
                $data = \App\StudentRegistrars::onlyTrashed()->where('name', 'LIKE', "%$filterKeyword%")->orWhere('email', 'LIKE', "%$filterKeyword%")->paginate($items);
            }
        } 

        return view('student-registrars.trash', compact('data'))->with(array('countPending' => $countPending, 'showData' => $showData, 'count' => $count, 'eligibleStatus' => $eligibleStatus, 'activeStatus' => $activeStatus, 'inactiveStatus' => $inactiveStatus, 'countTrash' => $countTrash))->withItems($items); //admin mengacu ke table admin di phpmyadmin
    }

    public function restore($id) {
        $student = \App\StudentRegistrars::withTrashed()->findOrFail($id);

        foreach($student->father_registrars as $getFatherData) {
            $fatherID = $getFatherData->id;
            $updateFather = \App\FatherRegistrars::where('id', $fatherID);
            $updateFather->restore();

            $fatherUsername = $getFatherData->username;
            $fatherCredential = \App\User::where('username', $fatherUsername);
            $fatherCredential->restore(); 
        }

        foreach($student->mother_registrars as $getMotherData) {
            $motherID = $getMotherData->id;
            $updateMother = \App\MotherRegistrars::where('id', $motherID); 
            $updateMother->restore();

            $motherUsername = $getMotherData->username;
            $motherCredential = \App\User::where('username', $motherUsername);
            $motherCredential->restore(); 
        }

        $selectedUser = \App\User::where('username', function($query) use ($id) { //retrieve a collection of users from users table whereIn usernames. (continue below)
            $query->select('username')->from('student_registrars')->where('id', $id); //$query(whereIn usernames in table users) like selected usernames in applicants table. (To get selected usernames in applicants table, use whereIn('id', $ids) parameter.)
        });

        if($selectedUser) {
            $selectedUser->restore(); 
        } 

        $student->restore();
      
        return redirect()->route('student-registrars.trash')->with('status', 'Applicant successfully restored');
    }

    public function restoreMultiple(Request $request) {
        $get_ids = $request->ids;
        $ids = explode(',', $get_ids);

        $users = \App\StudentRegistrars::whereIn('id', $ids);

        //get father registrars who have students within ids
        $getBulkFather = \App\FatherRegistrars::whereHas('student_registrars', function($q) use($ids) {
            $q->whereIn('student_registrars.id', $ids);
        });

        //get mother registrars who have students within ids
        $getBulkMother = \App\MotherRegistrars::whereHas('student_registrars', function($q) use($ids) {
            $q->whereIn('student_registrars.id', $ids);
        });

        $getBulkFather->restore();
        $getBulkMother->restore();
        $users->restore();
        
        return response()->json(['success' => "Applicants successfully restored"]);
    }

    public function deletePermanent($id) {
        $student = \App\StudentRegistrars::withTrashed()->findOrFail($id);
        $specificStudent = \App\StudentRegistrars::withTrashed()->where('id', $id);
        
        if($student->status == 'Eligible') {
            foreach($student->father_registrars as $getFatherData) {
                $fatherID = $getFatherData->id;
                $fatherUsername = $getFatherData->username;

                $studentAccount = \App\User::where('username', function($query) use($id) { //retrieve a collection of users from users table where username in table users. (continue below)
                    $query->select('username')->from('student_registrars')->where('id', $id); //$query(where usernames in table users) like selected usernames in student_registrars table. (To get selected usernames in applicants table, use where('id', $id) parameter.)
                }); 
    
                if($studentAccount) {
                    $studentAccount->forceDelete(); 
                }

                $totalFatherChilderns = \App\StudentRegistrars::withTrashed()->whereHas('father_registrars', function($q) use($fatherID) {
                    $q->where('father_registrars.id', $fatherID);
                })->count(); 

                if($totalFatherChilderns == 1) {
                    $fatherCredential = \App\FatherRegistrars::where('username', $fatherUsername);
                    if($fatherCredential) {
                        $fatherCredential->delete(); 
                        $fatherCredential->forceDelete(); 
                    }
                }

            }

            foreach($student->mother_registrars as $getMotherData) {
                $motherID = $getMotherData->id;
                $motherUsername = $getMotherData->username;

                $studentAccount = \App\User::where('username', function($query) use($id) { //retrieve a collection of users from users table where username in table users. (continue below)
                    $query->select('username')->from('student_registrars')->where('id', $id); //$query(where usernames in table users) like selected usernames in student_registrars table. (To get selected usernames in applicants table, use where('id', $id) parameter.)
                }); 

                if($studentAccount) {
                    $studentAccount->forceDelete(); 
                }

                $totalMotherChilderns = \App\StudentRegistrars::withTrashed()->whereHas('mother_registrars', function($q) use($motherID) {
                    $q->where('mother_registrars.id', $motherID);
                })->count();     
        
                if($totalMotherChilderns == 1) {
                    $motherCredential = \App\MotherRegistrars::where('username', $motherUsername);
                    if($motherCredential) {
                        $motherCredential->delete();
                        $motherCredential->forceDelete();
                    }
                }
                
            }

        }
        else if($student->status == 'Qualified') {
            foreach($student->father_registrars as $getFatherData) {
                $fatherID = $getFatherData->id;
                $fatherUsername = $getFatherData->username;

                $countQualifiedStudent = \App\StudentRegistrars::withTrashed()->where('status', 'Qualified')->whereHas('father_registrars', function($q) use($fatherID) {
                    $q->where('father_registrars.id', $fatherID);
                })->count();

                if($countQualifiedStudent == 1) {
                    $fatherAccount = \App\User::where('username', $fatherUsername);
                    if($fatherAccount) {
                        $fatherAccount->forceDelete();  
                    } 
                }

                $studentAccount = \App\User::where('username', function($query) use($id) { //retrieve a collection of users from users table where username in table users. (continue below)
                    $query->select('username')->from('student_registrars')->where('id', $id); //$query(where usernames in table users) like selected usernames in student_registrars table. (To get selected usernames in applicants table, use where('id', $id) parameter.)
                }); 
    
                if($studentAccount) {
                    $studentAccount->forceDelete(); 
                }

                $totalFatherChilderns = \App\StudentRegistrars::withTrashed()->whereHas('father_registrars', function($q) use($fatherID) {
                    $q->where('father_registrars.id', $fatherID);
                })->count(); 

                if($totalFatherChilderns == 1) {
                    $fatherCredential = \App\FatherRegistrars::where('username', $fatherUsername);
                    if($fatherCredential) {
                        $fatherCredential->delete();
                        $fatherCredential->forceDelete(); 
                    }
                }

            }

            foreach($student->mother_registrars as $getMotherData) {
                $motherID = $getMotherData->id;
                $motherUsername = $getMotherData->username;

                $countQualifiedStudent2 = \App\StudentRegistrars::withTrashed()->where('status', 'Qualified')->whereHas('mother_registrars', function($q) use($motherID) {
                    $q->where('mother_registrars.id', $motherID);
                })->count();

                if($countQualifiedStudent2 == 1) {
                    $motherAccount = \App\User::where('username', $motherUsername);
                    if($motherAccount) {
                        $motherAccount->forceDelete();  
                    } 
                }

                $studentAccount = \App\User::where('username', function($query) use($id) { //retrieve a collection of users from users table where username in table users. (continue below)
                    $query->select('username')->from('student_registrars')->where('id', $id); //$query(where usernames in table users) like selected usernames in student_registrars table. (To get selected usernames in applicants table, use where('id', $id) parameter.)
                }); 
    
                if($studentAccount) {
                    $studentAccount->forceDelete(); 
                }

                $totalMotherChilderns = \App\StudentRegistrars::withTrashed()->whereHas('mother_registrars', function($q) use($motherID) {
                    $q->where('mother_registrars.id', $motherID);
                })->count();     
        
                if($totalMotherChilderns == 1) {
                    $motherCredential = \App\MotherRegistrars::where('username', $motherUsername);
                    if($motherCredential) {
                        $motherCredential->delete();
                        $motherCredential->forceDelete(); 
                    }
                }

            }

        }

        $student->father_registrars()->detach();
        $student->mother_registrars()->detach(); 
        $student->forceDelete();
      
        return redirect()->route('student-registrars.trash')->with('status', 'User successfully deleted permanently');
    }

    public function deleteMultiple(Request $request) {
        $get_ids = $request->ids;
        $ids = explode(',', $get_ids); 

        $students = \App\StudentRegistrars::withTrashed()->whereIn('id', $ids)->get();
        $deleteStudent = \App\StudentRegistrars::withTrashed()->whereIn('id', $ids);

        $deleteStudent->update(['status' => 'Pending']); 

        //////////////////////////////////////////

        $fatherUsername = \App\FatherRegistrars::select('username')->whereHas('student_registrars', function($q) use($ids) {
            $q->whereIn('student_registrars.id', $ids);
        })->get();
        $motherUsername = \App\MotherRegistrars::select('username')->whereHas('student_registrars', function($q) use($ids) {
            $q->whereIn('student_registrars.id', $ids);  
        })->get();

        $getQualifiedFatherStudent = \App\FatherRegistrars::withTrashed()->whereIn('username', $fatherUsername)->doesntHave('student_registrars', 'and', function($q) {
            $q->where('student_registrars.status', 'Qualified');
        })->select('username')->get(); 
        $getQualifiedMotherStudent = \App\MotherRegistrars::withTrashed()->whereIn('username', $motherUsername)->doesntHave('student_registrars', 'and', function($q) {
            $q->where('student_registrars.status', 'Qualified');
        })->select('username')->get();

        //count exist specific students in table users
        $selectedUsers = \App\User::whereIn('username', function($query) use($ids) { //retrieve a collection of users from users table whereIn usernames. (continue below)
            $query->select('username')->from('student_registrars')->whereIn('id', $ids); //$query(whereIn usernames in table users) like selected usernames in applicants table. (To get selected usernames in applicants table, use whereIn('id', $ids) parameter.)
        });

        $UserFather = \App\User::whereIn('username', $getQualifiedFatherStudent);
        $UserMother = \App\User::whereIn('username', $getQualifiedMotherStudent);

        //////////////////////////////////////////

        if($UserFather) {
            $UserFather->forceDelete(); 
        } 

        if($UserMother) {
            $UserMother->forceDelete(); 
        }

        if($selectedUsers) {
            $selectedUsers->forceDelete(); //prevent duplicate data in users table
        }
        
        /////////////////////////////////////////

        foreach($students as $student) {
            $student->father_registrars()->detach();
            $student->mother_registrars()->detach(); 
        }
    
        //Get selected father and mother who doesn't have qualified student registrars
        $getFatherStudent = \App\FatherRegistrars::whereIn('username', $fatherUsername)->doesntHave('student_registrars')->select('username')->get(); 
        $getMotherStudent = \App\MotherRegistrars::whereIn('username', $motherUsername)->doesntHave('student_registrars')->select('username')->get();

        $fatherCredential = \App\FatherRegistrars::whereIn('username', $getFatherStudent);
        $motherCredential = \App\MotherRegistrars::whereIn('username', $getMotherStudent);

        //////////////////////////////////////////

        if($fatherCredential) {
            $fatherCredential->forceDelete(); 
        } 

        if($motherCredential) {
            $motherCredential->forceDelete(); 
        }

        $deleteStudent->forceDelete();

        //////////////////////////////////////////

        return response()->json(['success' => "Applicants successfully permanently deleted"]);
    }

    public function pending(Request $request) {
        $filterKeyword = $request->get('keyword');
        $status = $request->get('status');

        $count = \App\StudentRegistrars::count();
        $eligibleStatus = \App\StudentRegistrars::where('status', "Eligible")->count();
        $activeStatus = \App\StudentRegistrars::where('status', "Qualified")->count();
        $inactiveStatus = \App\StudentRegistrars::where('status', "Rejected")->count();
        $countTrash = \App\StudentRegistrars::onlyTrashed()->count();
        $countPending = \App\StudentRegistrars::where('status', "Pending")->count();

        $count = \App\StudentRegistrars::count();
        $items = $request->items ?? 5;
        $page    = $request->has('name') ? $request->get('name') : 1;
        $showingTotal  = $page * $items;
        $admins= \App\StudentRegistrars::paginate($items);
        $showingStarted = $admins->currentPage(); 

        $showData = "Showing $showingStarted to $showingTotal of $countPending";

        if($status) {
            $data = \App\StudentRegistrars::where('status', $status)->paginate($items);
        } else {
            $data = \App\StudentRegistrars::where('status', "Pending")->paginate($items);
        }

        if($filterKeyword) {
            if($status) {
                $data = \App\StudentRegistrars::where('name', 'LIKE', "%$filterKeyword%")->orWhere('email', 'LIKE', "%$filterKeyword%")->where('status', $status)->paginate($items);
            } else {
                $data = \App\StudentRegistrars::where('name', 'LIKE', "%$filterKeyword%")->orWhere('email', 'LIKE', "%$filterKeyword%")->paginate($items);
            }
        } 

        return view('student-registrars.pending', compact('data'))->with(array('countPending' => $countPending, 'showData' => $showData, 'count' => $count, 'eligibleStatus' => $eligibleStatus, 'activeStatus' => $activeStatus, 'inactiveStatus' => $inactiveStatus, 'countTrash' => $countTrash))->withItems($items); //admin mengacu ke table admin di phpmyadmin
    }

    public function store(Request $request) 
    {
         $validation = \Validator::make($request->all(),[
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users', 'unique:father_registrars', 'unique:student_registrars'],
            'phone' => ['required', 'digits_between:10,12', 'unique:users', 'unique:father_registrars', 'unique:student_registrars'],
            'username' => ['required','min:5', 'max:20', 'unique:users', 'unique:father_registrars', 'unique:student_registrars', 'regex:/^\S*$/u'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'father-username' => 'min:5|required_without:guardianmale-username', 
            'account-key' => 'required_without:guardianmale-account-key', 
            'mother-username' => 'min:5|required_without:guardianfemale-username', 
            'mother-account-key' => 'required_without:guardianfemale-account-key', 
            'guardianmale-username' => 'min:5|required_without:father-username', 
            'guardianmale-account-key' => 'required_without:account-key', 
            'guardianfemale-username' => 'min:5|required_without:mother-username', 
            'guardianfemale-account-key' => 'required_without:mother-account-key', 
        ])->validate();  

        $model = \App\FatherRegistrars::where('username', $request->get('father-username'))->first();
        $authFather = $model && Hash::check($request->get('account-key'), $model->account_key, []);

        $motherUsername = \App\MotherRegistrars::where('username', $request->get('mother-username'))->first();
        $authMother = $motherUsername && Hash::check($request->get('mother-account-key'), $motherUsername->account_key, []);

        $guardianMaleIdentity = \App\GuardianMaleRegistrars::where('username', $request->get('guardianmale-username'))->first();
        $authGuardianMale = $guardianMaleIdentity && Hash::check($request->get('guardianmale-account-key'), $guardianMaleIdentity->account_key, []);

        $guardianFemaleIdentity = \App\GuardianFemaleRegistrars::where('username', $request->get('guardianfemale-username'))->first();
        $authGuardianFemale = $guardianFemaleIdentity && Hash::check($request->get('guardianfemale-account-key'), $guardianFemaleIdentity->account_key, []);

        $new_user = new \App\StudentRegistrars(); //Panggil model User
        $new_user->name = $request->get('name');
        $new_user->email = $request->get('email');
        $new_user->phone = $request->get('phone');
        $new_user->username = $request->get('username');
        $new_user->gender = $request->get('gender');
        $new_user->password = \Hash::make($request->get('password'));
        $new_user->registered_date = now();

        if($authFather && $authMother) {
            $new_user->save(); 

            $fatherName = \App\FatherRegistrars::where('name', $model->name)->first();
            $motherName = \App\MotherRegistrars::where('name', $motherUsername->name)->first();

            $fatherName->registered_date = now();  
            $fatherName->save();
            $motherName->registered_date = now();
            $motherName->save();

            $new_user->father_registrars()->attach($fatherName);
            $new_user->mother_registrars()->attach($motherName);

            //$new_user->notify(new MailForApplicant($new_user)); 

            return redirect()->route('student-regis')->with('status', 'Registration successfull. Thank you for registration! You will be notified if you are approved');
        } 
        else 
        if($authGuardianMale && $authGuardianFemale) {
            $new_user->save(); 

            $guardianMale = \App\GuardianMaleRegistrars::where('name', $guardianMaleIdentity->name)->first();
            $guardianFemale = \App\GuardianFemaleRegistrars::where('name', $guardianFemaleIdentity->name)->first();

            $guardianMale->registered_date = now();  
            $guardianMale->save();
            $guardianFemale->registered_date = now();
            $guardianFemale->save();

            $new_user->guardianmale_registrars()->attach($guardianMale);
            $new_user->guardianfemale_registrars()->attach($guardianFemale);

            //$new_user->notify(new MailForApplicant($new_user)); 

            return redirect()->route('student-regis')->with('status', 'Registration successfull. Thank you for registration! You will be notified if you are approved');
        } 
        else 
        if($authMother && $authGuardianMale) {
            $new_user->save(); 

            $motherName = \App\MotherRegistrars::where('name', $motherUsername->name)->first();
            $guardianMale = \App\GuardianMaleRegistrars::where('name', $guardianMaleIdentity->name)->first();

            $motherName->registered_date = now();  
            $motherName->save();
            $guardianMale->registered_date = now();
            $guardianMale->save();

            $new_user->mother_registrars()->attach($motherName);
            $new_user->guardianmale_registrars()->attach($guardianMale);
        
            //$new_user->notify(new MailForApplicant($new_user)); 

            return redirect()->route('student-regis')->with('status', 'Registration successfull. Thank you for registration! You will be notified if you are approved');
        } 
        else 
        if($authFather && $authGuardianFemale) {
            $new_user->save(); 

            $fatherName = \App\FatherRegistrars::where('name', $model->name)->first();
            $guardianFemale = \App\GuardianFemaleRegistrars::where('name', $guardianFemaleIdentity->name)->first();

            $fatherName->registered_date = now();  
            $fatherName->save();
            $guardianFemale->registered_date = now();
            $guardianFemale->save();

            $new_user->father_registrars()->attach($fatherName);
            $new_user->guardianfemale_registrars()->attach($guardianFemale);

            //$new_user->notify(new MailForApplicant($new_user)); 

            return redirect()->route('student-regis')->with('status', 'Registration successfull. Thank you for registration! You will be notified if you are approved');
        } 
        else
        if( ($authFather != $authMother) || ($authGuardianMale != $authGuardianFemale) || ($authMother != $authGuardianMale) || ($authFather != $authGuardianFemale) ) {
            return redirect()->route('student-regis')->with('warning', 'Sorry your father/mother/guardian usernames and his/her account key doesnt match at all. Please try again!');
        }
        
    }

    public function approve($id) {
        $new_student = \App\StudentRegistrars::where('id', $id);
        $new_student->update(['status' => 'Qualified', 'approved_date' => now()]);

        //delete student from users table to prevent duplicate
        $getSelectedUser = \App\User::where('username', function($query) use ($id) { //retrieve a collection of users from users table where username in table users. (continue below)
            $query->select('username')->from('student_registrars')->where('id', $id); //$query(where usernames in table users) like selected usernames in student_registrars table. (To get selected usernames in student_registrars table, use where('id', $id) parameter.)
        });

        if($getSelectedUser) {
            $getSelectedUser->forceDelete(); 
        } 

        $student = \App\StudentRegistrars::find($id);

        //clone student father to users table
        foreach($student->father_registrars as $getFatherData) {
            $fatherID = $getFatherData->id;
            $guardianMaleUsername = $getFatherData->username;
            $updateFather = \App\FatherRegistrars::where('id', $fatherID);
            $updateFather->update(['status' => 'Qualified', 'approved_date' => now()]);

            $getFather = \App\User::where('username', $getFatherData->username);
            if($getFather) {
               $getFather->forceDelete();
            }

            $isStudentExist = \App\StudentRegistrars::withTrashed()->where('status', 'Qualified')->whereHas('guardianmale_registrars', function($q) use($guardianMaleUsername) {
                $q->where('guardianmale_registrars.username', $guardianMaleUsername);
            })->count();

            $getFatherData->makeHidden(['status', 'id', 'account_key']);
            $replicaFatherData = $getFatherData->replicate();
            $fatherDatatoArray = $replicaFatherData->toArray();
            $father = \App\User::firstOrCreate($fatherDatatoArray);
            if($isStudentExist >= 1) {
                $father->assignRole(['Parent', 'Guardian']);
            } else {
                $father->assignRole('Parent');
            }
            $father->password = $getFatherData->password;
            $father->save();
            //$user->notify(new ApprovedApplicant($user));
        }

         //clone student mother to users table
        foreach($student->mother_registrars as $getMotherData) {
            $motherID = $getMotherData->id;
            $guardianFemaleUsername = $getMotherData->username;
            $updateMother = \App\MotherRegistrars::where('id', $motherID);
            $updateMother->update(['status' => 'Qualified', 'approved_date' => now()]);

            $getMother = \App\User::where('username', $getMotherData->username);
            if($getMother) {
               $getMother->forceDelete();
            }

            $isStudentExist = \App\StudentRegistrars::withTrashed()->where('status', 'Qualified')->whereHas('guardianfemale_registrars', function($q) use($guardianFemaleUsername) {
                $q->where('guardianfemale_registrars.username', $guardianFemaleUsername);
            })->count();

            $getMotherData->makeHidden(['status', 'id', 'account_key']);
            $replicaMotherData = $getMotherData->replicate();
            $motherDatatoArray = $replicaMotherData->toArray(); 
            $mother = \App\User::firstOrCreate($motherDatatoArray);
            if($isStudentExist >= 1) {
                $mother->assignRole(['Parent', 'Guardian']);
            } else {
                $mother->assignRole('Parent');
            }
            $mother->password = $getMotherData->password;
            $mother->save();
            //$user->notify(new ApprovedApplicant($user));
        }

        //clone student guardian male to users table
        foreach($student->guardianmale_registrars as $getGuardianMaleData) {
            $guardianMaleID = $getGuardianMaleData->id;
            $guardianMaleUsername = $getGuardianMaleData->username;
            $updateGuardianMale = \App\GuardianMaleRegistrars::where('id', $guardianMaleID);
            $updateGuardianMale->update(['status' => 'Qualified', 'approved_date' => now()]);

            $guardianMale = \App\User::where('username', $getGuardianMaleData->username);
            if($guardianMale) {
               $guardianMale->forceDelete();
            } 

            $isStudentExist = \App\StudentRegistrars::withTrashed()->where('status', 'Qualified')->whereHas('father_registrars', function($q) use($guardianMaleUsername) {
                $q->where('father_registrars.username', $guardianMaleUsername);
            })->count();

            $getGuardianMaleData->makeHidden(['status', 'id', 'account_key']);
            $replicaGuardianMaleData = $getGuardianMaleData->replicate();
            $guardianMaleDatatoArray = $replicaGuardianMaleData->toArray(); 
            $guardianMale = \App\User::firstOrCreate($guardianMaleDatatoArray);
            if($isStudentExist >= 1) {
                $guardianMale->assignRole(['Parent', 'Guardian']);
            } else {
                $guardianMale->assignRole('Guardian');
            }
            $guardianMale->password = $getGuardianMaleData->password;
            $guardianMale->save();
        }

        //clone student guardian female to users table
        foreach($student->guardianfemale_registrars as $getGuardianFemaleData) {
            $guardianFemaleID = $getGuardianFemaleData->id;
            $guardianFemaleUsername = $getGuardianFemaleData->username;
            $updateGuardianFemale = \App\GuardianFemaleRegistrars::where('id', $guardianFemaleID);
            $updateGuardianFemale->update(['status' => 'Qualified', 'approved_date' => now()]);

            $guardianFemale = \App\User::where('username', $getGuardianFemaleData->username);
            if($guardianFemale) {
               $guardianFemale->forceDelete();
            } 

            $isStudentExist = \App\StudentRegistrars::withTrashed()->where('status', 'Qualified')->whereHas('mother_registrars', function($q) use($guardianFemaleUsername) {
                $q->where('mother_registrars.username', $guardianFemaleUsername);
            })->count();

            $getGuardianFemaleData->makeHidden(['status', 'id', 'account_key']);
            $replicaGuardianFemaleData = $getGuardianFemaleData->replicate();
            $guardianFemaleDatatoArray = $replicaGuardianFemaleData->toArray(); 
            $guardianFemale = \App\User::firstOrCreate($guardianFemaleDatatoArray);
            if($isStudentExist >= 1) {
                $guardianFemale->assignRole(['Parent', 'Guardian']);
            } else {
                $guardianFemale->assignRole('Guardian');
            }
            $guardianFemale->password = $getGuardianFemaleData->password;
            $guardianFemale->save();
            //$user->notify(new ApprovedApplicant($user));
        }

        //clone student to users table
        $find_one = \App\StudentRegistrars::where('id', $id)->firstOrFail();
        $find_one->makeHidden(['status', 'id', 'email_sent']);
        $new_user = $find_one->replicate();
        $new_user = $find_one->toArray();

        $user = \App\User::firstOrCreate($new_user);
        $user->assignRole('Student');
        $user->password = $find_one->password;
        $user->save();
        //$user->notify(new ApprovedApplicant($user)); 
      
        return redirect()->route('student-registrars.showeligible')->with('status', 'User successfully approved');
    }

    public function approveMultiple(Request $request) {
        $get_ids = $request->ids;
        $ids = explode(',', $get_ids); 

        //clone student registrars within ids
        $find_selected = \App\StudentRegistrars::whereIn('id', $ids)->get();
        $find_selected->makeHidden(['status', 'id', 'email_sent']);
        $find_selected->makeVisible(['password']);
        $new_students = $find_selected->toArray();  

        //clone father registrars who has students within ids
        $cloneFathers = \App\FatherRegistrars::whereHas('student_registrars', function($q) use($ids) {
            $q->whereIn('student_registrars.id', $ids);
        })->get();
        $cloneFathers->makeHidden(['status', 'id', 'email_sent']);
        $cloneFathers->makeVisible(['password']);
        $new_fathers = $cloneFathers->toArray();

        //clone mother registrars who has students within ids
        $cloneMothers = \App\MotherRegistrars::whereHas('student_registrars', function($q) use($ids) {
            $q->whereIn('student_registrars.id', $ids);
        })->get();
        $cloneMothers->makeHidden(['status', 'id', 'email_sent']);
        $cloneMothers->makeVisible(['password']);
        $new_mothers = $cloneMothers->toArray();

        //clone guardian male registrars who has students within ids
        $cloneGuardianMales = \App\GuardianMaleRegistrars::whereHas('student_registrars', function($q) use($ids) {
            $q->whereIn('student_registrars.id', $ids);
        })->get();
        $cloneGuardianMales->makeHidden(['status', 'id', 'email_sent']);
        $cloneGuardianMales->makeVisible(['password']);
        $new_GuardianMales = $cloneGuardianMales->toArray();

        //clone guardian female registrars who has students within ids
        $cloneGuardianFemales = \App\GuardianFemaleRegistrars::whereHas('student_registrars', function($q) use($ids) {
            $q->whereIn('student_registrars.id', $ids);
        })->get();
        $cloneGuardianFemales->makeHidden(['status', 'id', 'email_sent']);
        $cloneGuardianFemales->makeVisible(['password']);
        $new_GuardianFemales = $cloneGuardianFemales->toArray();

        //get student registrars within ids
        $students = \App\StudentRegistrars::whereIn('id', $ids);

        //get father registrars who has students within ids
        $getBulkFather = \App\FatherRegistrars::whereHas('student_registrars', function($q) use($ids) {
            $q->whereIn('student_registrars.id', $ids);
        });

        //get mother registrars who has students within ids
        $getBulkMother = \App\MotherRegistrars::whereHas('student_registrars', function($q) use($ids) {
            $q->whereIn('student_registrars.id', $ids);
        });

        //////////////////////////////////////////
 
        $getBulkFather->update(['status' => 'Qualified']); 
        $getBulkMother->update(['status' => 'Qualified']); 
        $students->update(['status' => 'Qualified', 'approved_date' => now()]);
        
        //////////////////////////////////////////

        $isStudentsExist = \App\User::whereIn('username', function($query) use($ids) { //retrieve a collection of users from users table whereIn usernames. (continue below)
            $query->select('username')->from('student_registrars')->whereIn('id', $ids); //$query(whereIn usernames in table users) like selected usernames in student_registrars table. (To get selected usernames in applicants table, use whereIn('id', $ids) parameter.)
        });

        //Insert new students
        if($isStudentsExist) {
           $isStudentsExist->forceDelete(); //prevent duplicate data in users table
           $bulkUsers = \App\User::insert($new_students);
        } else {
            $bulkUsers = \App\User::insert($new_students);
        }

        $getSelectedUsers = \App\User::whereIn('username', function($query) use($ids) { //retrieve a collection of users from users table whereIn usernames. (continue below)
            $query->select('username')->from('student_registrars')->whereIn('id', $ids); //$query(whereIn usernames in table users) like selected usernames in applicants table. (To get selected usernames in applicants table, use whereIn('id', $ids) parameter.)
        })->get();
        
        foreach($getSelectedUsers as $user) {
            $user->assignRole('Student');
            $user->save();
        } 

        //////////////////////////////////////////

        $getFatherUsername = \App\FatherRegistrars::select('username')->whereHas('student_registrars', function($q) use($ids) {
            $q->whereIn('student_registrars.id', $ids);
        })->get();

        $isFathersExist = \App\User::whereIn('username', function($query) use($getFatherUsername) { //retrieve a collection of users from users table whereIn usernames. (continue below)
            $query->select('username')->from('father_registrars')->whereIn('username', $getFatherUsername); //$query(whereIn usernames in table users) like selected usernames in father_registrars table. (To get selected usernames in father_registrars table, use whereIn('id', $ids) parameter.)
        });

        //Insert new fathers
        if($isFathersExist) {
            $isFathersExist->forceDelete(); //prevent duplicate data in users table
            $bulkUsers = \App\User::insert($new_fathers);
         } else {
             $bulkUsers = \App\User::insert($new_fathers);
         }
 
         $getSelectedFathers = \App\User::whereIn('username', function($query) use($getFatherUsername) { //retrieve a collection of users from users table whereIn usernames. (continue below)
             $query->select('username')->from('father_registrars')->whereIn('username', $getFatherUsername); //$query(whereIn usernames in users table) like selected usernames in father_registrars table. (To get selected usernames in applicants table, use whereIn('id', $ids) parameter.)
         })->get();

        //check if father has qualified adopted childern (students) (PROBLEM LAYS HERE)

        //retrieve a qualified student registrars username who has guardian male username like $getFatherUsername
        $studentsUsername = \App\StudentRegistrars::withTrashed()->where('status', 'Qualified')->whereHas('guardianmale_registrars', function($q) use($getFatherUsername) {
            $q->whereIn('guardianmale_registrars.username', $getFatherUsername);
        })->select('username')->get();

        $studentsCredential = \App\StudentRegistrars::whereIn('username', $studentsUsername); //if($studentsCredential == 0), give role as a parent only*/

        //retrieve guardian male username who doesnt have qualified student registrars (adopted childerns)
        /* $guardianMaleUsername = \App\GuardianMaleRegistrars::withTrashed()->whereIn('username', $getFatherUsername)->doesntHave('student_registrars', 'and', function($q) {
            $q->where('student_registrars.status', 'Qualified');
        })->select('username')->get();

        //retrieve a guardian male (who doesnt have a qualified student registrars (childerns)). If the result is zero, give role as a parent only. 
        $guardianMaleCredential = \App\User::whereIn('username', $guardianMaleUsername); //if($guardianMaleCredential) is not found, give role as a parent only */
        
        if($studentsCredential) { 
            foreach($getSelectedFathers as $user) {
                $user->assignRole('Parent');
                $user->save();
            }
        } else {
            foreach($getSelectedFathers as $user) {
                $user->assignRole(['Parent', 'Guardian']);
                $user->save();
            }
        }

        //////////////////////////////////////////

        $getGuardianMaleUsername = \App\GuardianMaleRegistrars::select('username')->whereHas('student_registrars', function($q) use($ids) {
            $q->whereIn('student_registrars.id', $ids);
        })->get();

        $isGuardianMalesExist = \App\User::whereIn('username', function($query) use($getGuardianMaleUsername) { //retrieve a collection of users from users table whereIn usernames. (continue below)
            $query->select('username')->from('guardianmale_registrars')->whereIn('username', $getGuardianMaleUsername); //$query(whereIn usernames in table users) like selected usernames in father_registrars table. (To get selected usernames in father_registrars table, use whereIn('id', $ids) parameter.)
        });

        //Insert new guardian male
        if($isGuardianMalesExist) {
            $isGuardianMalesExist->forceDelete(); //prevent duplicate data in users table
            $bulkUsers = \App\User::insert($new_GuardianMales);
        } else {
            $bulkUsers = \App\User::insert($new_GuardianMales);
        }
 
        $getSelectedGuardianMales = \App\User::whereIn('username', function($query) use($getGuardianMaleUsername) { //retrieve a collection of users from users table whereIn usernames. (continue below)
            $query->select('username')->from('guardianmale_registrars')->whereIn('username', $getGuardianMaleUsername); //$query(whereIn usernames in table users) like selected usernames in applicants table. (To get selected usernames in applicants table, use whereIn('id', $ids) parameter.)
        })->get();

        //check if guardian male has qualified adopted childern (students) (PROBLEM LAYS HERE)

        //retrieve qualified student registrars username who has father username like $getGuardianMaleUsername
        $studentsFatherUsername = \App\StudentRegistrars::withTrashed()->where('status', 'Qualified')->whereHas('father_registrars', function($q) use($getGuardianMaleUsername) {
            $q->whereIn('father_registrars.username', $getGuardianMaleUsername);
        })->select('username')->get();

        $studentsFatherCredential = \App\StudentRegistrars::whereIn('username', $studentsFatherUsername); //if($studentsFatherCredential == 0) */

        //retrieve father username who doesnt have qualified student registrars (adopted childerns)
        /* $fatherUsername = \App\FatherRegistrars::withTrashed()->whereIn('username', $getGuardianMaleUsername)->doesntHave('student_registrars', 'and', function($q) {
            $q->where('student_registrars.status', 'Qualified');
        })->select('username')->get();

        //retrieve a fathers (who doesnt have a qualified student registrars (childerns)). If the result is zero, give role as a parent only. 
        $studentsFatherCredential = \App\User::whereIn('username', $fatherUsername); //if($studentsFatherCredential) */
        
        if($studentsFatherCredential) {  
            foreach($getSelectedGuardianMales as $user) {
                $user->assignRole('Guardian');
                $user->save();
            }
        } else {
            foreach($getSelectedGuardianMales as $user) {
                $user->assignRole(['Parent', 'Guardian']);
                $user->save();
            }
        }

        //////////////////////////////////////////

        $getMotherUsername = \App\MotherRegistrars::select('username')->whereHas('student_registrars', function($q) use($ids) {
            $q->whereIn('student_registrars.id', $ids);
        })->get();

        $isMothersExist = \App\User::whereIn('username', function($query) use($getMotherUsername) { //retrieve a collection of users from users table whereIn usernames. (continue below)
            $query->select('username')->from('mother_registrars')->whereIn('username', $getMotherUsername); //$query(whereIn usernames in table users) like selected usernames in father_registrars table. (To get selected usernames in father_registrars table, use whereIn('id', $ids) parameter.)
        });

        //Insert new mothers
        if($isMothersExist) {
            $isMothersExist->forceDelete(); //prevent duplicate data in users table
            $bulkUsers = \App\User::insert($new_mothers);
         } else {
             $bulkUsers = \App\User::insert($new_mothers);
         }
 
         $getSelectedMothers = \App\User::whereIn('username', function($query) use($getMotherUsername) { //retrieve a collection of users from users table whereIn usernames. (continue below)
             $query->select('username')->from('mother_registrars')->whereIn('username', $getMotherUsername); //$query(whereIn usernames in table users) like selected usernames in applicants table. (To get selected usernames in applicants table, use whereIn('id', $ids) parameter.)
         })->get();

         //check if mother has qualified adopted childern (students) (PROBLEM LAYS HERE)

        //retrieve a qualified student registrars username who has guardian female username like $getMotherUsername
        $studentsGFUsername = \App\StudentRegistrars::withTrashed()->where('status', 'Qualified')->whereHas('guardianfemale_registrars', function($q) use($getMotherUsername) {
            $q->whereIn('guardianfemale_registrars.username', $getMotherUsername);
        })->select('username')->get();

        $studentsGFCredential = \App\StudentRegistrars::whereIn('username', $studentsGFUsername)->count(); //if($studentsGFCredential == 0), give role as a parent only*/

        //retrieve guardian female username who doesnt have qualified student registrars (adopted childerns)
        /* $guardianFemaleUsername = \App\GuardianFemaleRegistrars::withTrashed()->whereIn('username', $getMotherUsername)->doesntHave('student_registrars', 'and', function($q) {
            $q->where('student_registrars.status', 'Qualified');
        })->select('username')->get();

        //retrieve a guardian female (who doesnt have a qualified student registrars (adopted childerns)). If the result is zero, give role as a parent only. 
        $guardianFemaleCredential = \App\User::whereIn('username', $guardianFemaleUsername); //if($guardianFemaleCredential) is not found, give role as a parent only */
        
        if($studentsGFCredential == 0) {
            foreach($getSelectedMothers as $user) {
                $user->assignRole('Parent');
                $user->save();
            }
        } else {
            foreach($getSelectedMothers as $user) {
                $user->assignRole(['Parent', 'Guardian']);
                $user->save();
            }
        }

        //////////////////////////////////////////

        $getGuardianFemaleUsername = \App\GuardianFemaleRegistrars::select('username')->whereHas('student_registrars', function($q) use($ids) {
            $q->whereIn('student_registrars.id', $ids);
        })->get();

        $isGuardianFemalesExist = \App\User::whereIn('username', function($query) use($getGuardianFemaleUsername) { //retrieve a collection of users from users table whereIn usernames. (continue below)
            $query->select('username')->from('guardianfemale_registrars')->whereIn('username', $getGuardianFemaleUsername); //$query(whereIn usernames in table users) like selected usernames in father_registrars table. (To get selected usernames in father_registrars table, use whereIn('id', $ids) parameter.)
        });

        //Insert new guardian female
        if($isGuardianFemalesExist) {
            $isGuardianFemalesExist->forceDelete(); //prevent duplicate data in users table
            $bulkUsers = \App\User::insert($new_GuardianFemales);
            //$bulkUsers = \App\User::insert($new_mothers);
        } else {
            $bulkUsers = \App\User::insert($new_GuardianFemales);
        }
 
        $getSelectedGuardianFemales = \App\User::whereIn('username', function($query) use($getGuardianFemaleUsername) { //retrieve a collection of users from users table whereIn usernames. (continue below)
            $query->select('username')->from('guardianfemale_registrars')->whereIn('username', $getGuardianFemaleUsername); //$query(whereIn usernames in table users) like selected usernames in applicants table. (To get selected usernames in applicants table, use whereIn('id', $ids) parameter.)
        })->get();

        //check if mother has qualified adopted childern (students) (PROBLEM LAYS HERE)

        //retrieve qualified student registrars username who has mother username like $getGuardianFemaleUsername
        $studentsMotherUsername = \App\StudentRegistrars::withTrashed()->where('status', 'Qualified')->whereHas('mother_registrars', function($q) use($getGuardianFemaleUsername) {
            $q->whereIn('mother_registrars.username', $getGuardianFemaleUsername);
        })->select('username')->get();

        $studentsMotherCredential = \App\StudentRegistrars::whereIn('username', $studentsMotherUsername)->count();

        //retrieve mother username who doesnt have qualified student registrars (adopted childerns)
        /* $motherUsername = \App\MotherRegistrars::withTrashed()->whereIn('username', $getGuardianFemaleUsername)->doesntHave('student_registrars', 'and', function($q) {
            $q->where('student_registrars.status', 'Qualified');
        })->select('username')->get();

        //retrieve a fathers (who doesnt have a qualified student registrars ( adoptedchilderns)). If the result is zero, give role as a parent only. 
        $studentsMotherCredential = \App\User::whereIn('username', $motherUsername); //if($studentsFemtherCredential) */
        
        if($studentsMotherCredential == 0) {
            foreach($getSelectedGuardianFemales as $user) {
                $user->assignRole('Guardian');
                $user->save();
            }
        } else {
            foreach($getSelectedGuardianFemales as $user) {
                $user->assignRole(['Parent', 'Guardian']);
                $user->save();
            }
        }
         
        //////////////////////////////////////////

        /* $users->each(function($user) {
            Mail::to($user)->send(new SendEmail());
        });

        if(count(Mail::failures()) > 0) {
            //The Mail::failures() will return an array of failed emails.
            foreach(Mail::failures() as $sent_status) { 
                $newData = \App\SentStatus::create(['email' => $sent_status]);
                $newData->save();
            }
        } */

        return response()->json(['success' => "Selected applicant(s) successfully approved."]);
    }

    public function showEligible(Request $request) {
        $filterKeyword = $request->get('keyword');
        $status = $request->get('status');

        $count = \App\StudentRegistrars::count();
        $eligibleStatus = \App\StudentRegistrars::where('status', "Eligible")->count();
        $activeStatus = \App\StudentRegistrars::where('status', "Qualified")->count();
        $inactiveStatus = \App\StudentRegistrars::where('status', "Rejected")->count();
        $countTrash = \App\StudentRegistrars::onlyTrashed()->count();
        $countPending = \App\StudentRegistrars::where('status', "Pending")->count();

        $count = \App\StudentRegistrars::count();
        $items = $request->items ?? 5;
        $page    = $request->has('name') ? $request->get('name') : 1;
        $showingTotal  = $page * $items;
        $admins= \App\StudentRegistrars::paginate($items);
        $showingStarted = $admins->currentPage(); 

        $showData = "Showing $showingStarted to $showingTotal of $countPending";

        if($status) {
            $data = \App\StudentRegistrars::where('status', $status)->paginate($items);
        } else {
            $data = \App\StudentRegistrars::where('status', "Eligible")->paginate($items);
        }

        if($filterKeyword) {
            if($status) {
                $data = \App\StudentRegistrars::where('name', 'LIKE', "%$filterKeyword%")->orWhere('email', 'LIKE', "%$filterKeyword%")->where('status', $status)->paginate($items);
            } else {
                $data = \App\StudentRegistrars::where('name', 'LIKE', "%$filterKeyword%")->orWhere('email', 'LIKE', "%$filterKeyword%")->paginate($items);
            }
        } 

        return view('student-registrars.show-eligible', compact('data'))->with(array('countPending' => $countPending, 'showData' => $showData, 'count' => $count, 'eligibleStatus' => $eligibleStatus, 'activeStatus' => $activeStatus, 'inactiveStatus' => $inactiveStatus, 'countTrash' => $countTrash))->withItems($items); //admin mengacu ke table admin di phpmyadmin
    }

    public function eligible($id) {
        $new_student = \App\StudentRegistrars::where('id', $id);
        $new_student->update(['status' => 'Eligible', 'received_date' => now()]);

        //replicate the data to users table

        $student = \App\StudentRegistrars::find($id);

        foreach($student->father_registrars as $getFatherData) {
            $fatherID = $getFatherData->id;
            $updateFather = \App\FatherRegistrars::where('id', $fatherID);
            $updateFather->update(['status' => 'Eligible', 'received_date' => now()]);
            //$user->notify(new ApprovedApplicant($user));
        }

        foreach($student->mother_registrars as $getMotherData) {
            $motherID = $getMotherData->id;
            $updateMother = \App\MotherRegistrars::where('id', $motherID);
            $updateMother->update(['status' => 'Eligible', 'received_date' => now()]);
            //$user->notify(new ApprovedApplicant($user));
        }

        $find_one = \App\StudentRegistrars::where('id', $id)->firstOrFail();
        $find_one->makeHidden(['status', 'id', 'email_sent']);
        $new_user = $find_one->replicate();
        $new_user = $find_one->toArray();

        $user = \App\User::firstOrCreate($new_user);
        $user->assignRole('Student Registrars');
        $user->password = $find_one->password;
        $user->save();
        //$user->notify(new ApprovedApplicant($user)); 
      
        return redirect()->route('student-registrars.pending')->with('status', 'User eligible for take a test');
    }

    public function eligibleMultiple(Request $request) {
        $get_ids = $request->ids;
        $ids = explode(',', $get_ids); 

        $students = \App\StudentRegistrars::whereIn('id', $ids);

        $getStudents = \App\StudentRegistrars::whereIn('id', $ids)->get();
        $getStudents->makeHidden(['status', 'id', 'email_sent']);
        $getStudents->makeVisible(['password']);
        $replicate_students = $getStudents->toArray();

        //get father registrars who have students within ids
        $getBulkFather = \App\FatherRegistrars::whereHas('student_registrars', function($q) use($ids) {
            $q->whereIn('student_registrars.id', $ids);
        });

        //get mother registrars who have students within ids
        $getBulkMother = \App\MotherRegistrars::whereHas('student_registrars', function($q) use($ids) {
            $q->whereIn('student_registrars.id', $ids);
        });

        $selectedUsers = \App\User::whereIn('username', function($query) use($ids) { //retrieve a collection of users from users table whereIn usernames. (continue below)
            $query->select('username')->from('student_registrars')->whereIn('id', $ids); //$query(whereIn usernames in table users) like selected usernames in applicants table. (To get selected usernames in applicants table, use whereIn('id', $ids) parameter.)
        });

        $students->update(['status' => 'Eligible', 'received_date' => now()]); 
        $getBulkFather->update(['status' => 'Eligible']); 
        $getBulkMother->update(['status' => 'Eligible']); 

        if($selectedUsers) {
            $selectedUsers->forceDelete(); //prevent duplicate data in users table
            $bulkUsers = \App\User::insert($replicate_students);
         } else {
             $bulkUsers = \App\User::insert($replicate_students);
         }
 
         $getSelectedUsers = \App\User::whereIn('username', function($query) use($ids) { //retrieve a collection of users from users table whereIn usernames. (continue below)
             $query->select('username')->from('student_registrars')->whereIn('id', $ids); //$query(whereIn usernames in table users) like selected usernames in applicants table. (To get selected usernames in applicants table, use whereIn('id', $ids) parameter.)
         })->get();
         
         foreach($getSelectedUsers as $user) {
             $user->assignRole('Student Registrars');
             $user->save();
         }

        /* $students->each(function($user) {
            Mail::to($user)->send(new SendEmail());
        });

        if(count(Mail::failures()) > 0) {
            //The Mail::failures() will return an array of failed emails.
            foreach(Mail::failures() as $sent_status) { 
                $newData = \App\SentStatus::create(['email' => $sent_status]);
                $newData->save();
            }
        } */

        return response()->json(['success' => "Selected applicant(s) successfully approved."]);
    }

    public function showApproved(Request $request) {
        $filterKeyword = $request->get('keyword');
        $status = $request->get('status');

        $count = \App\StudentRegistrars::count();
        $eligibleStatus = \App\StudentRegistrars::where('status', "Eligible")->count();
        $activeStatus = \App\StudentRegistrars::where('status', "Qualified")->count();
        $inactiveStatus = \App\StudentRegistrars::where('status', "Rejected")->count();
        $countTrash = \App\StudentRegistrars::onlyTrashed()->count();
        $countPending = \App\StudentRegistrars::where('status', "Pending")->count();

        $count = \App\StudentRegistrars::count();
        $items = $request->items ?? 5;
        $page    = $request->has('name') ? $request->get('name') : 1;
        $showingTotal  = $page * $items;
        $admins= \App\StudentRegistrars::paginate($items);
        $showingStarted = $admins->currentPage(); 

        $showData = "Showing $showingStarted to $showingTotal of $countPending";

        if($status) {
            $data = \App\StudentRegistrars::where('status', $status)->paginate($items);
        } else {
            $data = \App\StudentRegistrars::where('status', "Qualified")->paginate($items);
        }

        if($filterKeyword) {
            if($status) {
                $data = \App\StudentRegistrars::where('name', 'LIKE', "%$filterKeyword%")->orWhere('email', 'LIKE', "%$filterKeyword%")->where('status', $status)->paginate($items);
            } else {
                $data = \App\StudentRegistrars::where('name', 'LIKE', "%$filterKeyword%")->orWhere('email', 'LIKE', "%$filterKeyword%")->paginate($items);
            }
        } 

        return view('student-registrars.show-approved', compact('data'))->with(array('countPending' => $countPending, 'showData' => $showData, 'count' => $count, 'eligibleStatus' => $eligibleStatus, 'activeStatus' => $activeStatus, 'inactiveStatus' => $inactiveStatus, 'countTrash' => $countTrash))->withItems($items); //admin mengacu ke table admin di phpmyadmin
    }

    public function showRejected(Request $request) {
        $filterKeyword = $request->get('keyword');
        $status = $request->get('status');

        $count = \App\StudentRegistrars::count();
        $eligibleStatus = \App\StudentRegistrars::where('status', "Eligible")->count();
        $activeStatus = \App\StudentRegistrars::where('status', "Qualified")->count();
        $inactiveStatus = \App\StudentRegistrars::where('status', "Rejected")->count();
        $countTrash = \App\StudentRegistrars::onlyTrashed()->count();
        $countPending = \App\StudentRegistrars::where('status', "Pending")->count(); 

        $count = \App\StudentRegistrars::count();
        $items = $request->items ?? 5;
        $page    = $request->has('name') ? $request->get('name') : 1;
        $showingTotal  = $page * $items;
        $admins= \App\StudentRegistrars::paginate($items);
        $showingStarted = $admins->currentPage(); 

        $showData = "Showing $showingStarted to $showingTotal of $countPending";

        if($status) {
            $data = \App\StudentRegistrars::where('status', $status)->paginate($items);
        } else {
            $data = \App\StudentRegistrars::where('status', "Rejected")->paginate($items);
        }

        if($filterKeyword) {
            if($status) {
                $data = \App\StudentRegistrars::where('name', 'LIKE', "%$filterKeyword%")->orWhere('email', 'LIKE', "%$filterKeyword%")->where('status', $status)->paginate($items);
            } else {
                $data = \App\StudentRegistrars::where('name', 'LIKE', "%$filterKeyword%")->orWhere('email', 'LIKE', "%$filterKeyword%")->paginate($items);
            }
        } 

        return view('student-registrars.show-rejected', compact('data'))->with(array('countPending' => $countPending, 'showData' => $showData, 'count' => $count, 'eligibleStatus' => $eligibleStatus, 'activeStatus' => $activeStatus, 'inactiveStatus' => $inactiveStatus, 'countTrash' => $countTrash))->withItems($items); //admin mengacu ke table admin di phpmyadmin
    }

    public function reject($id) {
        $applicant = \App\StudentRegistrars::where('id', $id);
        $applicant->update(['status' => 'Rejected']);

        $getSelectedUser = \App\User::where('username', function($query) use ($id) { //retrieve a collection of users from users table where username in table users. (continue below)
            $query->select('username')->from('student_registrars')->where('id', $id); //$query(where usernames in table users) like selected usernames in applicants table. (To get selected usernames in applicants table, use where('id', $id) parameter.)
        });

        if($getSelectedUser) {
            $getSelectedUser->forceDelete(); 
        }

        $student = \App\StudentRegistrars::find($id);

        foreach($student->father_registrars as $getFatherData) {
            $fatherID = $getFatherData->id;
            $updateFather = \App\FatherRegistrars::where('id', $fatherID);
            $updateFather->update(['status' => 'Rejected']);
        }

        foreach($student->mother_registrars as $getMotherData) {
            $motherID = $getMotherData->id;
            $updateMother = \App\MotherRegistrars::where('id', $motherID);
            $updateMother->update(['status' => 'Rejected']);
        }

        $user = \App\StudentRegistrars::where('id', $id)->firstOrFail();
        //$user->notify(new RejectedApplicant($user)); 
      
        return redirect()->route('student-registrars.pending')->with('status', 'User successfully rejected');

    }

    public function rejectMultiple(Request $request) {
        $get_ids = $request->ids;
        $ids = explode(',', $get_ids); 

        $users = \App\StudentRegistrars::whereIn('id', $ids);

        $selectedUsers = \App\User::whereIn('username', function($query) use($ids) { //retrieve a collection of users from users table whereIn usernames. (continue below)
            $query->select('username')->from('student_registrars')->whereIn('id', $ids); //$query(whereIn usernames in table users) like selected usernames in applicants table. (To get selected usernames in applicants table, use whereIn('id', $ids) parameter.)
        });

        //get father registrars who have students within ids
        $getBulkFather = \App\FatherRegistrars::whereHas('student_registrars', function($q) use($ids) {
            $q->whereIn('student_registrars.id', $ids);
        });

        //get mother registrars who have students within ids
        $getBulkMother = \App\MotherRegistrars::whereHas('student_registrars', function($q) use($ids) {
            $q->whereIn('student_registrars.id', $ids);
        });

        if($selectedUsers) {
            $selectedUsers->forceDelete(); //prevent duplicate data in users table
        } 
        
        $users->update(['status' => 'Rejected']); 
        $getBulkFather->update(['status' => 'Rejected']); 
        $getBulkMother->update(['status' => 'Rejected']); 

        /* $users->each(function($user) {
            Mail::to($user)->send(new RejectedEmail());
        });

        if(count(Mail::failures()) > 0) {
            //The Mail::failures() will return an array of failed emails.
            foreach(Mail::failures() as $sent_status) { 
                $newData = \App\SentStatus::create(['email' => $sent_status]);
                $newData->save();
            }
        } */   

        return response()->json(['success' => "Selected applicant(s) successfully rejected."]);
    }

    public function cancel($id) {
        $applicant = \App\StudentRegistrars::where('id', $id);
        $applicant->update(['status' => 'Pending']);

        $student = \App\StudentRegistrars::find($id);

        foreach($student->father_registrars as $getFatherData) {
            $fatherID = $getFatherData->id;
            $updateFather = \App\FatherRegistrars::where('id', $fatherID);
            $updateFather->update(['status' => 'Pending']);
        }

        foreach($student->mother_registrars as $getMotherData) {
            $motherID = $getMotherData->id;
            $updateMother = \App\MotherRegistrars::where('id', $motherID);
            $updateMother->update(['status' => 'Pending']);
        }

        $user = \App\StudentRegistrars::where('id', $id)->firstOrFail();
        //$user->notify(new ApplicantOnHold($user)); 
      
        return redirect()->route('student-registrars.showrejected')->with('status', 'User successfully on hold');
    }

    public function cancelMultiple(Request $request) {
        $get_ids = $request->ids;
        $ids = explode(',', $get_ids); 

        $students = \App\StudentRegistrars::whereIn('id', $ids);

        //get father registrars who have students within ids
        $getBulkFather = \App\FatherRegistrars::whereHas('student_registrars', function($q) use($ids) {
            $q->whereIn('student_registrars.id', $ids);
        });

        //get mother registrars who have students within ids
        $getBulkMother = \App\MotherRegistrars::whereHas('student_registrars', function($q) use($ids) {
            $q->whereIn('student_registrars.id', $ids);
        });

        $students->update(['status' => 'Pending']);
        $getBulkFather->update(['status' => 'Pending']); 
        $getBulkMother->update(['status' => 'Pending']); 

       /* $students->each(function($user) {
            Mail::to($user)->send(new SendEmail());
        }); */
      
        return response()->json(['success' => "Selected applicant(s) successfully rollback."]);
    }

    public function rollback($id) {
        $student = \App\StudentRegistrars::find($id);

        foreach($student->father_registrars as $getFatherData) {
            $fatherID = $getFatherData->id;
            $specificFather = \App\FatherRegistrars::where('id', $fatherID);
            $specificFather->update(['status' => 'Pending']);
        }

        foreach($student->mother_registrars as $getMotherData) {
            $motherID = $getMotherData->id;
            $specificMother = \App\MotherRegistrars::where('id', $motherID);
            $specificMother->update(['status' => 'Pending']);
        }

        $applicant = \App\StudentRegistrars::where('id', $id);
        $applicant->update(['status' => 'Pending']);

        $getSelectedUser = \App\User::where('username', function($query) use($id) { //retrieve a collection of users from users table where username in table users. (continue below)
            $query->select('username')->from('student_registrars')->where('id', $id); //$query(where usernames in table users) like selected usernames in student_registrars table. (To get selected usernames in applicants table, use where('id', $id) parameter.)
        });

        if($getSelectedUser) {
            $getSelectedUser->forceDelete(); 
        }

        $user = \App\StudentRegistrars::where('id', $id)->firstOrFail();
        //$user->notify(new ApplicantOnHold($user)); 
      
        return redirect()->route('student-registrars.showeligible')->with('status', 'User successfully on hold');
    }

    public function rollbackMultiple(Request $request) {
        $get_ids = $request->ids;
        $ids = explode(',', $get_ids); 

        $students = \App\StudentRegistrars::whereIn('id', $ids);

        //get father registrars who has students within ids
        $getBulkFather = \App\FatherRegistrars::whereHas('student_registrars', function($q) use($ids) {
            $q->whereIn('student_registrars.id', $ids);
        });

        //get mother registrars who has students within ids
        $getBulkMother = \App\MotherRegistrars::whereHas('student_registrars', function($q) use($ids) {
            $q->whereIn('student_registrars.id', $ids);
        });

        $selectedUsers = \App\User::whereIn('username', function($query) use($ids) { //retrieve a collection of users from users table whereIn usernames. (continue below)
            $query->select('username')->from('student_registrars')->whereIn('id', $ids); //$query(whereIn usernames in table users) like selected usernames in applicants table. (To get selected usernames in applicants table, use whereIn('id', $ids) parameter.)
        });

        $students->update(['status' => 'Pending']); 
        $getBulkFather->update(['status' => 'Pending']); 
        $getBulkMother->update(['status' => 'Pending']); 

        if($selectedUsers) {
            $selectedUsers->forceDelete(); 
        }

        /* $students->each(function($user) {
            Mail::to($user)->send(new SendEmail());
        }); */
      
        return response()->json(['success' => "Selected applicant(s) successfully rollback."]);
    }

    public function hold($id) {
        $student = \App\StudentRegistrars::find($id);

        //Delete father who has only one qualified student registrars in users table
        foreach($student->father_registrars as $getFatherData) {
            $fatherID = $getFatherData->id;
            $fatherUsername = $getFatherData->username;
            $specificFather = \App\FatherRegistrars::where('id', $fatherID);
            $specificFather->update(['status' => 'Pending']); 

            $getSelectedFathers = \App\User::where('username', $fatherUsername)->first();

            $totalGMStudent = \App\StudentRegistrars::withTrashed()->where('status', 'Qualified')->whereHas('guardianmale_registrars', function($q) use($fatherUsername) {
                $q->where('guardianmale_registrars.username', $fatherUsername);
            })->count();

            //count qualified students who has father with id $fatherID
            $totalStudent = \App\StudentRegistrars::withTrashed()->where('status', 'Qualified')->whereHas('father_registrars', function($q) use($fatherID) {
                $q->where('father_registrars.id', $fatherID);
            })->count();

            if($totalStudent == 1) {
                $fatherCredential = \App\User::where('username', $fatherUsername);

                if($totalGMStudent == 0) {
                    if($fatherCredential) {
                        $fatherCredential->forceDelete(); 
                    }
                } 
                else 
                if($totalGMStudent >= 1) { 
                    $getSelectedFathers->removeRole('Parent');
                    $getSelectedFathers->save();
                }
            } 

        }

        //Delete guardian male who has only one qualified student registrars in users table
        foreach($student->guardianmale_registrars as $getGuardianMaleData) {
            $guardianMaleID = $getGuardianMaleData->id;
            $guardianMaleUsername = $getGuardianMaleData->username;
            $specificGuardianMale = \App\GuardianMaleRegistrars::where('id', $guardianMaleID);
            $specificGuardianMale->update(['status' => 'Pending']); 

            $getSelectedGuardianMales = \App\User::where('username', $guardianMaleUsername)->first();

            $totalFatherStudents = \App\StudentRegistrars::withTrashed()->where('status', 'Qualified')->whereHas('father_registrars', function($q) use($guardianMaleUsername) {
                $q->where('father_registrars.username', $guardianMaleUsername);
            })->count();

            $totalGMStudents = \App\StudentRegistrars::withTrashed()->where('status', 'Qualified')->whereHas('guardianmale_registrars', function($q) use($guardianMaleID) {
                $q->where('guardianmale_registrars.id', $guardianMaleID);
            })->count();

            if($totalGMStudents == 1) {
                $guardianMaleCredential = \App\User::where('username', $guardianMaleUsername);

                if($totalFatherStudents == 0) {
                    if($guardianMaleCredential) {
                        $guardianMaleCredential->forceDelete(); 
                    }
                }
                else 
                if($totalFatherStudents >= 1) { 
                    $getSelectedGuardianMales->removeRole('Guardian');
                    $getSelectedGuardianMales->save();
                }    
            }
        }

        //Delete mother who has only one qualified student registrars in users table
        foreach($student->mother_registrars as $getMotherData) {
            $motherID = $getMotherData->id;
            $motherUsername = $getMotherData->username;
            $specificMother = \App\MotherRegistrars::where('id', $motherID);
            $specificMother->update(['status' => 'Pending']);

            $getSelectedMothers = \App\User::where('username', $motherUsername)->first();
   
            $totalGFStudents = \App\StudentRegistrars::withTrashed()->where('status', 'Qualified')->whereHas('guardianfemale_registrars', function($q) use($motherUsername) {
                $q->where('guardianfemale_registrars.username', $motherUsername);
            })->count();

            $totalMotherStudents = \App\StudentRegistrars::withTrashed()->where('status', 'Qualified')->whereHas('mother_registrars', function($q) use($motherID) {
                $q->where('mother_registrars.id', $motherID);
            })->count();

            if($totalMotherStudents == 1) {
                $motherCredential = \App\User::where('username', $motherUsername);

                if($totalGFStudents == 0) {
                    if($motherCredential) {
                        $motherCredential->forceDelete(); 
                    }
                }
                else 
                if($totalGFStudents >= 1) { 
                    $getSelectedMothers->removeRole('Parent');
                    $getSelectedMothers->save();
                }
            }
            
        }

        //Delete guardian female who has only one qualified student registrars in users table
        foreach($student->guardianfemale_registrars as $getGuardianFemaleData) {
            $guardianFemaleID = $getGuardianFemaleData->id;
            $guardianFemaleUsername = $getGuardianFemaleData->username;
            $specificGuardianFemale = \App\GuardianFemaleRegistrars::where('id', $guardianFemaleID);
            $specificGuardianFemale->update(['status' => 'Pending']);

            $getSelectedGuardianFemales = \App\User::where('username', $guardianFemaleUsername)->first();

            $numberOfMotherStudents = \App\StudentRegistrars::withTrashed()->where('status', 'Qualified')->whereHas('mother_registrars', function($q) use($guardianFemaleUsername) {
                $q->where('mother_registrars.username', $guardianFemaleUsername);
            })->count();

            $numberOfGFStudents = \App\StudentRegistrars::withTrashed()->where('status', 'Qualified')->whereHas('guardianfemale_registrars', function($q) use($guardianFemaleID) {
                $q->where('guardianfemale_registrars.id', $guardianFemaleID);
            })->count();

            if($numberOfGFStudents == 1) {
                $guardianFemaleCredential = \App\User::where('username', $guardianFemaleUsername);

                if($numberOfMotherStudents == 0) {
                    if($guardianFemaleCredential) {
                        $guardianFemaleCredential->forceDelete(); 
                    }
                }
                else 
                if($numberOfMotherStudents >= 1) { 
                    $getSelectedGuardianFemales->removeRole('Guardian');
                    $getSelectedGuardianFemales->save();
                }
            }
        }

        $applicant = \App\StudentRegistrars::where('id', $id);
        $applicant->update(['status' => 'Pending']);

        $getSelectedUser = \App\User::where('username', function($query) use($id) { //retrieve a collection of users from users table where username in table users. (continue below)
            $query->select('username')->from('student_registrars')->where('id', $id); //$query(where usernames in table users) like selected usernames in student_registrars table. (To get selected usernames in applicants table, use where('id', $id) parameter.)
        });

        if($getSelectedUser) {
            $getSelectedUser->forceDelete(); 
        }

        $user = \App\StudentRegistrars::where('id', $id)->firstOrFail();
        //$user->notify(new ApplicantOnHold($user)); 
      
        return redirect()->route('student-registrars.showapproved')->with('status', 'User successfully on hold');
    }

    public function holdMultiple(Request $request) {
        $get_ids = $request->ids;
        $ids = explode(',', $get_ids); 

        //////////////////////////////////////////

        //Get selected father who doesn't have qualified student registrars 
        $fatherUsername = \App\FatherRegistrars::select('username')->whereHas('student_registrars', function($q) use($ids) {
            $q->whereIn('student_registrars.id', $ids);
        })->get();

        /* $getQualifiedFatherStudent = \App\FatherRegistrars::whereIn('username', $fatherUsername)->doesntHave('student_registrars', 'and', function($q) {
            $q->where('student_registrars.status', 'Qualified');
        })->select('username')->get(); 

        $fatherCredential = \App\User::whereIn('username', $getQualifiedFatherStudent);

        if($fatherCredential) {
            $fatherCredential->forceDelete(); 
        } */ 

        $getSelectedFathers = \App\User::whereIn('username', $fatherUsername)->get();

        $totalGMStudent = \App\StudentRegistrars::withTrashed()->where('status', 'Qualified')->whereHas('guardianmale_registrars', function($q) use($fatherUsername) {
            $q->whereIn('guardianmale_registrars.username', $fatherUsername);
        })->count();

        //count qualified students who has father with id $fatherID
        $totalStudent = \App\StudentRegistrars::withTrashed()->where('status', 'Qualified')->whereHas('father_registrars', function($q) use($fatherUsername) {
            $q->whereIn('father_registrars.username', $fatherUsername);
        })->count();

        if($totalStudent == 1) {
            $fatherCredential = \App\User::whereIn('username', $fatherUsername);

            if($totalGMStudent == 0) {
                if($fatherCredential) {
                    $fatherCredential->forceDelete(); 
                }
            } 
            else 
            if($totalGMStudent >= 1) { 
                foreach($getSelectedFathers as $user) { 
                    $user->removeRole('Parent');
                    $user->save();
                }
            }
        } 

        //////////////////////////////////////////

        //Get selected guardian male who doesn't have qualified student registrars 
        $guardianMaleUsername = \App\GuardianMaleRegistrars::select('username')->whereHas('student_registrars', function($q) use($ids) {
            $q->whereIn('student_registrars.id', $ids);  
        })->get();

       /* $QualifiedGMStudentUsername = \App\GuardianMaleRegistrars::whereIn('username', $guardianMaleUsername)->doesntHave('student_registrars', 'and', function($q) {
            $q->where('student_registrars.status', 'Qualified');
        })->select('username')->get();

        $GuardianMaleCredential = \App\User::whereIn('username', $QualifiedGMStudentUsername);

        if($GuardianMaleCredential) {
            $GuardianMaleCredential->forceDelete(); 
        }
        */

        $getSelectedGuardianMales = \App\User::whereIn('username', $guardianMaleUsername)->get();

        $totalFatherStudents = \App\StudentRegistrars::withTrashed()->where('status', 'Qualified')->whereHas('father_registrars', function($q) use($guardianMaleUsername) {
            $q->whereIn('father_registrars.username', $guardianMaleUsername);
        })->count();

        $totalGMStudents = \App\StudentRegistrars::withTrashed()->where('status', 'Qualified')->whereHas('guardianmale_registrars', function($q) use($guardianMaleUsername) {
            $q->whereIn('guardianmale_registrars.username', $guardianMaleUsername);
        })->count();

        if($totalGMStudents == 1) {
            $guardianMaleCredential = \App\User::whereIn('username', $guardianMaleUsername);

            if($totalFatherStudents == 0) {
                if($guardianMaleCredential) {
                    $guardianMaleCredential->forceDelete(); 
                }
            }
            else 
            if($totalFatherStudents >= 1) { 
                foreach($getSelectedGuardianMales as $user) { 
                    $user->removeRole('Guardian');
                    $user->save();
                }
            }    
        }

        //////////////////////////////////////////

        //Get selected mother who doesn't have qualified student registrars 
        $motherUsername = \App\MotherRegistrars::select('username')->whereHas('student_registrars', function($q) use($ids) {
            $q->whereIn('student_registrars.id', $ids);  
        })->get();

        /* $getQualifiedMotherStudent = \App\MotherRegistrars::whereIn('username', $motherUsername)->doesntHave('student_registrars', 'and', function($q) {
            $q->where('student_registrars.status', 'Qualified');
        })->select('username')->get();

        $motherCredential = \App\User::whereIn('username', $getQualifiedMotherStudent);

        if($motherCredential) {
            $motherCredential->forceDelete(); 
        }
        */

        $getSelectedMothers = \App\User::whereIn('username', $motherUsername)->get();

        $totalGFStudents = \App\StudentRegistrars::withTrashed()->where('status', 'Qualified')->whereHas('guardianfemale_registrars', function($q) use($motherUsername) {
            $q->whereIn('guardianfemale_registrars.username', $motherUsername);
        })->count();

        $totalMotherStudents = \App\StudentRegistrars::withTrashed()->where('status', 'Qualified')->whereHas('mother_registrars', function($q) use($motherUsername) {
            $q->whereIn('mother_registrars.username', $motherUsername);
        })->count();

        if($totalMotherStudents == 1) {
            $motherCredential = \App\User::whereIn('username', $motherUsername);

            if($totalGFStudents == 0) {
                if($motherCredential) {
                    $motherCredential->forceDelete(); 
                }
            }
            else 
            if($totalGFStudents >= 1) { 
                foreach($getSelectedMothers as $user) { 
                    $user->removeRole('Parent');
                    $user->save();
                }
            }
        }

        //////////////////////////////////////////

        //Get selected mother who doesn't have qualified student registrars 
        $guardianFemaleUsername = \App\GuardianFemaleRegistrars::select('username')->whereHas('student_registrars', function($q) use($ids) {
            $q->whereIn('student_registrars.id', $ids);  
        })->get();

        $getSelectedGuardianFemales = \App\User::whereIn('username', $guardianFemaleUsername)->get();

        $numberOfMotherStudents = \App\StudentRegistrars::withTrashed()->where('status', 'Qualified')->whereHas('mother_registrars', function($q) use($guardianFemaleUsername) {
            $q->whereIn('mother_registrars.username', $guardianFemaleUsername);
        })->count();

        $numberOfGFStudents = \App\StudentRegistrars::withTrashed()->where('status', 'Qualified')->whereHas('guardianfemale_registrars', function($q) use($guardianFemaleUsername) {
            $q->whereIn('guardianfemale_registrars.username', $guardianFemaleUsername);
        })->count();

        if($numberOfGFStudents == 1) {
            $guardianFemaleCredential = \App\User::whereIn('username', $guardianFemaleUsername);

            if($numberOfMotherStudents == 0) {
                if($guardianFemaleCredential) {
                    $guardianFemaleCredential->forceDelete(); 
                }
            }
            else 
            if($numberOfMotherStudents >= 1) { 
                foreach($getSelectedGuardianFemales as $user) { 
                    $user->removeRole('Guardian');
                    $user->save();
                }
            }
        }

        //////////////////////////////////////////

        //get father registrars who have students within ids
        $getBulkFather = \App\FatherRegistrars::whereHas('student_registrars', function($q) use($ids) {
            $q->whereIn('student_registrars.id', $ids);
        });

        //get mother registrars who have students within ids
        $getBulkMother = \App\MotherRegistrars::whereHas('student_registrars', function($q) use($ids) {
            $q->whereIn('student_registrars.id', $ids);
        });

        $getBulkFather->update(['status' => 'Pending']); 
        $getBulkMother->update(['status' => 'Pending']); 

        //////////////////////////////////////////

        //get student registrars within ids
        $students = \App\StudentRegistrars::whereIn('id', $ids);
        $students->update(['status' => 'Pending']); 

        //////////////////////////////////////////

        //count exist specific students in table users
        $selectedUsers = \App\User::whereIn('username', function($query) use($ids) { //retrieve a collection of users from users table whereIn usernames. (continue below)
            $query->select('username')->from('student_registrars')->whereIn('id', $ids); //$query(whereIn usernames in table users) like selected usernames in applicants table. (To get selected usernames in applicants table, use whereIn('id', $ids) parameter.)
        });

        if($selectedUsers) {
            $selectedUsers->forceDelete(); //prevent duplicate data in users table
        }

        //////////////////////////////////////////

        /* $users->each(function($user) {
            Mail::to($user)->send(new OnHoldEmail());
        });
        if(count(Mail::failures()) > 0) {
            //The Mail::failures() will return an array of failed emails.
            foreach(Mail::failures() as $sent_status) { 
                $newData = \App\SentStatus::create(['email' => $sent_status]);
                $newData->save();
            }
        } */

        return response()->json(['success' => "Selected applicant(s) successfully on Hold."]);
    }

}
