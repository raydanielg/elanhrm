<?php

namespace App\Models\SuperAdmin;

use App\Models\LanguageSetting;
use App\Models\BaseModel;

/**
 * App\Models\SuperAdmin\FrontFeature
 *
 * @property int $id
 * @property int|null $language_setting_id
 * @property string|null $title
 * @property string|null $description
 * @property string $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|Feature[] $features
 * @property-read int|null $features_count
 * @method static Builder|FrontFeature newModelQuery()
 * @method static Builder|FrontFeature newQuery()
 * @method static Builder|FrontFeature query()
 * @method static Builder|FrontFeature whereCreatedAt($value)
 * @method static Builder|FrontFeature whereDescription($value)
 * @method static Builder|FrontFeature whereId($value)
 * @method static Builder|FrontFeature whereLanguageSettingId($value)
 * @method static Builder|FrontFeature whereStatus($value)
 * @method static Builder|FrontFeature whereTitle($value)
 * @method static Builder|FrontFeature whereUpdatedAt($value)
 * @mixin Eloquent
 * @property-read LanguageSetting|null $language
 */
class FrontFeature extends BaseModel
{

    public function features()
    {
        return $this->hasMany(Feature::class, 'front_feature_id');
    }

    public function language()
    {
        return $this->belongsTo(LanguageSetting::class, 'language_setting_id');
    }

}
