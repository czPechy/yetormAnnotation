<?php
namespace czPechy\YetOrmAnnotation;

class GeneratorException extends \Exception {}

class RuntimeException extends GeneratorException {}

class InvalidArgumentException extends GeneratorException {}

class ControlException extends GeneratorException {}

class ConfigException extends GeneratorException {}

class StructureException extends GeneratorException {}

class ColumnException extends GeneratorException {}
