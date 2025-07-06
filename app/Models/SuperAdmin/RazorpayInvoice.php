<?php

namespace App\Models\SuperAdmin;

use App\Models\Company;
use App\Models\BaseModel;

/**
 * App\Models\SuperAdmin\RazorpayInvoice
 *
 * @property int $id
 * @property int $company_id
 * @property int|null $currency_id
 * @property string $invoice_id
 * @property string $subscription_id
 * @property string|null $order_id
 * @property int $package_id
 * @property string $transaction_id
 * @property string $amount
 * @property Carbon|null $pay_date
 * @property Carbon|null $next_pay_date
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Company $company
 * @property-read Package $package
 * @method static Builder|RazorpayInvoice newModelQuery()
 * @method static Builder|RazorpayInvoice newQuery()
 * @method static Builder|RazorpayInvoice query()
 * @method static Builder|RazorpayInvoice whereAmount($value)
 * @method static Builder|RazorpayInvoice whereCompanyId($value)
 * @method static Builder|RazorpayInvoice whereCreatedAt($value)
 * @method static Builder|RazorpayInvoice whereCurrencyId($value)
 * @method static Builder|RazorpayInvoice whereId($value)
 * @method static Builder|RazorpayInvoice whereInvoiceId($value)
 * @method static Builder|RazorpayInvoice whereNextPayDate($value)
 * @method static Builder|RazorpayInvoice whereOrderId($value)
 * @method static Builder|RazorpayInvoice wherePackageId($value)
 * @method static Builder|RazorpayInvoice wherePayDate($value)
 * @method static Builder|RazorpayInvoice whereSubscriptionId($value)
 * @method static Builder|RazorpayInvoice whereTransactionId($value)
 * @method static Builder|RazorpayInvoice whereUpdatedAt($value)
 * @mixin Eloquent
 */
class RazorpayInvoice extends BaseModel
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

    public function currency()
    {
        return $this->belongsTo(GlobalCurrency::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

}
