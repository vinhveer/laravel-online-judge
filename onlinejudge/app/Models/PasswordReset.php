<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PasswordReset
 * 
 * @property int $id
 * @property string $user_id
 * @property string $token
 * @property Carbon $expires_at
 * @property Carbon|null $created_at
 * 
 * @property User $user
 *
 * @package App\Models
 */
class PasswordReset extends Model
{
	protected $table = 'PasswordResets';
	public $timestamps = false;

	protected $casts = [
		'expires_at' => 'datetime'
	];

	protected $hidden = [
		'token'
	];

	protected $fillable = [
		'user_id',
		'token',
		'expires_at'
	];

	public function user()
	{
		return $this->belongsTo(User::class, 'user_id');
	}
}
