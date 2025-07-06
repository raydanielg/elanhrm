<?php

namespace App\Models\SuperAdmin;

use App\Models\Company;
use App\Models\BaseModel;

/**
 * App\Models\SuperAdmin\PaypalInvoice
 *
 * @property int $id
 * @property int|null $company_id
 * @property int|null $currency_id
 * @property int|null $package_id
 * @property float|null $sub_total
 * @property float|null $total
 * @property string|null $transaction_id
 * @property string|null $remarks
 * @property string|null $billing_frequency
 * @property int|null $billing_interval
 * @property Carbon|null $pay_date
 * @property Carbon|null $next_pay_date
 * @property string|null $recurring
 * @property string|null $status
 * @property string|null $plan_id
 * @property string|null $event_id
 * @property string|null $end_on
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Company|null $company
 * @property-read Package|null $package
 * @method static Builder|PaypalInvoice newModelQuery()
 * @method static Builder|PaypalInvoice newQuery()
 * @method static Builder|PaypalInvoice query()
 * @method static Builder|PaypalInvoice whereBillingFrequency($value)
 * @method static Builder|PaypalInvoice whereBillingInterval($value)
 * @method static Builder|PaypalInvoice whereCompanyId($value)
 * @method static Builder|PaypalInvoice whereCreatedAt($value)
 * @method static Builder|PaypalInvoice whereCurrencyId($value)
 * @method static Builder|PaypalInvoice whereEndOn($value)
 * @method static Builder|PaypalInvoice whereEventId($value)
 * @method static Builder|PaypalInvoice whereId($value)
 * @method static Builder|PaypalInvoice whereNextPayDate($value)
 * @method static Builder|PaypalInvoice wherePackageId($value)
 * @method static Builder|PaypalInvoice wherePaidOn($value)
 * @method static Builder|PaypalInvoice wherePlanId($value)
 * @method static Builder|PaypalInvoice whereRecurring($value)
 * @method static Builder|PaypalInvoice whereRemarks($value)
 * @method static Builder|PaypalInvoice whereStatus($value)
 * @method static Builder|PaypalInvoice whereSubTotal($value)
 * @method static Builder|PaypalInvoice whereTotal($value)
 * @method static Builder|PaypalInvoice whereTransactionId($value)
 * @method static Builder|PaypalInvoice whereUpdatedAt($value)
 * @mixin Eloquent
 * @property Carbon|null $paid_on
 */
class PaypalInvoice extends BaseModel
{

    protected $dates = ['paid_on', 'next_pay_date'];

    protected $casts = [
        'paid_on' => 'datetime',
        'next_pay_date' => 'datetime',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function currency()
    {
        return $this->belongsTo(GlobalCurrency::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

}
