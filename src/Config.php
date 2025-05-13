<?php

namespace StreetCsv;

readonly class Config
{
    /** @var string[] */
    public array $titles;

    /** @var string[] */
    public array $conjunctions;

    public function __construct()
    {
        $this->titles = ['Mr', 'Mister', 'Mrs', 'Ms', 'Mx', 'Dr', 'Prof'];
        $this->conjunctions = ['and', '&'];
    }

}