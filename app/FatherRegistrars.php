<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model; 

class FatherRegistrars extends Model 
{
    use Notifiable;
    use SoftDeletes; //trash user
    use HasRoles; 

    protected $guard_name = 'web';

    public function student_registrars(){ 
        return $this->belongsToMany('App\StudentRegistrars')->withTrashed();
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

    protected $table = "father_registrars";
    protected $primaryKey = "id";
}
