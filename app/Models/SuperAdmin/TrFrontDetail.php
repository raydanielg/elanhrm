<?php

namespace App\Models\SuperAdmin;

use App\Models\LanguageSetting;
use App\Models\BaseModel;

/**
 * App\Models\SuperAdmin\TrFrontDetail
 *
 * @property int $id
 * @property int|null $language_setting_id
 * @property string $header_title
 * @property string $header_description
 * @property string $image
 * @property string|null $feature_title
 * @property string|null $feature_description
 * @property string|null $price_title
 * @property string|null $price_description
 * @property string|null $task_management_title
 * @property string|null $task_management_detail
 * @property string|null $manage_bills_title
 * @property string|null $manage_bills_detail
 * @property string|null $teamates_title
 * @property string|null $teamates_detail
 * @property string|null $favourite_apps_title
 * @property string|null $favourite_apps_detail
 * @property string|null $cta_title
 * @property string|null $cta_detail
 * @property string|null $client_title
 * @property string|null $client_detail
 * @property string|null $testimonial_title
 * @property string|null $testimonial_detail
 * @property string|null $faq_title
 * @property string|null $faq_detail
 * @property string|null $footer_copyright_text
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read mixed $image_url
 * @method static Builder|TrFrontDetail newModelQuery()
 * @method static Builder|TrFrontDetail newQuery()
 * @method static Builder|TrFrontDetail query()
 * @method static Builder|TrFrontDetail whereClientDetail($value)
 * @method static Builder|TrFrontDetail whereClientTitle($value)
 * @method static Builder|TrFrontDetail whereCreatedAt($value)
 * @method static Builder|TrFrontDetail whereCtaDetail($value)
 * @method static Builder|TrFrontDetail whereCtaTitle($value)
 * @method static Builder|TrFrontDetail whereFaqDetail($value)
 * @method static Builder|TrFrontDetail whereFaqTitle($value)
 * @method static Builder|TrFrontDetail whereFavouriteAppsDetail($value)
 * @method static Builder|TrFrontDetail whereFavouriteAppsTitle($value)
 * @method static Builder|TrFrontDetail whereFeatureDescription($value)
 * @method static Builder|TrFrontDetail whereFeatureTitle($value)
 * @method static Builder|TrFrontDetail whereFooterCopyrightText($value)
 * @method static Builder|TrFrontDetail whereHeaderDescription($value)
 * @method static Builder|TrFrontDetail whereHeaderTitle($value)
 * @method static Builder|TrFrontDetail whereId($value)
 * @method static Builder|TrFrontDetail whereImage($value)
 * @method static Builder|TrFrontDetail whereLanguageSettingId($value)
 * @method static Builder|TrFrontDetail whereManageBillsDetail($value)
 * @method static Builder|TrFrontDetail whereManageBillsTitle($value)
 * @method static Builder|TrFrontDetail wherePriceDescription($value)
 * @method static Builder|TrFrontDetail wherePriceTitle($value)
 * @method static Builder|TrFrontDetail whereTaskManagementDetail($value)
 * @method static Builder|TrFrontDetail whereTaskManagementTitle($value)
 * @method static Builder|TrFrontDetail whereTeamatesDetail($value)
 * @method static Builder|TrFrontDetail whereTeamatesTitle($value)
 * @method static Builder|TrFrontDetail whereTestimonialDetail($value)
 * @method static Builder|TrFrontDetail whereTestimonialTitle($value)
 * @method static Builder|TrFrontDetail whereUpdatedAt($value)
 * @mixin Eloquent
 * @property-read LanguageSetting|null $language
 */
class TrFrontDetail extends BaseModel
{

    protected $guarded = ['id'];

    public function getImageUrlAttribute()
    {
        return ($this->image) ? asset_url_local_s3('front/' . $this->image) : asset('saas/img/home/home-crm.png');
    }

    public function language()
    {
        return $this->belongsTo(LanguageSetting::class, 'language_setting_id');
    }

}
