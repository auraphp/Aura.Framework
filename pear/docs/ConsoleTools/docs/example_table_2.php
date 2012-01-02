<?php
/**
 * Example for the usage of ezcConsoleTable class.
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

// Initialize the console output handler
$out = new ezcConsoleOutput();

// Define format schemes for even and odd rows
$out->formats->evenRow->color = 'red';
$out->formats->evenRow->style = array( 'bold' );

$out->formats->oddRow->color = 'blue';
$out->formats->oddRow->style = array( 'bold' );

// Define format schemes for even and odd cells
$out->formats->evenCell->color = 'red';
$out->formats->evenCell->style = array( 'negative' );

$out->formats->oddCell->color = 'blue';
$out->formats->oddCell->style = array( 'negative' );

// Create a new table with a width of 60 chars
$table = new ezcConsoleTable( $out, 60 );
    
// Set global cell content align
$table->options->defaultAlign = ezcConsoleTable::ALIGN_CENTER;

for ( $i = 0; $i < 5; $i ++ )
{
    for ( $j = 0; $j < 5; $j++ )
    {
        // Fill each table cell with ##
        $table[$i][$j]->content = '##';
        if ( $i === $j )
        {
            // On diagonal line set explicit cell format for even/odd
            $table[$i][$j]->format = $j % 2 == 0 ? 'evenCell' : 'oddCell';
        }
    }
    // Set global format for even/odd rows
    $table[$i]->format = $i % 2 == 0 ? 'evenRow' : 'oddRow';
    // Set border format for even/odd rows
    $table[$i]->borderFormat = $i % 2 == 0 ? 'evenRow' : 'oddRow';
}

$table->outputTable();
echo "\n";
?>
