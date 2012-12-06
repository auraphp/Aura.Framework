<?php
namespace Aura\Framework\Intl;

use Aura\Intl\Translator as IntlTranslator;
use Aura\Cli\TranslatorInterface as CliTranslatorInterface;

class Translator extends IntlTranslator implements
    CliTranslatorInterface
{
    // do nothing, just extend and implement
}
