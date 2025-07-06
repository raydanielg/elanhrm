<?php

namespace App\Models\SuperAdmin;

use App\Models\LanguageSetting;
use App\Models\BaseModel;

/**
 * App\Models\SuperAdmin\SignUpSetting
 *
 * @property int $id
 * @property int|null $language_setting_id
 * @property string|null $message
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read LanguageSetting|null $language
 * @method static Builder|SignUpSetting newModelQuery()
 * @method static Builder|SignUpSetting newQuery()
 * @method static Builder|SignUpSetting query()
 * @method static Builder|SignUpSetting whereCreatedAt($value)
 * @method static Builder|SignUpSetting whereId($value)
 * @method static Builder|SignUpSetting whereLanguageSettingId($value)
 * @method static Builder|SignUpSetting whereMessage($value)
 * @method static Builder|SignUpSetting whereUpdatedAt($value)
 * @mixin Eloquent
 */
class SignUpSetting extends BaseModel
{

    public function language()
    {
        return $this->belongsTo(LanguageSetting::class, 'language_setting_id');
    }

}
