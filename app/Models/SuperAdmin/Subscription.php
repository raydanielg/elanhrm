<?php

namespace App\Models\SuperAdmin;

use App\Models\Company;
use App\Models\BaseModel;

class Subscription extends BaseModel
{

    protected $dates = ['created_at'];

    protected $casts = ['created_at'];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

}
