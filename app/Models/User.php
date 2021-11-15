<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Laravel\Scout\Searchable;

;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, Searchable;


    protected $fillable = [
        "name",
        "family",
        "national_number",
        "birthdate",
        'mobile',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $with = ["roles"];

    public function profiles()
    {
        return $this->hasMany(Profile::class, "user_id");
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, "users_roles", "user_id", "role_id");
    }

    public function addresses()
    {
        return $this->morphMany(Address::class, "addressable");
    }

    public function images()
    {
        return $this->morphMany(Attachment::class, "attachable");
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, "user_id");
    }

    public function hasRole(string $role, $rolesCollection): bool
    {
        $check = false;
        if ($role) {
            //check exist given role in user roles
            $check = in_array($role, $this->roles->pluck("name")->toArray());
        }


        if (!$check) {
            //get given role level
            $roleLevel = $rolesCollection->where('name', '=', $role)->first()->level;

            //max of user role level
            $userLevel = $this->roles->min("level");

            //compare user level and given role level
            if ($userLevel < $roleLevel) {
                $check = true;
            }
        }
        return $check;
    }
}
