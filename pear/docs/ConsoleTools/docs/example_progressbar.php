<?php
/**
 * Example for the usage of ezcConsoleProgressbar class.
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

$out->formats->red->color = "red";

// Create progress bar itself
$progress = new ezcConsoleProgressbar( $out, 100, array( 'step' => 5 ) );

$progress->options->emptyChar = '-';
$progress->options->progressChar = $out->formatText('>', "red");
$progress->options->formatString = "Uploading file </tmp/foobar.tar.bz2>: %act%/%max% kb [%bar%]";

// Perform actions
$i = 0;
while( $i++ < 20 ) 
{
    // Do whatever you want to indicate progress for
    usleep( mt_rand( 20000, 2000000 ) );
    // Advance the progressbar by one step ( uploading 5k per run )
    $progress->advance();
}

// Finish progress bar and jump to next line.
$progress->finish();

$out->outputText( "Successfully uploaded </tmp/foobar.tar.bz2>.\n", 'success' );

/*
OUTPUT: (sequential, will be printed into 1 line and updated in reallife)

Uploading file </tmp/foobar.tar.bz2>:   5/100 kb [++#----------------------------------------------]
Uploading file </tmp/foobar.tar.bz2>:  10/100 kb [+++++#-------------------------------------------]
Uploading file </tmp/foobar.tar.bz2>:  15/100 kb [++++++++#----------------------------------------]
Uploading file </tmp/foobar.tar.bz2>:  20/100 kb [+++++++++++#-------------------------------------]
Uploading file </tmp/foobar.tar.bz2>:  25/100 kb [++++++++++++++#----------------------------------]
Uploading file </tmp/foobar.tar.bz2>:  30/100 kb [+++++++++++++++++#-------------------------------]
Uploading file </tmp/foobar.tar.bz2>:  35/100 kb [++++++++++++++++++++#----------------------------]
Uploading file </tmp/foobar.tar.bz2>:  40/100 kb [+++++++++++++++++++++++#-------------------------]
Uploading file </tmp/foobar.tar.bz2>:  45/100 kb [++++++++++++++++++++++++++#----------------------]
Uploading file </tmp/foobar.tar.bz2>:  50/100 kb [+++++++++++++++++++++++++++++#-------------------]
Uploading file </tmp/foobar.tar.bz2>:  55/100 kb [++++++++++++++++++++++++++++++++#----------------]
Uploading file </tmp/foobar.tar.bz2>:  60/100 kb [+++++++++++++++++++++++++++++++++++#-------------]
Uploading file </tmp/foobar.tar.bz2>:  65/100 kb [++++++++++++++++++++++++++++++++++++++#----------]
Uploading file </tmp/foobar.tar.bz2>:  70/100 kb [+++++++++++++++++++++++++++++++++++++++++#-------]
Uploading file </tmp/foobar.tar.bz2>:  75/100 kb [++++++++++++++++++++++++++++++++++++++++++++#----]
Uploading file </tmp/foobar.tar.bz2>:  80/100 kb [+++++++++++++++++++++++++++++++++++++++++++++++#-]
Uploading file </tmp/foobar.tar.bz2>:  85/100 kb [++++++++++++++++++++++++++++++++++++++++++++++++#]
Uploading file </tmp/foobar.tar.bz2>:  90/100 kb [++++++++++++++++++++++++++++++++++++++++++++++++#]
Uploading file </tmp/foobar.tar.bz2>:  95/100 kb [++++++++++++++++++++++++++++++++++++++++++++++++#]
Uploading file </tmp/foobar.tar.bz2>: 100/100 kb [++++++++++++++++++++++++++++++++++++++++++++++++#]
Uploading file </tmp/foobar.tar.bz2>: 100/100 kb [++++++++++++++++++++++++++++++++++++++++++++++++#]Successfully uploaded </tmp/foobar.tar.bz2>.

*/
?>
