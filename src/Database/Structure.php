<?php
namespace czPechy\YetOrmAnnotation\Database;

use czPechy\YetOrmAnnotation\StructureException;
use Nette\Caching\Storages\DevNullStorage;
use Nette\Database\Connection;
use Nette\InvalidArgumentException;

class Structure
{

    /** @var \Nette\Database\Structure */
    protected static $databaseStructure;

    /**
     * @param $table
     * @param Connection $database
     * @return array
     * @throws StructureException
     */
    public static function get( $table, Connection $database) {
        $databaseStructure = self::getDatabaseStructure($database);
        try {
            return $databaseStructure->getColumns( $table );
        } catch (InvalidArgumentException $e) {
            throw new StructureException($e->getMessage());
        }
    }

    /**
     * @param Connection $database
     * @return \Nette\Database\Structure
     */
    protected static function getDatabaseStructure(Connection $database)
    {
        if(self::$databaseStructure === null) {
            self::$databaseStructure = new \Nette\Database\Structure($database, new DevNullStorage());
        }
        return self::$databaseStructure;
    }

}