<?php

namespace App\Models\SuperAdmin;

use App\Models\Company;
use App\Models\BaseModel;

class GlobalSubscription extends BaseModel
{

    protected $table = 'global_subscriptions';
    protected $dates = ['created_at'];
    protected $casts = [
        'created_at',
        'pay_date' => 'datetime',
        'next_pay_date' => 'datetime',
        'subscribed_on_date' => 'datetime',
    ];
    protected $guarded = ['id'];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

}
