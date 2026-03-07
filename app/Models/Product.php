<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $product_id
 * @property int $supplier_id
 * @property int $category_id
 * @property string $product_name
 * @property string|null $description
 * @property string $unit
 * @property float|null $markup_rule
 * @property string|null $image_path
 * @property string|null $archive
 */
class Product extends Model
{
    use HasFactory;

    protected $primaryKey = 'product_id';

    protected $fillable = [
        'supplier_id',
        'category_id',
        'product_name',
        'description',
        'unit',
        'markup_rule',
        'image_path',
        'archive',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'supplier_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'category_id');
    }
}
