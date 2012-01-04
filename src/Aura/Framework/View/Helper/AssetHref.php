<?php
/**
 * 
 * This file is part of the Aura project for PHP.
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 * @package Aura.Framework
 * 
 */
namespace Aura\Framework\View\Helper;
use Aura\View\Helper\AbstractHelper;

/**
 * 
 * Generates href values for assets.
 * 
 */
class AssetHref extends AbstractHelper
{
    public function setBase($base)
    {
        $this->base = rtrim($base, '/');
    }
    
    public function __invoke($href)
    {
        return $this->base . '/' . ltrim($href, '/');
    }
}
