<?php

namespace App\Models\SuperAdmin;

use App\Models\LanguageSetting;
use App\Models\BaseModel;
use App\Traits\HasMaskImage;
use Illuminate\Database\Eloquent\Casts\Attribute;

/**
 * App\Models\SuperAdmin\SeoDetail
 *
 * @property int $id
 * @property string $page_name
 * @property string|null $seo_title
 * @property string|null $seo_keywords
 * @property string|null $seo_description
 * @property string|null $seo_author
 * @property int|null $language_setting_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read mixed $og_image_url
 * @method static Builder|SeoDetail newModelQuery()
 * @method static Builder|SeoDetail newQuery()
 * @method static Builder|SeoDetail query()
 * @method static Builder|SeoDetail whereCreatedAt($value)
 * @method static Builder|SeoDetail whereId($value)
 * @method static Builder|SeoDetail whereLanguageSettingId($value)
 * @method static Builder|SeoDetail wherePageName($value)
 * @method static Builder|SeoDetail whereSeoAuthor($value)
 * @method static Builder|SeoDetail whereSeoDescription($value)
 * @method static Builder|SeoDetail whereSeoKeywords($value)
 * @method static Builder|SeoDetail whereSeoTitle($value)
 * @method static Builder|SeoDetail whereUpdatedAt($value)
 * @mixin Eloquent
 * @property string|null $og_image
 * @method static Builder|SeoDetail whereOgImage($value)
 */
class SeoDetail extends BaseModel
{

    use HasMaskImage;

    protected $guarded = ['id'];

    protected $appends = ['og_image_url'];

    public function language()
    {
        return $this->belongsTo(LanguageSetting::class, 'language_setting_id');
    }

    public function getOgImageUrlAttribute()
    {
        return ($this->og_image) ? asset_url_local_s3('front/seo-detail/' . $this->og_image) : asset('saas/img/home/home-crm.png');
    }

    public function maskedOgImageUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                return ($this->og_image) ? $this->generateMaskedImageAppUrl('front/seo-detail/' . $this->og_image) : asset('saas/img/home/home-crm.png');
            },
        );

    }


}
