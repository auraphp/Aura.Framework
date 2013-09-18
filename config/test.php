<?php
/**
 * Loader
 */
$loader->add('Aura\Framework\\', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'tests');

// PHPUnit adds its own autoloader, so don't throw exceptions
$loader->setMode($loader::MODE_SILENT);
