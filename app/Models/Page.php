<?php

namespace App\Models;

use App\Traits\HasEncodedId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
        'current_snapshot_id',
        'current_snapshot_version',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'is_pending' => 'boolean',
        'current_snapshot_version' => 'integer',
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany<PageSnapshot, $this>
     */
    public function snapshots(): HasMany
    {
        return $this->hasMany(PageSnapshot::class);
    }

    /**
     * @return BelongsTo<PageSnapshot, $this>
     */
    public function currentSnapshot(): BelongsTo
    {
        return $this->belongsTo(PageSnapshot::class, 'current_snapshot_id');
    }
}
