<?php
namespace czPechy\YetOrmAnnotation\Cli;

use czPechy\YetOrmAnnotation\ColumnException;
use czPechy\YetOrmAnnotation\ConfigException;
use czPechy\YetOrmAnnotation\Database\Column;
use czPechy\YetOrmAnnotation\Database\Config;
use czPechy\YetOrmAnnotation\Database\Structure;
use czPechy\YetOrmAnnotation\StructureException;
use Nette\Neon\Neon;
use Nette\Utils\Callback;
use Nette\Utils\FileSystem;
use Nette\Utils\Strings;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;

class Control
{

    public $dirSeparator = '/';

//    public function __construct()
//    {
//        if (PHP_OS === 'WINNT') {
//            $this->dirSeparator = '\\';
//        }
//    }

    public function run()
    {
        $this->writeLine('Welcome in YetORM Annotation generator!');

		$type = $this->readLine('Use nette configs or manual connection (n/m): ');
		while(!in_array($type, ['m', 'n'], true)) {
			$type = $this->readLine('Use nette configs or manual connection (n/m): ');
		}

        if($type === 'n') {
			$configDir = NULL;
			if (is_dir('app/config/')) {
				$this->boolQuestion('Default config dir found, did you want use \'app/config\'?', function () use (&$configDir) {
					$configDir = 'app/config/';
				}, function () {
				});
			}
			if ($configDir === NULL) {
				$configDir = $this->readLine('Please enter your config directory: ');
				while (!is_dir($configDir)) {
					$configDir = $this->readLine('Directory \'' . $configDir . '\' not exist, please specify existing dir: ');
				}
			}

			$configDir = rtrim($configDir, '/');
			if (!empty($configDir)) {
				$configDir .= $this->dirSeparator;
			}

			$originalDirectory = array_diff(scandir($configDir, SCANDIR_SORT_NONE), ['..', '.']);
			$originalDirectory = array_filter($originalDirectory, function ($file) use ($configDir) {
				if (is_file($configDir . $file)) {
					return $file;
				}
				return NULL;
			});
			$itemsLimit = 4;
			$itemsCnt = 0;
			$directory = array_filter($originalDirectory, function ($file) use (&$itemsCnt, $itemsLimit) {
				if ($itemsCnt <= $itemsLimit) {
					$itemsCnt++;
					return $file;
				}
				return NULL;
			});
			if ($itemsCnt > $itemsLimit) {
				$directory[] = '...';
			}
			$this->writeLine('In directory found ' . \count($originalDirectory) . ' files. (' . implode(', ', $directory) . ')');

			$existingConfigDefault = file_exists($configDir . 'config.neon');
			$existingConfigLocal = file_exists($configDir . 'config.local.neon');

			$callbackOtherNeon = function () use ($configDir) {
				$read = $this->readLine('Please specify your neon cofig in dir \'' . $configDir . '\': ');
				while (!file_exists($configDir . $read)) {
					$read = $this->readLine('Config not found (' . $configDir . $read . '), please specify existing config in \'' . $configDir . '\': ');
				}
				$this->processConfig($configDir . $read);
			};
			$callbackDefaultNeon = function () use ($callbackOtherNeon, $configDir) {
				$this->boolQuestion('Do you want use default config.neon?', function () use ($configDir) {
					$this->processConfig($configDir . 'config.neon');
				}, $callbackOtherNeon);
			};

			if ($existingConfigDefault && $existingConfigLocal) {
				$this->boolQuestion('Do you want use default config.local.neon and config.neon?', function () use ($configDir) {
					$this->processConfig([$configDir . 'config.local.neon', $configDir . 'config.neon']);
				}, $callbackDefaultNeon);
			} else if ($existingConfigDefault) {
				$callbackDefaultNeon();
			} else {
				$callbackOtherNeon();
			}
		} else {

        	$setingsCallback = function(){
				$driver = $this->readLine('Use MySQL or PostgreSQL (m/p): ');
				while(!in_array($driver, ['m', 'p'], true)) {
					$driver = $this->readLine('Use MySQL or PostgreSQL (m/p): ');
				}
				$driver = $driver === 'm' ? 'mysql' : 'pgsql';

				$server = $this->readLine('HOST: ');
				$user = $this->readLine('USER: ');
				$password = $this->readLine('PASS: ');
				$db = $this->readLine('DB: ');

				return [
					'database' => [
						'default' => [
							'dsn' => $driver . ':host=' . $server . ';dbname=' . $db,
							'user' => $user,
							'password' => $password
						]
					]
				];
			};

        	$connected = false;
        	while(!$connected) {
				try {
					$config = $setingsCallback();
					Config::connect($config);
					$connected = true;
					$this->processTable();
				} catch (ConfigException $e) {
					$this->writeLine($e->getMessage());
				}
			}
		}

        $this->newLine();
        $this->writeLine('Thanks for using!');
        $this->writeLine('Now donate, or i eat your dog! ;-)');
    }

    protected function processConfig($path) {
        if(method_exists('Nette\Utils\FileSystem', 'read')) {
            $neonContent = FileSystem::read($path);
        } else {
            $neonContent = (string) file_get_contents($path);
        }
        try {
            $neon = Neon::decode($neonContent);
        } catch (\Exception $e) {
            $this->writeLine($e->getMessage());
            exit(-1);
        }
        try {
            Config::connect($neon);
        } catch (ConfigException $e) {
            $this->writeLine($e->getMessage());
            exit(-1);
        }
        $this->processTable();
    }

    protected function processTable() {
        $exist = false;
        while($exist === false) {
            $table = $this->readLine('What table do you want generate? ');
            try {
                $structure = Structure::get( $table, Config::getDatabase() );
                $exist = true;
            } catch (StructureException $e) {
                $this->writeLine($e->getMessage());
            }
        }

        $annotations = [];
        $errors = [];
        foreach ( $structure as $column ) {
            try {
                $annotations[] = Column::generateAnnotation( $column );
            } catch (ColumnException $e) {
                $errors[] = 'MISSING ANNOTATION for ' . $column['name'] . ': ' . $e->getMessage();
            }
        }

        $this->newLine();
        $this->writeLine('---------------------------------------------');
        $this->newLine();
        foreach($annotations as $annotation) {
            $this->writeLine($annotation);
        }
        $this->newLine();
        $this->writeLine('---------------------------------------------');
        $this->newLine();
        foreach($errors as $error) {
            $this->writeLine($error);
        }
        $this->newLine();

        $this->boolQuestion('Do you want another table?', function(){
            $this->processTable();
        }, function(){

        });
    }

    protected function boolQuestion($question, $callbackTrue, $callbackFalse) {
        $question .= ' [y/n]: ';

        do {
            $read = $this->readLine($question);
        } while(!in_array(Strings::lower($read), ['y', 'n'], true));

        $this->newLine();
        if(Strings::lower($read) === 'y') {
            $callbackTrue();
        } else {
            $callbackFalse();
        }
    }

    protected function readLine($question) {
        if (PHP_OS === 'WINNT') {
            $this->write($question);
            $read = stream_get_line(STDIN, 1024, PHP_EOL);
        } else {
            $read = readline($question);
        }
        return $read;
    }

    protected function writeLine($text) {
        $this->write($text);
        $this->newLine();
    }

    protected function newLine() {
        $this->write(PHP_EOL);
    }

    protected function write($text) {
        echo $text;
    }

}