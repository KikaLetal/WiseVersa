<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DictItems extends Model
{
    use HasFactory;
	
	protected $fillable=['list_id', 'word', 'translation', 'mastered'] ;

    protected function casts(): array
    {
        return [
            'mastered' => 'boolean',
        ];
    }

    public function list(): BelongsTo
    {
        return $this->belongsTo(DictLists::class, 'list_id');
    }
}
