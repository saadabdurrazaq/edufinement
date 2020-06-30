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
        $this->middleware(['auth', 'verified']); 
    }

    public function register()
    {
        return view('student-registrars.register');
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

        //////////////////////////////////////////

        //count students who have father with id $fatherID
        $fatherUsername = \App\FatherRegistrars::select('username')->whereHas('student_registrars', function($q) use($ids) {
            $q->whereIn('student_registrars.id', $ids);
        });
        $fatherID = \App\FatherRegistrars::select('id')->whereHas('student_registrars', function($q) use($ids) {
            $q->whereIn('student_registrars.id', $ids);
        })->get();
        $getQualifiedFatherStudents = \App\StudentRegistrars::withTrashed()->where('status', 'Qualified')->whereHas('father_registrars', function($q) use($fatherID) {
            $q->whereIn('father_registrars.id', $fatherID);
        })->count();
        $countTotalFatherChilderns = \App\StudentRegistrars::withTrashed()->whereHas('father_registrars', function($q) use($fatherID) {
            $q->whereIn('father_registrars.id', $fatherID);
        })->count();

        //count students who have mother with id $motherID
        $motherUsername = \App\MotherRegistrars::select('username')->whereHas('student_registrars', function($q) use($ids) {
            $q->whereIn('student_registrars.id', $ids);
        });
        $motherID = \App\MotherRegistrars::select('id')->whereHas('student_registrars', function($q) use($ids) {
            $q->whereIn('student_registrars.id', $ids);
        })->get();
        $getQualifiedMotherStudents = \App\StudentRegistrars::withTrashed()->where('status', 'Qualified')->whereHas('mother_registrars', function($q) use($motherID) {
            $q->whereIn('mother_registrars.id', $motherID);
        })->count();
        $countTotalMotherChilderns = \App\StudentRegistrars::withTrashed()->whereHas('mother_registrars', function($q) use($motherID) {
            $q->whereIn('mother_registrars.id', $motherID);
        })->count();

        //count exist specific students in table users
        $selectedUsers = \App\User::whereIn('username', function($query) use($ids) { //retrieve a collection of users from users table whereIn usernames. (continue below)
            $query->select('username')->from('student_registrars')->whereIn('id', $ids); //$query(whereIn usernames in table users) like selected usernames in applicants table. (To get selected usernames in applicants table, use whereIn('id', $ids) parameter.)
        });

        //get student registrars within ids
        $students = \App\StudentRegistrars::withTrashed()->whereIn('id', $ids);
        $eachStudent = \App\StudentRegistrars::withTrashed()->whereIn('id', $ids)->get();

        //////////////////////////////////////////

        if($selectedUsers) {
           $selectedUsers->forceDelete(); //prevent duplicate data in users table
        }

        if($getQualifiedFatherStudents == 1) {
            $fatherAccount = \App\User::whereIn('username', $fatherUsername);
            if($fatherAccount) {
                $fatherAccount->forceDelete(); 
            }
        }

        if($getQualifiedMotherStudents == 1) {
            $motherAccount = \App\User::whereIn('username', $motherUsername);
            if($motherAccount) {
                $motherAccount->forceDelete(); 
            }
        }

        if($countTotalFatherChilderns == 1) {
            $fatherCredential = \App\FatherRegistrars::whereIn('username', $fatherUsername);
            if($fatherCredential) {
                $fatherCredential->forceDelete(); 
            }
        }

        if($countTotalMotherChilderns == 1) {
            $motherCredential = \App\MotherRegistrars::whereIn('username', $motherUsername);
            if($motherCredential) {
                $motherCredential->forceDelete(); 
            }
        }

        ///////////////////////////////////////////

        foreach ($eachStudent as $student) {
            $student->father_registrars()->detach();
            $student->mother_registrars()->detach(); 
        }
        $students->forceDelete(); 

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
            'father-username' => 'min:5|required', 
            'account-key' => 'required', 
            'mother-username' => 'min:5|required', 
            'mother-account-key' => 'required', 
        ])->validate();  

        $model = \App\FatherRegistrars::where('username', $request->get('father-username'))->first();
        $authFather = $model && Hash::check($request->get('account-key'), $model->account_key, []);

        $motherUsername = \App\MotherRegistrars::where('username', $request->get('mother-username'))->first();
        $authMother = $motherUsername && Hash::check($request->get('mother-account-key'), $motherUsername->account_key, []);

        if($authFather && $authMother) {
            $new_user = new \App\StudentRegistrars(); //Panggil model User
            $new_user->name = $request->get('name');
            $new_user->email = $request->get('email');
            $new_user->phone = $request->get('phone');
            $new_user->username = $request->get('username');
            $new_user->gender = $request->get('gender');
            $new_user->password = \Hash::make($request->get('password'));
            $new_user->registered_date = now();
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
        else {
            return redirect()->route('student-regis')->with('warning', 'Sorry your father/mother usernames and his/her account key doesnt match at all. Please try again!');
        }
        
    }

    public function approve($id) {
        $new_student = \App\StudentRegistrars::where('id', $id);
        $new_student->update(['status' => 'Qualified', 'approved_date' => now()]);

        $getSelectedUser = \App\User::where('username', function($query) use ($id) { //retrieve a collection of users from users table where username in table users. (continue below)
            $query->select('username')->from('student_registrars')->where('id', $id); //$query(where usernames in table users) like selected usernames in student_registrars table. (To get selected usernames in student_registrars table, use where('id', $id) parameter.)
        });

        if($getSelectedUser) {
            $getSelectedUser->forceDelete(); 
        } 

        //replicate the data to users table

        $student = \App\StudentRegistrars::find($id);

        foreach($student->father_registrars as $getFatherData) {
            $fatherID = $getFatherData->id;
            $updateFather = \App\FatherRegistrars::where('id', $fatherID);
            $updateFather->update(['status' => 'Qualified', 'approved_date' => now()]);

            $getFather = \App\User::where('username', $getFatherData->username);
            if($getFather) {
               $getFather->forceDelete();
            }

            $getFatherData->makeHidden(['status', 'id', 'account_key']);
            $replicaFatherData = $getFatherData->replicate();
            $fatherDatatoArray = $replicaFatherData->toArray();
            $father = \App\User::firstOrCreate($fatherDatatoArray);
            $father->assignRole('Parents');
            $father->password = $getFatherData->password;
            $father->save();
            //$user->notify(new ApprovedApplicant($user));
        }

        foreach($student->mother_registrars as $getMotherData) {
            $motherID = $getMotherData->id;
            $updateMother = \App\MotherRegistrars::where('id', $motherID);
            $updateMother->update(['status' => 'Qualified', 'approved_date' => now()]);

            $getMother = \App\User::where('username', $getMotherData->username);
            if($getMother) {
               $getMother->forceDelete();
            }

            $getMotherData->makeHidden(['status', 'id', 'account_key']);
            $replicaMotherData = $getMotherData->replicate();
            $motherDatatoArray = $replicaMotherData->toArray(); 
            $mother = \App\User::firstOrCreate($motherDatatoArray);
            $mother->assignRole('Parents');
            $mother->password = $getMotherData->password;
            $mother->save();
            //$user->notify(new ApprovedApplicant($user));
        }

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

        //clone father registrars who have students within ids
        $cloneFathers = \App\FatherRegistrars::whereHas('student_registrars', function($q) use($ids) {
            $q->whereIn('student_registrars.id', $ids);
        })->get();
        $cloneFathers->makeHidden(['status', 'id', 'email_sent']);
        $cloneFathers->makeVisible(['password']);
        $new_fathers = $cloneFathers->toArray();

        //clone mother registrars who have students within ids
        $cloneMothers = \App\MotherRegistrars::whereHas('student_registrars', function($q) use($ids) {
            $q->whereIn('student_registrars.id', $ids);
        })->get();
        $cloneMothers->makeHidden(['status', 'id', 'email_sent']);
        $cloneMothers->makeVisible(['password']);
        $new_mothers = $cloneMothers->toArray();

        //get student registrars within ids
        $students = \App\StudentRegistrars::whereIn('id', $ids);

        //get father registrars who have students within ids
        $getBulkFather = \App\FatherRegistrars::whereHas('student_registrars', function($q) use($ids) {
            $q->whereIn('student_registrars.id', $ids);
        });

        //get mother registrars who have students within ids
        $getBulkMother = \App\MotherRegistrars::whereHas('student_registrars', function($q) use($ids) {
            $q->whereIn('student_registrars.id', $ids);
        });

        //////////////////////////////////////////

        $students->update(['status' => 'Qualified', 'approved_date' => now()]); 
        $getBulkFather->update(['status' => 'Qualified']); 
        $getBulkMother->update(['status' => 'Qualified']); 
        
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
             $query->select('username')->from('father_registrars')->whereIn('username', $getFatherUsername); //$query(whereIn usernames in table users) like selected usernames in applicants table. (To get selected usernames in applicants table, use whereIn('id', $ids) parameter.)
         })->get();
         
         foreach($getSelectedFathers as $user) {
             $user->assignRole('Parents');
             $user->save();
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
         
         foreach($getSelectedMothers as $user) {
             $user->assignRole('Parents');
             $user->save();
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

        foreach($student->father_registrars as $getFatherData) {
            $fatherID = $getFatherData->id;
            $specificFather = \App\FatherRegistrars::where('id', $fatherID);
            $specificFather->update(['status' => 'Pending']);

            //count qualified students who have father with id $fatherID
            $getSelectedStudent = \App\StudentRegistrars::withTrashed()->where('status', 'Qualified')->whereHas('father_registrars', function($q) use($fatherID) {
                $q->where('father_registrars.id', $fatherID);
            })->count();

            if($getSelectedStudent == 1) {
                $fatherUsername = $getFatherData->username;
                $fatherCredential = \App\User::where('username', $fatherUsername);
                if($fatherCredential) {
                    $fatherCredential->forceDelete(); 
                }
            }
        }

        foreach($student->mother_registrars as $getMotherData) {
            $motherID = $getMotherData->id;
            $specificMother = \App\MotherRegistrars::where('id', $motherID);
            $specificMother->update(['status' => 'Pending']);

            $getQualifiedStudent = \App\StudentRegistrars::withTrashed()->where('status', 'Qualified')->whereHas('mother_registrars', function($q) use($motherID) {
                $q->where('mother_registrars.id', $motherID);
            })->count();

            if($getQualifiedStudent == 1) {
                $motherUsername = $getMotherData->username;
                $motherCredential = \App\User::where('username', $motherUsername);
                if($motherCredential) {
                    $motherCredential->forceDelete(); 
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

        //get student registrars within ids
        $students = \App\StudentRegistrars::whereIn('id', $ids);

        //get father registrars who have students within ids
        $getBulkFather = \App\FatherRegistrars::whereHas('student_registrars', function($q) use($ids) {
            $q->whereIn('student_registrars.id', $ids);
        });

        //get mother registrars who have students within ids
        $getBulkMother = \App\MotherRegistrars::whereHas('student_registrars', function($q) use($ids) {
            $q->whereIn('student_registrars.id', $ids);
        });

        // $students->update(['status' => 'Pending']); 

        //////////////////////////////////////////

        //count selected qualified students (whereIn('id', $ids)) who have father with id $fatherID
        $fatherUsername = \App\FatherRegistrars::select('username')->whereHas('student_registrars', function($q) use($ids) {
            $q->whereIn('student_registrars.id', $ids);
        })->get();

        
        
        $fatherID = \App\FatherRegistrars::select('id')->whereHas('student_registrars', function($q) use($ids) {
            $q->whereIn('student_registrars.id', $ids);
        })->get();

        
        /* $getQualifiedFatherStudent = \App\StudentRegistrars::withTrashed()->where('status', 'Qualified')->whereHas('father_registrars', function($q) use($fatherID) {
            $q->whereIn('father_registrars.id', $fatherID);
        }); */
        /* $getQualifiedFatherStudent = \App\StudentRegistrars::withTrashed()->where('status', 'Qualified')->whereHas('father_registrars', function($q) use($fatherID) {
            foreach($fatherID as $idf) {
                $q->whereIn('father_registrars.id', $idf);
            }
        }); */
        //$fatherTargeted = \App\FatherRegistrars::select('username')->where('username', $fatherUsername)->get(); 
        /* $getQualifiedFatherStudent = \App\StudentRegistrars::withTrashed()->where('status', 'Qualified')->whereHas('father_registrars', function($q) use($fatherTargeted) {
            $q->whereIn('father_registrars.username', $fatherTargeted);
        })->count(); */
        $getQualifiedFatherStudent = \App\FatherRegistrars::where('username', $fatherUsername)->whereHas('student_registrars', function($q) {
            $q->where('student_registrars.status', 'Qualified');
        })->get(); 
        print_r($getQualifiedFatherStudent);
        return;
        dd($getQualifiedFatherStudent);
        return;
        /* $getQualifiedFatherStudent = \App\FatherRegistrars::withTrashed()->whereHas('student_registrars', function($q) use($ids) {
            $q->whereIn('student_registrars.id', $ids)->where('student_registrars.status', 'Qualified');
        }); */
        /* $getQualifiedFatherStudent = \App\FatherRegistrars::withTrashed()->whereHas('student_registrars', function($q) use($ids) {
            $q->where('student_registrars.status', 'Qualified');
        })->count(); */
        /* $fatherUsername = \App\FatherRegistrars::select('username')->whereHas('student_registrars', function($q) use($ids) {
            $q->whereIn('student_registrars.id', $ids);
        })->get(); */

        //count selected qualified students (whereIn('id', $ids)) who have mother with id $motherID
        $motherUsername = \App\MotherRegistrars::select('username')->whereHas('student_registrars', function($q) use($ids) {
            $q->whereIn('student_registrars.id', $ids);  
        })->get();
        $motherID = \App\MotherRegistrars::select('id')->whereHas('student_registrars', function($q) use($ids) {
            $q->whereIn('student_registrars.id', $ids);
        })->get();
        $getQualifiedMotherStudent = \App\StudentRegistrars::withTrashed()->where('status', 'Qualified')->whereHas('mother_registrars', function($q) use($motherID) {
            $q->whereIn('mother_registrars.id', $motherID);
        })->count(); 
        /* $getPendingStudent2 = \App\MotherRegistrars::select('username')->withTrashed()->whereHas('student_registrars', function($q) use($ids) {
            $q->whereIn('student_registrars.id', $ids)->where('student_registrars.status', 'Pending');
        })->get(); */
        /* $getQualifiedMotherStudent = \App\MotherRegistrars::withTrashed()->whereHas('student_registrars', function($q) use($ids) {
            $q->whereIn('student_registrars.id', $ids)->where('student_registrars.status', 'Qualified');
        })->count(); */
        /* $getQualifiedMotherStudent = \App\MotherRegistrars::withTrashed()->whereHas('student_registrars', function($q) use($ids) {
            $q->where('student_registrars.status', 'Qualified');
        })->count(); */
        /* $motherUsername = \App\MotherRegistrars::select('username')->withTrashed()->whereHas('student_registrars', function($q) use($ids) {
            $q->whereIn('student_registrars.id', $ids); 
        })->get(); */

        //count exist specific students in table users
        $selectedUsers = \App\User::whereIn('username', function($query) use($ids) { //retrieve a collection of users from users table whereIn usernames. (continue below)
            $query->select('username')->from('student_registrars')->whereIn('id', $ids); //$query(whereIn usernames in table users) like selected usernames in applicants table. (To get selected usernames in applicants table, use whereIn('id', $ids) parameter.)
        });

        //////////////////////////////////////////

        foreach($getQualifiedFatherStudent as $eachResult) {
            $getCount = $eachResult->count();
            if($getCount == 0) {
                $fatherCredential = \App\User::whereIn('username', $fatherUsername);
                if($fatherCredential) {
                    $fatherCredential->forceDelete(); 
                } 
            }
        } 

        /* if($getQualifiedFatherStudent == 0) {
            $fatherCredential = \App\User::whereIn('username', $fatherUsername);
            if($fatherCredential) {
                $fatherCredential->forceDelete(); 
            } 
        } */

        if($getQualifiedMotherStudent == 0) {
            $motherCredential = \App\User::whereIn('username', $motherUsername);
            if($motherCredential) {
                $motherCredential->forceDelete(); 
            }
        } 

        /* if($getQualifiedFatherStudent == 1) {
            $fatherCredential = \App\User::whereIn('username', $getPendingStudent1);
            if($fatherCredential) {
                $fatherCredential->forceDelete(); 
            } 
        }

        if($getQualifiedMotherStudent == 1) {
            $motherCredential = \App\User::whereIn('username', $getPendingStudent2);
            if($motherCredential) {
                $motherCredential->forceDelete(); 
            }
        } */

        if($selectedUsers) {
            $selectedUsers->forceDelete(); //prevent duplicate data in users table
         }

        //////////////////////////////////////////

        //$students->update(['status' => 'Pending']); 
        $getBulkFather->update(['status' => 'Pending']); 
        $getBulkMother->update(['status' => 'Pending']); 

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
        //return response()->json(['success' => $getQualifiedFatherStudent]);
    }

    public function show($id)
    {
        $user = \App\StudentRegistrars::withTrashed()->find($id);
        return view('student-registrars.show', compact('user'));
    }

}
