<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Submission
 * 
 * @property int $id
 * @property string $user_id
 * @property string $problem_id
 * @property string $code
 * @property string $language
 * @property string|null $status
 * @property int|null $score
 * @property Carbon|null $submitted_at
 * @property int|null $execution_time
 * @property int|null $memory_used
 * @property string|null $error_message
 * 
 * @property User $user
 * @property Problem $problem
 * @property Collection|SubmissionTestcase[] $submission_testcases
 *
 * @package App\Models
 */
class Submission extends Model
{
	protected $table = 'Submissions';
	public $timestamps = false;

	protected $casts = [
		'score' => 'int',
		'submitted_at' => 'datetime',
		'execution_time' => 'int',
		'memory_used' => 'int'
	];

	protected $fillable = [
		'user_id',
		'problem_id',
		'code',
		'language',
		'status',
		'score',
		'submitted_at',
		'execution_time',
		'memory_used',
		'error_message'
	];

	public function user()
	{
		return $this->belongsTo(User::class, 'user_id');
	}

	public function problem()
	{
		return $this->belongsTo(Problem::class, 'problem_id');
	}

	public function submission_testcases()
	{
		return $this->hasMany(SubmissionTestcase::class, 'submission_id');
	}
}
