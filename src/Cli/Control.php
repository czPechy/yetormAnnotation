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
        $configDir = $this->readLine('Please enter your config directory: ');
        while(!is_dir($configDir)) {
            $configDir = $this->readLine('Directory \'' . $configDir . '\' not exist, please specify existing dir: ');
        }

        $configDir = rtrim($configDir, '/');
        if(!empty($configDir)) {
            $configDir .=  $this->dirSeparator;
        }

        $originalDirectory = array_diff( scandir( $configDir, SCANDIR_SORT_NONE ), [ '..', '.' ] );
        $originalDirectory = array_filter($originalDirectory, function($file) use ($configDir) {
            if(is_file($configDir . $file)) {
                return $file;
            }
            return null;
        });
        $itemsLimit = 4;
        $itemsCnt = 0;
        $directory = array_filter($originalDirectory, function($file) use (&$itemsCnt, $itemsLimit) {
            if($itemsCnt <= $itemsLimit) {
                $itemsCnt++;
                return $file;
            }
            return null;
        });
        if($itemsCnt > $itemsLimit) {
            $directory[] = '...';
        }
        $this->writeLine('In directory found ' . \count($originalDirectory) . ' files. (' . implode(', ', $directory) . ')' );

        $existingConfigDefault = file_exists($configDir . 'config.neon');
        $existingConfigLocal = file_exists($configDir . 'config.local.neon');

        $callbackOtherNeon = function() use ($configDir) {
            $read = $this->readLine('Please specify your neon cofig in dir \'' . $configDir . '\': ');
            while (!file_exists($configDir . $read)) {
                $read = $this->readLine('Config not found ('. $configDir . $read .'), please specify existing config in \'' . $configDir . '\': ');
            }
            $this->processConfig($configDir . $read);
        };
        $callbackDefaultNeon = function() use ($callbackOtherNeon, $configDir) {
            $this->boolQuestion('Do you want use default config.neon?', function() use ($configDir){
                $this->processConfig($configDir . 'config.neon');
            }, $callbackOtherNeon);
        };

        if($existingConfigDefault && $existingConfigLocal) {
            $this->boolQuestion('Do you want use default config.local.neon and config.neon?', function() use ($configDir) {
                $this->processConfig([$configDir . 'config.local.neon', $configDir . 'config.neon']);
            }, $callbackDefaultNeon);
        } else if($existingConfigDefault) {
            Callback::invokeSafe($callbackDefaultNeon, [], function(){
                $this->writeLine('Error when invoking $callbackDefaultNeon');
            });
        } else {
            Callback::invokeSafe($callbackOtherNeon, [], function(){
                $this->writeLine('Error when invoking $callbackOtherNeon');
            });
        }

        $this->newLine();
        $this->writeLine('Thanks for using!');
        $this->writeLine('Now donate, or i eat your dog! ;-)');
    }

    protected function processConfig($path) {
        $neonContent = FileSystem::read($path);
        $neon = Neon::decode($neonContent);
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
            if(Callback::check($callbackTrue)) {
                Callback::invokeSafe($callbackTrue, [], function(){
                    $this->writeLine('Error when invoking $callbackTrue');
                });
            }
        } else if(Callback::check($callbackFalse)) {
            Callback::invokeSafe($callbackFalse, [], function(){
                $this->writeLine('Error when invoking $callbackFalse');
            });
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