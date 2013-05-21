<?php
namespace Aura\Framework;

use Aura\Framework\Test\WiringAssertionsTrait;

class WiringTest extends \PHPUnit_Framework_TestCase
{
    use WiringAssertionsTrait;
    
    protected function setUp()
    {
        $this->loadDi();
    }
    
    public function testServices()
    {
        $this->assertGet('framework_inflect', 'Aura\Framework\Inflect');
        $this->assertGet('web_front', 'Aura\Framework\Web\Controller\Front');
        $this->assertGet('signal_manager', 'Aura\Framework\Signal\Manager');
    }
    
    public function testInstances()
    {
        $this->assertNewInstance('Aura\Framework\Bootstrap\Cli');
        $this->assertNewInstance('Aura\Framework\Bootstrap\Web');
        $this->assertNewInstance('Aura\Framework\Cli\AbstractCommand', 'Aura\Framework\Cli\MockCommand');
        $this->assertNewInstance('Aura\Framework\Cli\CacheClassmap\Command');
        $this->assertNewInstance('Aura\Framework\Cli\CacheConfig\Command');
        $this->assertNewInstance('Aura\Framework\Cli\Factory');
        $this->assertNewInstance('Aura\Framework\Cli\Server\Command');
        $this->assertNewInstance('Aura\Framework\View\Helper\AssetHref');
        $this->assertNewInstance('Aura\Framework\View\Helper\Route');
        $this->assertNewInstance('Aura\Framework\Web\Asset\Page');
        $this->assertNewInstance('Aura\Framework\Web\Controller\AbstractPage', 'Aura\Framework\Web\MockPage');
        $this->assertNewInstance('Aura\Framework\Web\Controller\Factory');
        $this->assertNewInstance('Aura\Framework\Web\Controller\Front');
        $this->assertNewInstance('Aura\Framework\Web\Renderer\AuraViewTwoStep');
        $this->assertNewInstance('Aura\Input\Form');
        $this->assertNewInstance('Aura\Intl\TranslatorLocator');
    }
    
    public function testViewHelpers()
    {
        $helper = $this->assertNewInstance('Aura\View\HelperLocator');
        $this->assertInstanceOf('Aura\Framework\View\Helper\AssetHref', $helper->get('assetHref'));
        $this->assertInstanceOf('Aura\Framework\View\Helper\Route', $helper->get('route'));
    }
    
    public function testAssetRoute()
    {
        $map = $this->di->get('router_map');
        $route = $map->match('/asset/Vendor.Package/foo/bar/baz.ext', []);
        $actual = $route->values;
        $expect = [
            'controller' => 'aura.framework.asset',
            'action' => 'index',
            'package' => 'Vendor.Package',
            'file' => 'foo/bar/baz',
            'format' => '.ext',
        ];
        $this->assertSame($expect, $actual);
    }
}
