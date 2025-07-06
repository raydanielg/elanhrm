<?php

namespace App\Models\SuperAdmin;

use App\Models\Invoice;
use App\Observers\SuperAdmin\InvoicePaymentReceivedObserver;
use App\Models\BaseModel;

class ClientPayment extends BaseModel
{

    protected $table = 'payments';

    protected $dates = ['paid_on'];

    protected $casts = [
        'paid_on' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        static::observe(InvoicePaymentReceivedObserver::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

}
