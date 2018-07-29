<?php
require_once __DIR__ . '/../vendor/autoload.php';

class databaseTest extends PHPUnit_Framework_TestCase
{

    public function prepareDatabase() {
        return new \Nette\Database\Connection('sqlite::memory:');
    }

    public function testColumns()
    {
        $database = $this->prepareDatabase();
        $database->connect();

        $database->query('CREATE TABLE test (id INT(11) PRIMARY KEY)');

        $columns = \czPechy\YetOrmAnnotation\Database\Structure::get('test', $database); // NDB >2.3
        $this->assertCount(1, $columns);

        $columns = \czPechy\YetOrmAnnotation\Database\Structure::get('test', $database, true); // NDB 2.2
        $this->assertCount(1, $columns);

        $column = array_shift($columns);
        $annotation = \czPechy\YetOrmAnnotation\Database\Column::generateAnnotation($column);

        $this->assertSame($annotation, ' * @property int|null $id');

        $database->query('DROP TABLE test;');
    }

    public function testConnection() {
        $configData = [
            'database' => [
                'default' => [
                    'dsn' => 'sqlite::memory:',
                    'user' => null,
                    'password' => null
                ]
            ]
        ];
        $this->assertNull( \czPechy\YetOrmAnnotation\Database\Config::connect($configData) );
    }

}
