<?php

namespace App\Models\SuperAdmin;

use App\Models\Company;
use App\Models\BaseModel;

/**
 * App\Models\SuperAdmin\AuthorizeInvoice
 *
 * @property int $id
 * @property int $company_id
 * @property int $package_id
 * @property string|null $transaction_id
 * @property string|null $amount
 * @property Carbon|null $pay_date
 * @property Carbon|null $next_pay_date
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Company $company
 * @property-read Package $package
 * @method static Builder|AuthorizeInvoice newModelQuery()
 * @method static Builder|AuthorizeInvoice newQuery()
 * @method static Builder|AuthorizeInvoice query()
 * @method static Builder|AuthorizeInvoice whereAmount($value)
 * @method static Builder|AuthorizeInvoice whereCompanyId($value)
 * @method static Builder|AuthorizeInvoice whereCreatedAt($value)
 * @method static Builder|AuthorizeInvoice whereId($value)
 * @method static Builder|AuthorizeInvoice whereNextPayDate($value)
 * @method static Builder|AuthorizeInvoice wherePackageId($value)
 * @method static Builder|AuthorizeInvoice wherePayDate($value)
 * @method static Builder|AuthorizeInvoice whereTransactionId($value)
 * @method static Builder|AuthorizeInvoice whereUpdatedAt($value)
 * @mixin Eloquent
 */
class AuthorizeInvoice extends BaseModel
{

    protected $dates = [
        'pay_date',
        'next_pay_date',
    ];

    protected $casts = [
        'pay_date' => 'datetime',
        'next_pay_date' => 'datetime',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

}
