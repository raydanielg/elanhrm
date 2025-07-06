<?php

namespace App\Models\SuperAdmin;

use App\Models\BaseModel;
use App\Models\LanguageSetting;

/**
 * App\Models\SuperAdmin\Feature
 *
 * @property int $id
 * @property int|null $language_setting_id
 * @property string $title
 * @property string|null $description
 * @property string|null $image
 * @property string|null $icon
 * @property string $type
 * @property int|null $front_feature_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read mixed $image_url
 * @property-read LanguageSetting|null $language
 * @method static Builder|Feature newModelQuery()
 * @method static Builder|Feature newQuery()
 * @method static Builder|Feature query()
 * @method static Builder|Feature whereCreatedAt($value)
 * @method static Builder|Feature whereDescription($value)
 * @method static Builder|Feature whereFrontFeatureId($value)
 * @method static Builder|Feature whereIcon($value)
 * @method static Builder|Feature whereId($value)
 * @method static Builder|Feature whereImage($value)
 * @method static Builder|Feature whereLanguageSettingId($value)
 * @method static Builder|Feature whereTitle($value)
 * @method static Builder|Feature whereType($value)
 * @method static Builder|Feature whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Feature extends BaseModel
{

    protected $appends = ['image_url'];

    public function language()
    {
        return $this->belongsTo(LanguageSetting::class, 'language_setting_id');
    }

    public function getImageUrlAttribute()
    {
        if ($this->type == 'image' && is_null($this->image)) {
            if ($this->title == 'Meet Your Business Needs') {
                return asset('saas/img/svg/mock-banner.svg');
            }

            if ($this->title == 'Analyse Your Workflow') {
                return asset('saas/img/svg/mock-2.svg');
            }

            if ($this->title == 'Manage your support tickets efficiently') {
                return asset('saas/img/svg/mock-1.svg');
            }
        }

        if ($this->type == 'apps') {
            if (!is_null($this->image)) {
                return asset_url_local_s3('front/feature/' . $this->image);
            }

            if (strtolower($this->title) == 'onesignal') {
                return asset('saas/img/pages/onesignal.svg');
            }

            if (strtolower($this->title) == 'paypal') {
                return asset('saas/img/pages/paypal.svg');
            }

            if (strtolower($this->title) == 'slack') {
                return asset('saas/img/pages/slack-new-logo.svg');
            }

            if (strtolower($this->title) == 'pusher') {
                return asset('saas/img/pages/pusher.svg');
            }

            return asset('saas/img/pages/app-' . (($this->id) % 6) . '.png');
        }

        return ($this->image) ? asset_url_local_s3('front/feature/' . $this->image) : asset('front/img/tools.png');
    }

}
