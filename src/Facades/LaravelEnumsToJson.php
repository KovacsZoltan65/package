<?php

namespace KovacsZoltan65\LaravelEnumsToJson\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \KovacsZoltan65\LaravelEnumsToJson\LaravelEnumsToJson
 */
class LaravelEnumsToJson extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \KovacsZoltan65\LaravelEnumsToJson\LaravelEnumsToJson::class;
    }
}
