<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Value extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = ['user_id', 'sub_category_id', 'nilai', 'tahun'];

    protected $searchableFields = ['*'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class);
    }
}
