<?php

namespace App\Models\SuperAdmin;

use App\Models\BaseModel;

/**
 * App\Models\SuperAdmin\FaqCategory
 *
 * @property int $id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|Faq[] $faqs
 * @property-read int|null $faqs_count
 * @method static Builder|FaqCategory newModelQuery()
 * @method static Builder|FaqCategory newQuery()
 * @method static Builder|FaqCategory query()
 * @method static Builder|FaqCategory whereCreatedAt($value)
 * @method static Builder|FaqCategory whereId($value)
 * @method static Builder|FaqCategory whereName($value)
 * @method static Builder|FaqCategory whereUpdatedAt($value)
 * @mixin Eloquent
 */
class FaqCategory extends BaseModel
{

    public function faqs()
    {
        return $this->hasMany(Faq::class, 'faq_category_id', 'id');
    }

}
