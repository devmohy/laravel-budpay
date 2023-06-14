<?php
namespace Devmohy\Budpay\Facades;
use Illuminate\Support\Facades\Facade;

class Budpay extends Facade
{
    /**
     * Get the registered name of the component
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'laravel-budpay';
    }
}