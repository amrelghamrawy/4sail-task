<?php

namespace App\Classes ;

use App\Interfaces\CheckoutStrategy;

class TaxAndServiceCheckoutStrategy implements CheckoutStrategy
{
    public function calculateTotal($amount)
    {
        $taxes = $amount * 0.14;
        $serviceCharge = $amount * 0.2;
    
        return number_format($amount + $taxes + $serviceCharge , 2 ,'.' , '');
    }

    public function printInvoice($tableNumber, $totalAmount)
    {
        // Logic to print invoice with taxes and service details for the table
    }
}