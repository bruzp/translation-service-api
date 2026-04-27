<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Attributes\Fillable;

/**
 * @property int $id
 * @property string $key
 * @property string $value
 * @property int $locale_id
 */
#[Fillable(['locale_id', 'key', 'value'])]
class Translation extends Model
{
    use SoftDeletes;

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'translation_tags');
    }

    public function locale(): BelongsTo
    {
        return $this->belongsTo(Locale::class);
    }
}
