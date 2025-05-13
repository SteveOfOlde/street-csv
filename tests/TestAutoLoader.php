<?php

use StreetCsv\Config;

class TestAutoLoader extends PHPUnit\Framework\TestCase
{
    public function testAutoLoadingConfig(): void
    {
        $config = new Config();
        $this->assertInstanceOf(Config::class, $config);
    }
}