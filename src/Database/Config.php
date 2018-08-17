<?php
namespace czPechy\YetOrmAnnotation\Database;

use czPechy\YetOrmAnnotation\ConfigException;
use Nette\Database\Connection;
use Nette\Database\ConnectionException;

class Config
{

	const PGSQL = 'pgsql';
	const MYSQL = 'mysql';

	protected static $type;
    protected static $connection;

    /**
     * @param $neonConfig
     * @throws ConfigException
     */
    public static function connect( $neonConfig) {
        $settings = self::getDatabaseSettings($neonConfig);
        if($settings === null) {
            throw new ConfigException('Database settings not found in neon');
        }
        try {
            self::$connection = new Connection($settings['dsn'], $settings['user'], $settings['password'], (isset($settings['options']) ? $settings['options'] : []));
            if(!self::$connection->query('SELECT 1')) {
                throw new ConfigException('Cannot connect to database');
            }
            self::$type = \strpos($settings['dsn'], self::PGSQL) === 0 ? self::PGSQL : self::MYSQL;
        } catch (\Exception $e) {
            throw new ConfigException($e->getMessage());
        }
    }

    public static function getDatabase() {
        return self::$connection;
    }

    public static function getDatabaseSettings($neonConfig) {
        $databaseConfig = null;
        if(isset($neonConfig['database'])) {
            $databaseConfig = $neonConfig['database']['default'];
        } else if(isset($neonConfig['nette']['database'])) {
            $databaseConfig = $neonConfig['nette']['database']['default'];
        }
        return $databaseConfig;
    }

    public static function getDbType() {
    	return self::$type;
	}

	public static function isPostgreSQL() {
    	return self::getDbType() === self::PGSQL;
	}

	public static function isMySQL() {
    	return self::getDbType() === self::MYSQL;
	}

}