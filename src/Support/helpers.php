<?php

if (! function_exists("budpay"))
{
    function budpay() {

        return app()->make('laravel-budpay');
    }
}