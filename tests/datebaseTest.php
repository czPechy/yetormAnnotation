<?php
require_once __DIR__ . '/../vendor/autoload.php';

class datebaseTest extends PHPUnit_Framework_TestCase
{

    public function prepareDatabase() {
        return new \Nette\Database\Connection('sqlite::memory:');
    }

    public function testColumns()
    {
        $database = $this->prepareDatabase();
        $database->connect();

        $this->assertSame([], \czPechy\YetOrmAnnotation\Database\Structure::get('test', $database));

        $database->query('CREATE TABLE test (id INT(11) PRIMARY KEY)');

        $columns = \czPechy\YetOrmAnnotation\Database\Structure::get('test', $database);
        $this->assertCount(1, $columns);

        $column = array_shift($columns);
        $annotation = \czPechy\YetOrmAnnotation\Database\Column::generateAnnotation($column);

        $this->assertSame($annotation, ' * @property int|null $id');

        $database->query('DROP TABLE test;');

        $this->assertSame([], \czPechy\YetOrmAnnotation\Database\Structure::get('test', $database));
    }

}
