<?php

namespace App\Data;

class person
{
    var string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }
}