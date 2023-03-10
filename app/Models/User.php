<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'username',
        'phone',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function contacts()
    {
        return $this->belongsToMany(Contact::class);
    }

    /**
 * Get the username attribute based on the user's first and last name.
 *
 * @return string
 */
public function getUsernameAttribute()
{
    $username = strtolower($this->first_name . '.' . $this->last_name);

    // Remove any non-alphanumeric characters and replace spaces with underscores
    $username = preg_replace('/[^a-z0-9 ]/i', '', $username);
    $username = str_replace(' ', '_', $username);

    // Check if the username already exists in the database
    $count = User::where('username', $username)->where('id', '!=', $this->id)->count();

    // If the username is already taken, append a number to make it unique
    if ($count > 0) {
        $username .= $count;
    }

    return $username;
}
}
