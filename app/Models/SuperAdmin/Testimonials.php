<?php

namespace App\Models\SuperAdmin;

use App\Models\LanguageSetting;
use App\Models\BaseModel;

/**
 * App\Models\SuperAdmin\Testimonials
 *
 * @property int $id
 * @property string $name
 * @property string|null $comment
 * @property float|null $rating
 * @property int|null $language_setting_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read LanguageSetting|null $language
 * @method static Builder|Testimonials newModelQuery()
 * @method static Builder|Testimonials newQuery()
 * @method static Builder|Testimonials query()
 * @method static Builder|Testimonials whereComment($value)
 * @method static Builder|Testimonials whereCreatedAt($value)
 * @method static Builder|Testimonials whereId($value)
 * @method static Builder|Testimonials whereLanguageSettingId($value)
 * @method static Builder|Testimonials whereName($value)
 * @method static Builder|Testimonials whereRating($value)
 * @method static Builder|Testimonials whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Testimonials extends BaseModel
{

    protected $guarded = ['id'];

    public function language()
    {
        return $this->belongsTo(LanguageSetting::class, 'language_setting_id');
    }

}
