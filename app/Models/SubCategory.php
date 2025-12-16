<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SubCategory extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = ['category_id', 'name'];

    protected $searchableFields = ['*'];

    protected $table = 'sub_categories';

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function values()
    {
        return $this->hasMany(Value::class);
    }
}
