<?php
/**
 * Example for the usage of ezcConsoleStatusbar class.
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

$out = new ezcConsoleOutput();

// Create status bar itself
$status = new ezcConsoleStatusbar( $out );

// Perform actions
$i = 0;
while( $i++ < 20 ) 
{
    // Do whatever you want to indicate progress for
    usleep( mt_rand( 20000, 2000000 ) );
    // Indicate success or failure
    $status->add( (bool)mt_rand( 0, 1 ) );
}

$out->outputLine();
// Print statistics
$out->outputLine( $status->getSuccessCount() . ' operations succeeded, ' . $status->getFailureCount() . ' failed.' );

/*
OUTPUT:

+-++++-++++-++-+--+-
13 operations succeeded, 7 failed.

*/
?>
