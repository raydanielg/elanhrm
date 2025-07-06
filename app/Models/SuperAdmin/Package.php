<?php

namespace App\Models\SuperAdmin;

use App\Models\Company;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\SuperAdmin\Package
 *
 * @property int $id
 * @property int|null $currency_id
 * @property string $name
 * @property string|null $description
 * @property int $max_storage_size
 * @property int $max_file_size
 * @property string $annual_price
 * @property string $monthly_price
 * @property int $billing_cycle
 * @property int $max_employees
 * @property string $sort
 * @property string $module_in_package
 * @property string $stripe_annual_plan_id
 * @property string $stripe_monthly_plan_id
 * @property string|null $razorpay_annual_plan_id
 * @property string|null $razorpay_monthly_plan_id
 * @property string|null $default
 * @property string|null $paystack_monthly_plan_id
 * @property string|null $paystack_annual_plan_id
 * @property int $is_private
 * @property string $storage_unit
 * @property int $is_recommended
 * @property int $is_free
 * @property int $contact_to_buy
 * @property int $is_auto_renew
 * @property string|null $monthly_status
 * @property string|null $annual_status
 * @property string|null $contact_button_text
 * @property string|null $contact_url
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|Package newModelQuery()
 * @method static Builder|Package newQuery()
 * @method static Builder|Package query()
 * @method static Builder|Package whereAnnualPrice($value)
 * @method static Builder|Package whereAnnualStatus($value)
 * @method static Builder|Package whereBillingCycle($value)
 * @method static Builder|Package whereCreatedAt($value)
 * @method static Builder|Package whereCurrencyId($value)
 * @method static Builder|Package whereDefault($value)
 * @method static Builder|Package whereDescription($value)
 * @method static Builder|Package whereId($value)
 * @method static Builder|Package whereIsAutoRenew($value)
 * @method static Builder|Package whereIsFree($value)
 * @method static Builder|Package whereIsPrivate($value)
 * @method static Builder|Package whereIsRecommended($value)
 * @method static Builder|Package whereMaxEmployees($value)
 * @method static Builder|Package whereMaxFileSize($value)
 * @method static Builder|Package whereMaxStorageSize($value)
 * @method static Builder|Package whereModuleInPackage($value)
 * @method static Builder|Package whereMonthlyPrice($value)
 * @method static Builder|Package whereMonthlyStatus($value)
 * @method static Builder|Package whereName($value)
 * @method static Builder|Package wherePaystackAnnualPlanId($value)
 * @method static Builder|Package wherePaystackMonthlyPlanId($value)
 * @method static Builder|Package whereRazorpayAnnualPlanId($value)
 * @method static Builder|Package whereRazorpayMonthlyPlanId($value)
 * @method static Builder|Package whereSort($value)
 * @method static Builder|Package whereStorageUnit($value)
 * @method static Builder|Package whereStripeAnnualPlanId($value)
 * @method static Builder|Package whereStripeMonthlyPlanId($value)
 * @method static Builder|Package whereUpdatedAt($value)
 * @mixin Eloquent
 * @property-read Collection|Company[] $companies
 * @property-read int|null $companies_count
 * @property-read \App\Models\SuperAdmin\GlobalCurrency|null $currency
 * @property-read mixed $formatted_annual_price
 * @property-read mixed $formatted_monthly_price
 */
class Package extends BaseModel
{

    use HasFactory;

    protected $guarded = ['id'];

    protected $appends = [
        'formatted_annual_price',
        'formatted_monthly_price'
    ];

    public function companies()
    {
        return $this->hasMany(Company::class);
    }

    public function formatSizeUnits($bytes)
    {
        if ($bytes >= 1024) {
            $bytes = number_format($bytes / 1024, 2) . ' GB';
        }
        elseif ($bytes > 1) {
            $bytes = $bytes . ' MB';
        }
        else {
            $bytes = '0 MB';
        }

        return $bytes;
    }

    /**
     * Convert bytes to megabytes (MB).
     *
     * @param int $bytes
     * @return float
     */
    public static function bytesToMB($bytes)
    {
        return round($bytes / 1048576, 2);
    }

    /**
     * Convert bytes to gigabytes (GB).
     *
     * @param int $bytes
     * @return float
     */
    public static function bytesToGB($bytes)
    {
        return round($bytes / 1073741824, 2);
    }

    /**
     * Convert bytes to gigabytes (GB) and megabytes (MB).
     *
     * @param int $bytes
     * @return string
     */
    public static function bytesToGBMB($bytes)
    {
        $gb = round($bytes / 1073741824, 2);
        $mb = round($bytes / 1048576, 2);

        if ($gb > 0) {
            return $gb . ' GB';
        }
        else {
            return $mb . ' MB';
        }
    }

    public function currency()
    {
        return $this->belongsTo(GlobalCurrency::class, 'currency_id')->withTrashed();
    }

    public function getFormattedAnnualPriceAttribute()
    {
        return global_currency_format($this->annual_price, $this->currency_id);
    }

    public function getFormattedMonthlyPriceAttribute()
    {
        return global_currency_format($this->monthly_price, $this->currency_id);
    }

}
