<?php

namespace Modules\CheckPayments\Entities;

use Modules\CheckPayments\Scope\CheckPaymentsScope;
use Modules\Gateway\Entities\Gateway;

class CheckPayments extends Gateway
{
    protected $table = 'gateways';
    protected $appends = ['image_url'];

    /**
     * Global scope for check payment
     *
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope(new CheckPaymentsScope);
    }
}
