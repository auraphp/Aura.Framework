<?php
/**
 * 
 * This file is part of the Aura project for PHP.
 * 
 * @package Aura.Framework
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 */
namespace Aura\Framework\Intl;

use Aura\Intl\Translator as IntlTranslator;
use Aura\Cli\TranslatorInterface as CliTranslatorInterface;

/**
 * 
 * Implements separated interfaces for packages.
 * 
 * @package Aura.Framework
 * 
 */
class Translator extends IntlTranslator implements
    CliTranslatorInterface
{
    // do nothing, just extend and implement
}
