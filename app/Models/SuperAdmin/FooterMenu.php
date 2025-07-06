<?php

namespace App\Models\SuperAdmin;

use App\Models\BaseModel;
use App\Models\LanguageSetting;

/**
 * App\Models\SuperAdmin\FooterMenu
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property string|null $video_link
 * @property string|null $video_embed
 * @property string|null $file_name
 * @property string|null $hash_name
 * @property string|null $external_link
 * @property string|null $type
 * @property string|null $status
 * @property int|null $language_setting_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read mixed $video_url
 * @property-read LanguageSetting|null $language
 * @method static Builder|FooterMenu newModelQuery()
 * @method static Builder|FooterMenu newQuery()
 * @method static Builder|FooterMenu query()
 * @method static Builder|FooterMenu whereCreatedAt($value)
 * @method static Builder|FooterMenu whereDescription($value)
 * @method static Builder|FooterMenu whereExternalLink($value)
 * @method static Builder|FooterMenu whereFileName($value)
 * @method static Builder|FooterMenu whereHashName($value)
 * @method static Builder|FooterMenu whereId($value)
 * @method static Builder|FooterMenu whereLanguageSettingId($value)
 * @method static Builder|FooterMenu whereName($value)
 * @method static Builder|FooterMenu whereSlug($value)
 * @method static Builder|FooterMenu whereStatus($value)
 * @method static Builder|FooterMenu whereType($value)
 * @method static Builder|FooterMenu whereUpdatedAt($value)
 * @method static Builder|FooterMenu whereVideoEmbed($value)
 * @method static Builder|FooterMenu whereVideoLink($value)
 * @mixin Eloquent
 */
class FooterMenu extends BaseModel
{

    protected $table = 'footer_menu';

    public function language()
    {
        return $this->belongsTo(LanguageSetting::class, 'language_setting_id');
    }

    public function getVideoUrlAttribute()
    {
        return ($this->file_name) ? asset_url('footer-files/' . $this->file_name) : '';
    }

}
