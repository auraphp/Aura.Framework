<?php
/**
 *
 * This file is part of the Aura Project for PHP.
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 *
 */
namespace Aura\Framework\Signal;

use Aura\Signal\Manager as SignalManager;
use Aura\Cli\SignalInterface as CliSignalInterface;
use Aura\Web\SignalInterface as WebSignalInterface;

/**
 *
 * Nothing to do but implement the separated interface.
 *
 * @package Aura.Framework
 *
 */
class Manager extends SignalManager implements
    CliSignalInterface,
    WebSignalInterface
{
    // nothing to do but implement the separated interface
}
