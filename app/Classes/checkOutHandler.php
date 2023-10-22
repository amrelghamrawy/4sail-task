<?php
namespace App\Classes ; 

class checkOutHandler{

    public $checkoutStrategies = [
        'tax_and_service' => TaxAndServiceCheckoutStrategy::class,
        'service_only' => ServiceOnlyCheckoutStrategy::class,
    ];

    public function getCheckoutStrategy($method)
    {
        $strategyClass = $this->checkoutStrategies[$method];
        return new $strategyClass();
    }
}