<?php

namespace App\Models\SuperAdmin;

use App\Models\LanguageSetting;
use App\Models\BaseModel;

/**
 * App\Models\SuperAdmin\FrontFaq
 *
 * @property int $id
 * @property string $question
 * @property string $answer
 * @property int|null $language_setting_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read LanguageSetting|null $language
 * @method static Builder|FrontFaq newModelQuery()
 * @method static Builder|FrontFaq newQuery()
 * @method static Builder|FrontFaq query()
 * @method static Builder|FrontFaq whereAnswer($value)
 * @method static Builder|FrontFaq whereCreatedAt($value)
 * @method static Builder|FrontFaq whereId($value)
 * @method static Builder|FrontFaq whereLanguageSettingId($value)
 * @method static Builder|FrontFaq whereQuestion($value)
 * @method static Builder|FrontFaq whereUpdatedAt($value)
 * @mixin Eloquent
 */
class FrontFaq extends BaseModel
{

    protected $guarded = ['id'];

    public function language()
    {
        return $this->belongsTo(LanguageSetting::class, 'language_setting_id');
    }

}
