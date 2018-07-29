<?php
namespace czPechy\YetOrmAnnotation\Database;

use czPechy\YetOrmAnnotation\ColumnException;
use Nette\Utils\DateTime;

class Column
{

    /**
     * @param array $columnData
     * @return string
     * @throws ColumnException
     */
    public static function generateAnnotation( array $columnData) {
        $propertyType = ' * @property';
        if($columnData['autoincrement']) {
            $propertyType = ' * @property-read';
        }
        $nullable = $columnData['nullable'] ? '|null' : '';
        $type = self::getType($columnData['nativetype']);

        return $propertyType . ' ' . $type . $nullable . ' $' . $columnData['name'];
    }

    /**
     * @param $nativeType
     * @return string
     * @throws ColumnException
     */
    public static function getType( $nativeType) {
        if($nativeType === 'INT' || $nativeType === 'TINYINT' || $nativeType === 'BIGINT' || $nativeType === 'SMALLINT'
            || $nativeType === 'INTEGER' || $nativeType === 'MEDIUMINT' || $nativeType === 'TIMESTAMP') {
            return 'int';
        }
        if($nativeType === 'FLOAT' || $nativeType === 'DECIMAL' || $nativeType === 'DEC' || $nativeType === 'DOUBLE') {
            return 'double';
        }
        if($nativeType === 'VARCHAR' || $nativeType === 'TEXT' || $nativeType === 'LONGTEXT' || $nativeType === 'SHORTTEXT'
            || $nativeType === 'BLOB' || $nativeType === 'BINARY') {
            return 'string';
        }
        if($nativeType === 'BOOL' || $nativeType === 'BOOLEAN') {
            return 'bool';
        }
        if($nativeType === 'DATE' || $nativeType === 'DATETIME') {
            return DateTime::class;
        }
        throw new ColumnException('This type of column is not implemented yet, please create MR');
    }

}