<?php
namespace Aura\Framework\Intl;

use Aura\Intl\TranslatorFactory as IntlTranslatorFactory;

class TranslatorFactory extends IntlTranslatorFactory
{
    protected $class = 'Aura\Framework\Intl\Translator';
}
