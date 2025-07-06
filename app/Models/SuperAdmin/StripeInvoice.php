<?php

namespace App\Models\SuperAdmin;

use App\Models\Company;
use App\Models\BaseModel;

/**
 * App\Models\SuperAdmin\StripeInvoice
 *
 * @property int $id
 * @property int $company_id
 * @property string|null $invoice_id
 * @property int $package_id
 * @property string|null $transaction_id
 * @property string $amount
 * @property Carbon|null $pay_date
 * @property Carbon|null $next_pay_date
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Company $company
 * @property-read Package $package
 * @method static Builder|StripeInvoice newModelQuery()
 * @method static Builder|StripeInvoice newQuery()
 * @method static Builder|StripeInvoice query()
 * @method static Builder|StripeInvoice whereAmount($value)
 * @method static Builder|StripeInvoice whereCompanyId($value)
 * @method static Builder|StripeInvoice whereCreatedAt($value)
 * @method static Builder|StripeInvoice whereId($value)
 * @method static Builder|StripeInvoice whereInvoiceId($value)
 * @method static Builder|StripeInvoice whereNextPayDate($value)
 * @method static Builder|StripeInvoice wherePackageId($value)
 * @method static Builder|StripeInvoice wherePayDate($value)
 * @method static Builder|StripeInvoice whereTransactionId($value)
 * @method static Builder|StripeInvoice whereUpdatedAt($value)
 * @mixin Eloquent
 */
class StripeInvoice extends BaseModel
{

    protected $dates = ['pay_date', 'next_pay_date'];

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
