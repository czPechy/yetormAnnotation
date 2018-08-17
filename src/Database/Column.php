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
            || $nativeType === 'INTEGER' || $nativeType === 'MEDIUMINT'	|| $nativeType === 'INT4' || $nativeType === 'INT8'
			|| $nativeType === 'INT2' || ($nativeType === 'TIMESTAMP' && Config::isMySQL())) {
            return 'int';
        }
        if($nativeType === 'FLOAT' || $nativeType === 'DECIMAL' || $nativeType === 'DEC' || $nativeType === 'DOUBLE'
			|| $nativeType === 'NUMERIC' || $nativeType === 'FLOAT4' || $nativeType === 'FLOAT8') {
            return 'double';
        }
        if($nativeType === 'VARCHAR' || $nativeType === 'TEXT' || $nativeType === 'LONGTEXT' || $nativeType === 'SHORTTEXT'
            || $nativeType === 'MEDIUMTEXT' || $nativeType === 'BLOB' || $nativeType === 'BINARY' || $nativeType === 'CHAR') {
            return 'string';
        }
        if($nativeType === 'BOOL' || $nativeType === 'BOOLEAN') {
            return 'bool';
        }
        if($nativeType === 'DATE' || $nativeType === 'DATETIME' || ($nativeType === 'TIMESTAMP' && Config::isPostgreSQL())) {
            return '\\' . DateTime::class;
        }
        throw new ColumnException('This type of column (' . $nativeType . ') is not implemented yet, please create MR');
    }

}