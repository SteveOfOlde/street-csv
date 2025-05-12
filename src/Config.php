<?php

namespace StreetCsv;

class Config
{
    /** @var string[] */
    public readonly array $titles;

    /** @var string[] */
    public readonly array $conjunctions;

    public function __construct()
    {
        $this->titles = ['Mr', 'Mrs', 'Mister', 'Ms', 'Mx', 'Dr', 'Prof'];
        $this->conjunctions = ['and', '&'];
    }

}