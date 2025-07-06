<?php

namespace App\Models\SuperAdmin;

use App\Models\Company;
use App\Models\BaseModel;

class PaystackSubscription extends BaseModel
{

    protected $dates = ['created_at'];

    protected $casts = ['created_at'];

    protected $table = 'paystack_subscriptions';

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

}
