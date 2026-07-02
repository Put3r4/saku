<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MenuEvaluation extends Model
{
    use HasFactory;
    protected $fillable = [
        'menu_id',
        'criterion_id',
        'value',
    ];

    /**
     * Disable timestamps for this model.
     * MenuEvaluation is static data that doesn't need created_at or updated_at.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'value' => 'decimal:2',
        ];
    }

    /**
     * Get the menu that owns this evaluation.
     */
    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    /**
     * Get the criterion that this evaluation is for.
     */
    public function criterion(): BelongsTo
    {
        return $this->belongsTo(Criterion::class);
    }
}
