<?php
/**
 *
 * This file is part of the Aura project for PHP.
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 *
 */
namespace Aura\Framework\View\Helper;

use Aura\View\Helper\AbstractHelper;

/**
 *
 * Generates href values for assets.
 *
 * @package Aura.Framework
 *
 */
class AssetHref extends AbstractHelper
{
    /**
     *
     * The base (prefix) href for all asset hrefs.
     *
     * @var
     *
     */
    protected $base;

    /**
     *
     * Sets the base (prefix) href for all asset hrefs.
     *
     * @param string $base The base href.
     *
     */
    public function setBase($base)
    {
        $this->base = rtrim($base, '/');
    }

    /**
     *
     * Returns the href for an asset.
     *
     * @param string $href The asset href, prefixed with the base href.
     *
     */
    public function __invoke($href)
    {
        return $this->base . '/' . ltrim($href, '/');
    }
}
