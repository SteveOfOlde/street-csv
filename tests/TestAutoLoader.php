<?php

use StreetCsv\Config;

class TestAutoLoader extends PHPUnit\Framework\TestCase
{
    public function testAutoLoadingConfig()
    {
        $config = new Config();
        $this->assertInstanceOf(Config::class, $config);
    }
}