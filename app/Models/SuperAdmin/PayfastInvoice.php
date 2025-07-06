<?php

namespace App\Models\SuperAdmin;

use App\Models\Company;
use App\Models\BaseModel;

/**
 * App\Models\SuperAdmin\PayfastInvoice
 *
 * @property int $id
 * @property int|null $company_id
 * @property int|null $package_id
 * @property string|null $m_payment_id
 * @property string|null $pf_payment_id
 * @property string|null $payfast_plan
 * @property string|null $amount
 * @property Carbon|null $pay_date
 * @property Carbon|null $next_pay_date
 * @property string|null $signature
 * @property string|null $token
 * @property string|null $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Company|null $company
 * @property-read Package|null $package
 * @method static Builder|PayfastInvoice newModelQuery()
 * @method static Builder|PayfastInvoice newQuery()
 * @method static Builder|PayfastInvoice query()
 * @method static Builder|PayfastInvoice whereAmount($value)
 * @method static Builder|PayfastInvoice whereCompanyId($value)
 * @method static Builder|PayfastInvoice whereCreatedAt($value)
 * @method static Builder|PayfastInvoice whereId($value)
 * @method static Builder|PayfastInvoice whereMPaymentId($value)
 * @method static Builder|PayfastInvoice whereNextPayDate($value)
 * @method static Builder|PayfastInvoice wherePackageId($value)
 * @method static Builder|PayfastInvoice wherePayDate($value)
 * @method static Builder|PayfastInvoice wherePayfastPlan($value)
 * @method static Builder|PayfastInvoice wherePfPaymentId($value)
 * @method static Builder|PayfastInvoice whereSignature($value)
 * @method static Builder|PayfastInvoice whereStatus($value)
 * @method static Builder|PayfastInvoice whereToken($value)
 * @method static Builder|PayfastInvoice whereUpdatedAt($value)
 * @mixin Eloquent
 */
class PayfastInvoice extends BaseModel
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
