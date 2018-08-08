<?php
require_once __DIR__ . '/../vendor/autoload.php';

class columnTest extends PHPUnit_Framework_TestCase
{

    public function testInteger()
    {
        $testData = [
            'nativetype' => 'INT',
            'nullable' => false,
            'autoincrement' => false,
            'name' => 'test'
        ];

        $this->assertSame(' * @property int $test', \czPechy\YetOrmAnnotation\Database\Column::generateAnnotation($testData));

        $testData['nativetype'] = 'BIGINT';
        $this->assertSame(' * @property int $test', \czPechy\YetOrmAnnotation\Database\Column::generateAnnotation($testData));

        $testData['nativetype'] = 'MEDIUMINT';
        $this->assertSame(' * @property int $test', \czPechy\YetOrmAnnotation\Database\Column::generateAnnotation($testData));

        $testData['nativetype'] = 'TINYINT';
        $this->assertSame(' * @property int $test', \czPechy\YetOrmAnnotation\Database\Column::generateAnnotation($testData));

        $testData['nativetype'] = 'SMALLINT';
        $this->assertSame(' * @property int $test', \czPechy\YetOrmAnnotation\Database\Column::generateAnnotation($testData));

        $testData['autoincrement'] = true;
        $this->assertSame(' * @property-read int $test', \czPechy\YetOrmAnnotation\Database\Column::generateAnnotation($testData));

        $testData['autoincrement'] = false;
        $testData['nullable'] = true;
        $this->assertSame(' * @property int|null $test', \czPechy\YetOrmAnnotation\Database\Column::generateAnnotation($testData));
    }

    public function testString()
    {
        $testData = [
            'nativetype' => 'VARCHAR',
            'nullable' => false,
            'autoincrement' => false,
            'name' => 'test'
        ];

        $this->assertSame(' * @property string $test', \czPechy\YetOrmAnnotation\Database\Column::generateAnnotation($testData));

        $testData['nativetype'] = 'TEXT';
        $this->assertSame(' * @property string $test', \czPechy\YetOrmAnnotation\Database\Column::generateAnnotation($testData));

        $testData['nativetype'] = 'LONGTEXT';
        $this->assertSame(' * @property string $test', \czPechy\YetOrmAnnotation\Database\Column::generateAnnotation($testData));

        $testData['nativetype'] = 'MEDIUMTEXT';
        $this->assertSame(' * @property string $test', \czPechy\YetOrmAnnotation\Database\Column::generateAnnotation($testData));

        $testData['nativetype'] = 'BLOB';
        $this->assertSame(' * @property string $test', \czPechy\YetOrmAnnotation\Database\Column::generateAnnotation($testData));

        $testData['nativetype'] = 'BINARY';
        $this->assertSame(' * @property string $test', \czPechy\YetOrmAnnotation\Database\Column::generateAnnotation($testData));
    }

    public function testDouble()
    {
        $testData = [
            'nativetype' => 'FLOAT',
            'nullable' => false,
            'autoincrement' => false,
            'name' => 'test'
        ];

        $this->assertSame(' * @property double $test', \czPechy\YetOrmAnnotation\Database\Column::generateAnnotation($testData));

        $testData['nativetype'] = 'DOUBLE';
        $this->assertSame(' * @property double $test', \czPechy\YetOrmAnnotation\Database\Column::generateAnnotation($testData));

        $testData['nativetype'] = 'DECIMAL';
        $this->assertSame(' * @property double $test', \czPechy\YetOrmAnnotation\Database\Column::generateAnnotation($testData));

        $testData['nativetype'] = 'DEC';
        $this->assertSame(' * @property double $test', \czPechy\YetOrmAnnotation\Database\Column::generateAnnotation($testData));
    }

    public function testBool()
    {
        $testData = [
            'nativetype' => 'BOOL',
            'nullable' => false,
            'autoincrement' => false,
            'name' => 'test'
        ];

        $this->assertSame(' * @property bool $test', \czPechy\YetOrmAnnotation\Database\Column::generateAnnotation($testData));
    }

    public function testDateTime()
    {
        $testData = [
            'nativetype' => 'DATETIME',
            'nullable' => true,
            'autoincrement' => false,
            'name' => 'test'
        ];

        $this->assertSame(' * @property \Nette\Utils\DateTime|null $test', \czPechy\YetOrmAnnotation\Database\Column::generateAnnotation($testData));

        $testData['nativetype'] = 'DATE';
        $this->assertSame(' * @property \Nette\Utils\DateTime|null $test', \czPechy\YetOrmAnnotation\Database\Column::generateAnnotation($testData));
    }

    public function testNotImplemented()
    {
        $testData = [
            'nativetype' => 'NOTIMPLEMENTED',
            'nullable' => true,
            'autoincrement' => false,
            'name' => 'test'
        ];

        try {
            \czPechy\YetOrmAnnotation\Database\Column::generateAnnotation($testData);
        } catch (\czPechy\YetOrmAnnotation\ColumnException $e) {
            $this->throwException($e);
        }
    }

}
