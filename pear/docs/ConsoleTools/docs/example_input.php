<?php
/**
 * Example for the usage of ezcConsoleParameter class.
 *
 * @package ConsoleTools
 * @version 1.6.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

require_once 'Base/src/base.php';
/**
 * Autoload ezc classes 
 * 
 * @param string $className 
 */
function __autoload( $className )
{
    ezcBase::autoload( $className );
}

$optionHandler = new ezcConsoleInput();

// Register simple parameter -h/--help
$optionHandler->registerOption( new ezcConsoleOption( 'h', 'help' ) );

// Register complex parameter -f/--file
$file = new ezcConsoleOption(
 'f',
 'file',
 ezcConsoleInput::TYPE_STRING,
 null,
 false,
 'Process a file.',
 'Processes a single file.'
);
$optionHandler->registerOption( $file );

// Manipulate parameter -f/--file after registration
$file->multiple = true;

// Register another complex parameter that depends on -f and excludes -h
$dir = new ezcConsoleOption(
 'd',
 'dir',
 ezcConsoleInput::TYPE_STRING,
 null,
 true,
 'Process a directory.',
 'Processes a complete directory.',
 array( new ezcConsoleOptionRule( $optionHandler->getOption( 'f' ) ) ),
 array( new ezcConsoleOptionRule( $optionHandler->getOption( 'h' ) ) )
);
$optionHandler->registerOption( $dir );

// Register an alias for this parameter
$optionHandler->registerAlias( 'e', 'extended-dir', $dir );

// Process registered parameters and handle errors
try
{
     $optionHandler->process( array( 'example_input.php', '-h' ) );
}
catch ( ezcConsoleOptionException $e )
{
     echo $e->getMessage();
     exit( 1 );
}

// Process a single parameter
$file = $optionHandler->getOption( 'f' );
if ( $file->value === false )
{
     echo "Parameter -{$file->short}/--{$file->long} was not submitted.\n";
}
elseif ( $file->value === true )
{
     echo "Parameter -{$file->short}/--{$file->long} was submitted without value.\n";
}
else
{
     echo "Parameter -{$file->short}/--{$file->long} was submitted with value <".var_export($file->value, true).">.\n";
}

// Process all parameters at once:
foreach ( $optionHandler->getOptionValues() as $paramShort => $val )
{
     switch ( true )
     {
         case $val === false:
             echo "Parameter $paramShort was not submitted.\n";
             break;
         case $val === true:
             echo "Parameter $paramShort was submitted without a value.\n";
             break;
         case is_array( $val ):
             echo "Parameter $paramShort was submitted multiple times with value: <".implode(', ', $val).">.\n";
             break;
         default:
             echo "Parameter $paramShort was submitted with value: <$val>.\n";
             break;
     }
}
?>
