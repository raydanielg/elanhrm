<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\HasCompany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\ProjectCategory
 *
 * @property int $id
 * @property string $category_name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $added_by
 * @property int|null $last_updated_by
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Project[] $project
 * @property-read int|null $project_count
 * @method static \Illuminate\Database\Eloquent\Builder|ProjectCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProjectCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProjectCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProjectCategory whereAddedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProjectCategory whereCategoryName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProjectCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProjectCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProjectCategory whereLastUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProjectCategory whereUpdatedAt($value)
 * @property int|null $company_id
 * @property-read \App\Models\Company|null $company
 * @method static \Illuminate\Database\Eloquent\Builder|ProjectCategory whereCompanyId($value)
 * @mixin \Eloquent
 */
class ProjectSubCategory extends BaseModel
{

    // use HasCompany;

    protected $table = 'project_sub_categories';

    public function projectCategory(): BelongsTo
    {
        return $this->belongsTo(projectCategory::class, 'category_id');
    }
}
