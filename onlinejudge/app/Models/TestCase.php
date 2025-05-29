<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TestCase
 * 
 * @property int $id
 * @property string $problem_id
 * @property string $input
 * @property string $expected_output
 * @property bool|null $is_sample
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Problem $problem
 *
 * @package App\Models
 */
class TestCase extends Model
{
	protected $table = 'TestCases';

	protected $casts = [
		'is_sample' => 'bool'
	];

	protected $fillable = [
		'problem_id',
		'input',
		'expected_output',
		'is_sample'
	];

	public function problem()
	{
		return $this->belongsTo(Problem::class, 'problem_id');
	}
}
