<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Attributes\Fillable;

/**
 * @property int $id
 * @property string $name
 */
#[Fillable(['name'])]
class Tag extends Model
{
    use SoftDeletes;
}
