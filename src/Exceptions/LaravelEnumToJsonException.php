<?php

namespace KovacsZoltan65\LaravelEnumsToJson\Exceptions;

class LaravelEnumToJsonException extends \Exception
{
    public static function nameCollision($name) {
        return new self("There was a collision of names: {$name}");
    }
}
