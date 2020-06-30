<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model; 

class StudentRegistrars extends Model
{
    use Notifiable;
    use SoftDeletes; //trash user
    use HasRoles;  

    protected $guard_name = 'web';  

    public function father_registrars(){
        return $this->belongsToMany('App\FatherRegistrars')->withTrashed(); 
    }

    public function mother_registrars(){
        return $this->belongsToMany('App\MotherRegistrars')->withTrashed(); 
    }

    /** 
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'username', 'gender', 'phone', 'email', 'password',
    ]; 

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $table = "student_registrars";
    protected $primaryKey = "id";
}
