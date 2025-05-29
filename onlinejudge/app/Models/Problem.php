<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Problem
 * 
 * @property string $id
 * @property string $name
 * @property string $content
 * @property Carbon|null $created_at
 * @property string $difficulty
 * @property int $time_limit
 * @property int $memory_limit
 * @property bool $is_public
 * 
 * @property Collection|Submission[] $submissions
 * @property Collection|TestCase[] $test_cases
 *
 * @package App\Models
 */
class Problem extends Model
{
	protected $table = 'Problems';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'time_limit' => 'int',
		'memory_limit' => 'int',
		'is_public' => 'bool'
	];

	protected $fillable = [
		'name',
		'content',
		'difficulty',
		'time_limit',
		'memory_limit',
		'is_public'
	];

	public function submissions()
	{
		return $this->hasMany(Submission::class, 'problem_id');
	}

	public function test_cases()
	{
		return $this->hasMany(TestCase::class, 'problem_id');
	}
}
