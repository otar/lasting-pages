<?php

namespace App\Models;

use App\Traits\HasEncodedId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Page extends Model
{
    /** @use HasFactory<\Database\Factories\PageFactory> */
    use HasEncodedId, HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'url',
        'title',
        'is_pending',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'is_pending' => 'boolean',
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
