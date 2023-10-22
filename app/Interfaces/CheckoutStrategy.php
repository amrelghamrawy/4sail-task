<?php
namespace App\Interfaces ; 

interface CheckoutStrategy
{
    public function calculateTotal($amount);
    public function printInvoice($reservationNumber, $totalAmount);
}