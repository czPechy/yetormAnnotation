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

    public function testUnsupportedNeon()
    {
        $configData = [];

        try {
            \czPechy\YetOrmAnnotation\Database\Config::connect($configData);
        } catch (\czPechy\YetOrmAnnotation\ConfigException $e) {
            $this->throwException($e);
        }
    }

    public function testGetDatabase()
    {
        $this->assertNull(\czPechy\YetOrmAnnotation\Database\Config::getDatabase());
    }

    public function testBadCredentials()
    {
        $configData = [
            'database' => [
                'default' => [
                    'dsn' => 'mysql:host=aaaaa;database=something',
                    'user' => null,
                    'password' => null
                ]
            ]
        ];

        try {
            \czPechy\YetOrmAnnotation\Database\Config::connect($configData);
        } catch (\czPechy\YetOrmAnnotation\ConfigException $e) {
            $this->throwException($e);
        }
    }

}
