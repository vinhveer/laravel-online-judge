<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Laravel\Sanctum\HasApiTokens;

/**
 * Class User
 * 
 * @property string $id
 * @property string $username
 * @property string $email
 * @property string $password
 * @property Carbon|null $created_at
 * @property Carbon|null $email_verified_at
 * @property string|null $remember_token
 * @property Carbon|null $updated_at
 * @property bool $is_admin
 * @property bool $is_active
 * @property string|null $avatar
 * @property string|null $bio
 * @property string|null $last_login_ip
 * @property Carbon|null $last_login_at
 * 
 * @property Collection|Submission[] $submissions
 *
 * @package App\Models
 */
class User extends Authenticatable implements AuthenticatableContract
{
	use Notifiable, HasApiTokens;

	protected $table = 'Users';
	public $incrementing = false;

	protected $casts = [
		'email_verified_at' => 'datetime',
		'is_admin' => 'bool',
		'is_active' => 'bool',
		'last_login_at' => 'datetime',
	];

	protected $hidden = [
		'password',
		'remember_token'
	];

	protected $fillable = [
		'username',
		'email',
		'password',
		'email_verified_at',
		'remember_token',
		'is_admin',
		'is_active',
		'avatar',
		'bio',
		'last_login_ip',
		'last_login_at',
		'location',
		'website',
		'social_links'
	];

	public function submissions()
	{
		return $this->hasMany(Submission::class, 'user_id');
	}

	public function isActive()
	{
		return $this->is_active;
	}

	public function isAdmin()
	{
		return $this->is_admin;
	}

	public function updateLastLogin($ip)
	{
		$this->update([
			'last_login_at' => now(),
			'last_login_ip' => $ip
		]);
	}
}
