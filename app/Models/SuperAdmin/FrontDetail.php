<?php

namespace App\Models\SuperAdmin;

use App\Models\BaseModel;
use App\Traits\HasMaskImage;

/**
 * App\Models\SuperAdmin\FrontDetail
 *
 * @property int $id
 * @property string $get_started_show
 * @property string $sign_in_show
 * @property string|null $address
 * @property string|null $phone
 * @property string|null $email
 * @property string|null $image
 * @property string|null $social_links
 * @property string|null $primary_color
 * @property string|null $custom_css
 * @property string|null $custom_css_theme_two
 * @property string|null $locale
 * @property string|null $contact_html
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read mixed $image_url
 * @property-read mixed $light_color
 * @method static Builder|FrontDetail newModelQuery()
 * @method static Builder|FrontDetail newQuery()
 * @method static Builder|FrontDetail query()
 * @method static Builder|FrontDetail whereAddress($value)
 * @method static Builder|FrontDetail whereContactHtml($value)
 * @method static Builder|FrontDetail whereCreatedAt($value)
 * @method static Builder|FrontDetail whereCustomCss($value)
 * @method static Builder|FrontDetail whereCustomCssThemeTwo($value)
 * @method static Builder|FrontDetail whereEmail($value)
 * @method static Builder|FrontDetail whereGetStartedShow($value)
 * @method static Builder|FrontDetail whereId($value)
 * @method static Builder|FrontDetail whereLocale($value)
 * @method static Builder|FrontDetail wherePhone($value)
 * @method static Builder|FrontDetail wherePrimaryColor($value)
 * @method static Builder|FrontDetail whereSignInShow($value)
 * @method static Builder|FrontDetail whereSocialLinks($value)
 * @method static Builder|FrontDetail whereUpdatedAt($value)
 * @mixin Eloquent
 */
class FrontDetail extends BaseModel
{

    use HasMaskImage;
    protected $appends = ['image_url', 'light_color', 'background_image_url'];

    public function getBackgroundImageUrlAttribute()

    {
        return ($this->background_image) ? $this->generateMaskedImageAppUrl('front/homepage-background/' . $this->background_image) : null;
    }

    public function getImageUrlAttribute()
    {
        return ($this->image) ? asset_url_local_s3('front/' . $this->image) : asset('saas/img/home/home-crm.png');
    }

    public function getLightColorAttribute()
    {
        if (strlen($this->primary_color) === 7) {
            return $this->primary_color . '26';
        }

        return $this->primary_color;
    }

}
