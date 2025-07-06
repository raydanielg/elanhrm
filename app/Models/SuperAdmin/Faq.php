<?php

namespace App\Models\SuperAdmin;

use App\Models\BaseModel;

/**
 * App\Models\SuperAdmin\Faq
 *
 * @property int $id
 * @property string $title
 * @property string $description
 * @property int $faq_category_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read FaqCategory $category
 * @property-read Collection|FaqFile[] $files
 * @property-read int|null $files_count
 * @property-read mixed $image_url
 * @method static Builder|Faq newModelQuery()
 * @method static Builder|Faq newQuery()
 * @method static Builder|Faq query()
 * @method static Builder|Faq whereCreatedAt($value)
 * @method static Builder|Faq whereDescription($value)
 * @method static Builder|Faq whereFaqCategoryId($value)
 * @method static Builder|Faq whereId($value)
 * @method static Builder|Faq whereTitle($value)
 * @method static Builder|Faq whereUpdatedAt($value)
 * @mixin Eloquent
 * @property string|null $image
 * @method static Builder|Faq whereImage($value)
 */
class Faq extends BaseModel
{

    public $appends = ['image_url'];

    public function getImageUrlAttribute()
    {
        return ($this->image) ? asset_url('faq-files/' . $this->id . '/' . $this->image) : asset('saas/img/svg/mock-2.svg');
    }

    public function category()
    {
        return $this->belongsTo(FaqCategory::class, 'faq_category_id');
    }

    public function files()
    {
        return $this->hasMany(FaqFile::class, 'faq_id');
    }

}
