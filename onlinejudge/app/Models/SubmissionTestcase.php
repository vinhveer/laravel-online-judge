<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SubmissionTestcase
 * 
 * @property int $id
 * @property int $submission_id
 * @property string $testcase_name
 * @property string $status
 * @property int|null $execution_time
 * @property int|null $memory_used
 * @property string|null $output
 * @property string|null $error_message
 * @property int|null $score
 * @property Carbon|null $judged_at
 * 
 * @property Submission $submission
 *
 * @package App\Models
 */
class SubmissionTestcase extends Model
{
	protected $table = 'SubmissionTestcases';
	public $timestamps = false;

	protected $casts = [
		'submission_id' => 'int',
		'execution_time' => 'int',
		'memory_used' => 'int',
		'score' => 'int',
		'judged_at' => 'datetime'
	];

	protected $fillable = [
		'submission_id',
		'testcase_name',
		'status',
		'execution_time',
		'memory_used',
		'output',
		'error_message',
		'score',
		'judged_at'
	];

	public function submission()
	{
		return $this->belongsTo(Submission::class, 'submission_id');
	}
}
