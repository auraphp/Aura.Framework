<?php
/**
 * Example for the usage of ezcConsoleProgressMonitor class.
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

// Create a progress monitor
$status = new ezcConsoleProgressMonitor( $out, 7 );

// Perform actions
$i = 0;
while( $i++ < 7 ) 
{
    // Do whatever you want to indicate progress for
    usleep( mt_rand( 20000, 2000000 ) );
    // Advance the statusbar by one step
    $status->addEntry( 'ACTION', "Performed action #{$i}." );
}

$out->outputLine();

/*
OUTPUT:

    14.3% ACTION Performed action #1.
    28.6% ACTION Performed action #2.
    42.9% ACTION Performed action #3.
    57.1% ACTION Performed action #4.
    71.4% ACTION Performed action #5.
    85.7% ACTION Performed action #6.
   100.0% ACTION Performed action #7.

*/
?>
