<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Spatie\Permission\Models\Role;
use DB;
use Hash;
use PDF;
use App\Exports\UserExport;
use App\Exports\ActiveUserExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\InactiveUserExport;
use Illuminate\Validation\Rule;

class UserController extends Controller 
{
    /**
    * Display a listing of the resource.
    * 
    * @return \Illuminate\Http\Response
    */
    function __construct() 
    {
        $this->middleware('permission:View Users|Create Users|Edit User|Show User|Trash Users|Restore Users|Delete Users|Activate Users|Deactivate Users', ['only' => ['index','active','inactive','trash','show']]);
        $this->middleware('permission:Create Users', ['only' => ['create','store']]);
        $this->middleware('permission:Edit User', ['only' => ['edit','update']]);
        $this->middleware('permission:Trash Users', ['only' => ['destroy','destroyMultiple']]);
        $this->middleware('permission:Delete Users', ['only' => ['deletePermanent','deleteMultiple']]);
        $this->middleware('permission:Activate Users', ['only' => ['activate','activateMultiple']]);
        $this->middleware('permission:Deactivate Users', ['only' => ['deactivate','deactivateMultiple']]);
        $this->middleware('permission:Restore Users', ['only' => ['restore','restoreMultiple']]);
        $this->middleware(['auth', 'verified']);
    }

    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function index(Request $request) {
        $filterKeyword = $request->get('keyword');
        $status = $request->get('status'); 

        $activeStatus = \App\User::where('status', "ACTIVE")->count();
        $inactiveStatus = \App\User::where('status', "INACTIVE")->count();
        $countTrash = \App\User::onlyTrashed()->count();

        $count = \App\User::count();
        $items = $request->items ?? 5;
        $page    = $request->has('name') ? $request->get('name') : 1;
        $showingTotal  = $page * $items;
        $admins= \App\User::paginate($items);
        $showingStarted = $admins->currentPage(); 

        $showData = "Showing $showingStarted to $showingTotal of $count";

        if($status) {
            $data = \App\User::where('status', $status)->paginate($items);
        } else {
            $data = \App\User::paginate($items);
        }

        if($filterKeyword) {
            if($status) {
                $data = \App\User::where('name', 'LIKE', "%$filterKeyword%")->orWhere('email', 'LIKE', "%$filterKeyword%")->where('status', $status)->paginate($items);
            } else {
                $data = \App\User::where('name', 'LIKE', "%$filterKeyword%")->orWhere('email', 'LIKE', "%$filterKeyword%")->paginate($items);
            }
        } 
    
        return view('users.index', compact('data'))->with(array('showData' => $showData, 'count' => $count, 'activeStatus' => $activeStatus, 'inactiveStatus' => $inactiveStatus, 'countTrash' => $countTrash))->withItems($items); //admin mengacu ke table admin di phpmyadmin
    }

    public function trash(Request $request) {
        $filterKeyword = $request->get('keyword');
        $status = $request->get('status');

        $count = \App\User::count();
        $activeStatus = \App\User::where('status', "ACTIVE")->count();
        $inactiveStatus = \App\User::where('status', "INACTIVE")->count();
        $countTrash = \App\User::onlyTrashed()->count();

        $count = \App\User::count();
        $items = $request->items ?? 5;
        $page    = $request->has('name') ? $request->get('name') : 1;
        $showingTotal  = $page * $items;
        $admins= \App\User::paginate($items);
        $showingStarted = $admins->currentPage(); 

        $showData = "Showing $showingStarted to $showingTotal of $countTrash";

        if($status) {
            $data = \App\User::where('status', $status)->paginate($items);
        } else {
            $data = \App\User::onlyTrashed()->paginate($items);
        }

        if($filterKeyword) {
            if($status) {
                $data = \App\User::onlyTrashed()->where('name', 'LIKE', "%$filterKeyword%")->orWhere('email', 'LIKE', "%$filterKeyword%")->where('status', $status)->paginate($items);
            } else {
                $data = \App\User::onlyTrashed()->where('name', 'LIKE', "%$filterKeyword%")->orWhere('email', 'LIKE', "%$filterKeyword%")->paginate($items);
            }
        } 

        return view('users.trash', compact('data'))->with(array('showData' => $showData, 'count' => $count, 'activeStatus' => $activeStatus, 'inactiveStatus' => $inactiveStatus, 'countTrash' => $countTrash))->withItems($items); //admin mengacu ke table admin di phpmyadmin
    }

    public function restore($id) {
        $category = \App\User::withTrashed()->findOrFail($id);

        if($category->trashed()) {
            $category->restore(); 
        } else {
            return redirect()->route('users.trash')->with('status', 'User is not in trash');
        }  
      
        return redirect()->route('users.trash')->with('status', 'User successfully restored');
    }

    public function restoreMultiple(Request $request) {
        $get_ids = $request->ids;
        $ids = explode(',', $get_ids);
        $users = \App\User::whereIn('id', $ids);
        $users->restore();

        return response()->json(['success' => "Users successfully restored"]);
    }

    public function deletePermanent($id) {
        $category = \App\User::withTrashed()->findOrFail($id);

        if(!$category->trashed()){ 
            return redirect()->route('users.trash')->with('status', 'Can not delete permanent user');
        } else {
            $category->forceDelete();
            DB::table("social_facebook_accounts")->where('user_id', $id)->delete(); //agar user yang sudah dihapus dan mencoba kembali login dengan facebook, tampil halaman error
            DB::table("social_google_accounts")->where('user_id', $id)->delete(); 
            return redirect()->route('users.trash')->with('status', 'User permanently deleted');
        }
    }

    public function deleteMultiple(Request $request) {
        $get_ids = $request->ids;
        $ids = explode(',', $get_ids);
        $users = \App\User::whereIn('id', $ids);
        $users->forceDelete(); 

        DB::table("social_facebook_accounts")->whereIn('user_id', $ids)->delete();
        DB::table("social_google_accounts")->whereIn('user_id', $ids)->delete();

        return response()->json(['success' => "Users successfully permanently deleted"]);
    }

    public function active(Request $request) {
        $filterKeyword = $request->get('keyword');
        $status = $request->get('status');

        $activeStatus = \App\User::where('status', "ACTIVE")->count();
        $inactiveStatus = \App\User::where('status', "INACTIVE")->count();
        $countTrash = \App\User::onlyTrashed()->count();

        $count = \App\User::count();
        $items = $request->items ?? 5;
        $page    = $request->has('name') ? $request->get('name') : 1;
        $showingTotal  = $page * $items;
        $admins= \App\User::paginate($items);
        $showingStarted = $admins->currentPage(); 

        $showData = "Showing $showingStarted to $showingTotal of $activeStatus";

        if($status) {
            $data = \App\User::where('status', $status)->paginate($items);
        } else {
            $data = \App\User::where('status', "ACTIVE")->paginate($items);
        }

        if($filterKeyword) {
            if($status) {
                $data = \App\User::where('name', 'LIKE', "%$filterKeyword%")->orWhere('email', 'LIKE', "%$filterKeyword%")->paginate($items);
            } else {
                $data = \App\User::where('name', 'LIKE', "%$filterKeyword%")->orWhere('email', 'LIKE', "%$filterKeyword%")->where('status', "ACTIVE")->paginate($items);
            }
        } 

        return view('users.active', compact('data'))->with(array('showData' => $showData, 'count' => $count, 'activeStatus' => $activeStatus, 'inactiveStatus' => $inactiveStatus, 'countTrash' => $countTrash))->withItems($items); //admin mengacu ke table admin di phpmyadmin
    }

    public function inactive(Request $request) {
        $filterKeyword = $request->get('keyword');
        $status = $request->get('status');

        $activeStatus = \App\User::where('status', "ACTIVE")->count();
        $inactiveStatus = \App\User::where('status', "INACTIVE")->count();
        $countTrash = \App\User::onlyTrashed()->count();

        $count = \App\User::count();
        $items = $request->items ?? 5;
        $page    = $request->has('name') ? $request->get('name') : 1;
        $showingTotal  = $page * $items;
        $admins= \App\User::paginate($items);
        $showingStarted = $admins->currentPage(); 

        $showData = "Showing $showingStarted to $showingTotal of $inactiveStatus";

        if($status) {
            $data = \App\User::where('status', $status)->paginate($items);
        } else {
            $data = \App\User::where('status', "INACTIVE")->paginate($items);
        }

        if($filterKeyword) {
            if($status) {
                $data = \App\User::where('name', 'LIKE', "%$filterKeyword%")->orWhere('email', 'LIKE', "%$filterKeyword%")->paginate($items);
            } else {
                $data = \App\User::where('name', 'LIKE', "%$filterKeyword%")->orWhere('email', 'LIKE', "%$filterKeyword%")->where('status', "INACTIVE")->paginate($items);
            }
        } 

        return view('users.inactive', compact('data'))->with(array('showData' => $showData, 'count' => $count, 'activeStatus' => $activeStatus, 'inactiveStatus' => $inactiveStatus, 'countTrash' => $countTrash))->withItems($items); //admin mengacu ke table admin di phpmyadmin
    }

    public function activate($id) {
        $category = \App\User::where('id', $id);
        $category->update(['status' => 'ACTIVE']);
      
        return redirect()->route('users.inactive')->with('status', 'User successfully activated');
    }

    public function activateMultiple(Request $request) {
        $get_ids = $request->ids;
        $ids = explode(',', $get_ids);
        $users = \App\User::whereIn('id', $ids);
        $users->update(['status' => 'ACTIVE']); 

        return response()->json(['success' => "Users successfully activated."]);
    }

    public function deactivate($id) {
        $category = \App\User::where('id', $id);
        $category->update(['status' => 'INACTIVE']);
      
        return redirect()->route('users.active')->with('status', 'User successfully deactivated');
    }

    public function deactivateMultiple(Request $request) {
        $get_ids = $request->ids;
        $ids = explode(',', $get_ids);
        $users = \App\User::whereIn('id', $ids);
        $users->update(['status' => 'INACTIVE']); 

        return response()->json(['success' => "Users successfully deactivated."]);
    }

    /**
    * Show the form for creating a new resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function create()
    {
        $roles = Role::pluck('name','name')->all();
        return view('users.create',compact('roles'));
    }

    /**
    * Store a newly created resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
    public function store(Request $request) { 

        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email|unique:mother_registrars|unique:father_registrars|unique:student_registrars',
            "password" => "required",
            "password_confirmation" => "required|same:password",
            'username' => 'required|min:5|max:20|unique:users|unique:mother_registrars|unique:father_registrars|unique:student_registrars|regex:/^\S*$/u',
            'gender' => "required",
            'phone' => 'required|digits_between:10,12|unique:users|unique:mother_registrars|unique:father_registrars|unique:student_registrars',
            'roles' => 'required',
        ]);

        $input = $request->all();
        $input['password'] = Hash::make($input['password']);

        $user = User::create($input);
        $user->assignRole($request->input('roles'));

        return redirect()->route('users.index')->with('success', 'User created successfully');
    }

    /**
    * Display the specified resource.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function show($id)
    {
        $user = User::find($id);
        return view('users.show',compact('user'));
    }


    /**
    * Show the form for editing the specified resource.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function edit($id)
    {
        $user = User::find($id);
        $roles = Role::pluck('name','name')->all();
        $userRole = $user->roles->pluck('name','name')->all();

        return view('users.edit',compact('user','roles','userRole'));
    }

    /**
    * Update the specified resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function update(Request $request, $id) { 

        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$id,
            'phone' => 'required|digits_between:10,12|unique:users,phone,'.$id,
            'roles' => 'required'
        ]);

        $input = $request->all();

        $user = User::find($id); 
        $user->update($input);
        DB::table('model_has_roles')->where('model_id',$id)->delete();

        $user->assignRole($request->input('roles'));

        return redirect()->route('users.index')->with('success','User updated successfully');
    }

    /**
    * Remove the specified resource from storage. 
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function destroy($id) {
        User::find($id)->delete();
        DB::table("social_facebook_accounts")->where('user_id', $id)->delete();
        DB::table("social_google_accounts")->where('user_id', $id)->delete();
        return redirect()->route('users.index')->with('success','User deleted successfully');
    }

    public function destroyMultiple(Request $request) {
        $get_ids = $request->ids;
        $ids = explode(',', $get_ids);
        $users = \App\User::whereIn('id', $ids);
        $users->delete(); 

        DB::table("social_facebook_accounts")->whereIn('user_id', $ids)->delete();
        DB::table("social_google_accounts")->whereIn('user_id', $ids)->delete();

        return response()->json(['success' => "Users successfully moved to trash."]);
    }

    public function downloadPDF() {
        $user = \App\User::all();
        $pdf = PDF::loadview('users.pdf', ['user' => $user]);
        //return $pdf->download('list-admin.pdf'); //Download PDF immadiately
        return $pdf->stream(); 
    }

    public function downloadExcel() {
        return Excel::download(new UserExport, 'list-users.xlsx');
    }

    public function downloadWord() { 
        $user = \App\User::all();
        $headers = array(
            "Content-type"        => "text/html",
            "Content-Disposition" => "attachment;Filename=list-users.doc"
        );
        $content =  view('users.pdf', ['user' => $user])->render();
        return \Response::make($content, 200, $headers);
    }

    public function downloadActiveAdminPDF() {
        $user = \App\User::where('status', "ACTIVE")->get();
        $pdf = PDF::loadview('users.pdf', ['user' => $user]);
        //return $pdf->download('list-admin.pdf'); //Download PDF immadiately
        return $pdf->stream(); 
    }

    public function downloadActiveExcel() {
        return Excel::download(new ActiveUserExport, 'list-users.xlsx');
    }

    public function downloadActiveAdminWord() {
        $user = \App\User::where('status', "ACTIVE")->get();
        $headers = array(
            "Content-type"        => "text/html",
            "Content-Disposition" => "attachment;Filename=report.doc"
        );
        $content =  view('users.pdf', ['user' => $user])->render();
        return \Response::make($content, 200, $headers);
    }

    public function downloadInactiveAdminPDF() {
        $user = \App\User::where('status', "INACTIVE")->get();
        $pdf = PDF::loadview('users.pdf', ['user' => $user]);
        //return $pdf->download('list-admin.pdf'); //Download PDF immadiately
        return $pdf->stream(); 
    }

    public function downloadInactiveExcel() {
        return Excel::download(new InactiveUserExport, 'list-users.xlsx');
    }

    public function downloadInactiveAdminWord() {
        $user = \App\User::where('status', "INACTIVE")->get();
        $headers = array(
            "Content-type"        => "text/html",
            "Content-Disposition" => "attachment;Filename=report.doc"
        );
        $content =  view('users.pdf', ['user' => $user])->render();
        return \Response::make($content, 200, $headers);
    }

    public function ajaxSearch(Request $request){
        $keyword = $request->get('q');

        $categories = DB::table('roles')->where("name", "LIKE", "%$keyword%")->get();

        return $categories;
    }
}
