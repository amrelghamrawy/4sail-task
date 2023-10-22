<?php 

namespace App\Classes ;

use App\Interfaces\CheckoutStrategy;

class ServiceOnlyCheckoutStrategy implements CheckoutStrategy
{
    public function calculateTotal($amount)
    {
        $serviceCharge = $amount * 0.15;
        return number_format($amount + $serviceCharge , 2 ,'.' , '');
    }

    public function printInvoice($tableNumber, $totalAmount)
    {
        // Logic to print invoice with service charge details for the table
    }
}