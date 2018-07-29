<?php
require_once __DIR__ . '/../vendor/autoload.php';

class configTest extends PHPUnit_Framework_TestCase
{

    public function testNeon()
    {
        $configData = [
            'database' => [
                'default' => [
                    'dsn' => 'sqlite::memory:',
                    'user' => null,
                    'password' => null
                ]
            ]
        ];

        $this->assertSame([
            'dsn' => 'sqlite::memory:',
            'user' => null,
            'password' => null
        ], \czPechy\YetOrmAnnotation\Database\Config::getDatabaseSettings($configData));
    }

    public function testNeon22()
    {
        $configData = [
            'nette' => [
                'database' => [
                    'default' => [
                        'dsn' => 'sqlite::memory:',
                        'user' => null,
                        'password' => null
                    ]
                ]
            ]
        ];

        $this->assertSame([
            'dsn' => 'sqlite::memory:',
            'user' => null,
            'password' => null
        ], \czPechy\YetOrmAnnotation\Database\Config::getDatabaseSettings($configData));
    }

}
