<?php
/**
 * 
 * This file is part of the Aura Project for PHP.
 * 
 * @package Aura.Framework
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 */
namespace Aura\Framework\Web\Hello;

use Aura\Framework\Web\Controller\AbstractPage;

/**
 * 
 * A basic controller to show "Hello world" or an image asset.
 * 
 * @package Aura.Framework
 * 
 */
class Page extends AbstractPage
{
    /**
     * 
     * Sets the inner view to "world" and does nothing else.
     * 
     * @return void
     * 
     */
    public function actionWorld()
    {
        $this->view = 'world';
    }
    
    /**
     * 
     * Sets the inner view to "asset" and does nothing else.
     * 
     * @return void
     * 
     */
    public function actionAsset()
    {
        $this->view = 'asset';
    }
}
