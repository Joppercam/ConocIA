<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class Vimeo extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'vimeo';
    }
}