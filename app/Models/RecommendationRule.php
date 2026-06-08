<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecommendationRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_code',
        'category',
        'severity_level',
        'min_score',
        'max_score',
        'recommendation_text',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'min_score' => 'integer',
        'max_score' => 'integer',
    ];

    /**
     * Dokter / admin yang membuat aturan rekomendasi
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope: hanya aturan aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: filter berdasarkan skor
     */
    public function scopeMatchScore($query, float $score)
    {
        return $query
            ->where('min_score', '<=', $score)
            ->where('max_score', '>=', $score);
    }

    /**
     * Scope: filter berdasarkan grup risiko
     */
    public function scopeForGroup($query, string $groupCode)
    {
        return $query->where('group_code', $groupCode);
    }
}
