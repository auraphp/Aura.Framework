<?php
namespace Aura\Framework\Web\Asset;
use Aura\Framework\Web\AbstractPageTest;
use Aura\Framework\System;

class PageTest extends AbstractPageTest
{
    protected $page_name = 'Asset';
    
    protected $system;
    
    protected $content = 'a plain text file';
    
    protected $asset_file;
    
    public function setUp()
    {
        parent::setUp();
        
        $system_dir = dirname(dirname(dirname(dirname(__DIR__)))) . DIRECTORY_SEPARATOR
                    . 'tmp' . DIRECTORY_SEPARATOR
                    . 'test' . DIRECTORY_SEPARATOR
                    . 'Aura.Framework.Web.Asset.PageTest' . DIRECTORY_SEPARATOR
                    . 'mock_system';
        $this->system = new System($system_dir);
        @mkdir($system_dir, 0777, true);
        
        $web_dir = $this->system->getWebPath();
        @mkdir($web_dir, 0777, true);
        
        $this->asset_file = $this->system->getPackagePath('Vendor.Package/web/file.txt');
        @mkdir(dirname($this->asset_file), 0777, true);
        file_put_contents($this->asset_file, $this->content);
    }
    
    public function tearDown()
    {
        // delete any cached files
        $dir = $this->system->getWebPath('cache/asset/Vendor.Package');
        $list = glob("$dir/*");
        foreach ($list as $file) {
            unlink($file);
        }
        
        @rmdir($dir); //cache/asset/Vendor.Package
        @rmdir(dirname($dir)); //cache/asset
        @rmdir(dirname(dirname($dir))); //cache
    }
    
    protected function newPage($params = [])
    {
        $page = parent::newPage($params);
        $page->setSystem($this->system);
        $page->setCacheConfigModes(['phpunit']); // ???
        $page->setWebCacheDir('cache/asset'); // ???
        return $page;
    }
    
    public function testActionIndex()
    {
        $_ENV['AURA_CONFIG_MODE'] = 'phpunit';
        
        $params = [
            'action' => 'index',
            'package' => 'Vendor.Package',
            'file' => 'file.txt',
        ];
        
        $page = $this->newPage($params);
        
        $xfer = $page->exec();
        
        // check that the content is a stream
        $fh = $xfer->getContent();
        $this->assertTrue(is_resource($fh));
        
        // read the stream to make sure it's the right thing
        $actual = fread($fh, 1000);
        $this->assertSame($actual, $this->content);
        
        // now check to see if it's been cached
        $file = $this->system->getWebPath('cache/asset/Vendor.Package/file.txt');
        $actual = file_get_contents($file);
        $this->assertSame($actual, $this->content);
    }
    
    public function testActionIndex_noSuchAsset()
    {
        $params = [
            'action' => 'index',
            'package' => 'Vendor.Package',
            'file' => 'no-such-file.txt',
        ];
        
        $page   = $this->newPage($params);
        $xfer   = $page->exec();
        $actual = $xfer->getContent();
        $expect = "Asset not found: "
                . $this->system->getPackagePath('Vendor.Package/web/no-such-file.txt');
        
        $this->assertSame($actual, $expect);
    }
    
    public function testActionIndex_doNotCacheInConfigMode()
    {
        $_ENV['AURA_CONFIG_MODE'] = 'foobar';
        
        $params = [
            'action' => 'index',
            'package' => 'Vendor.Package',
            'file' => 'file.txt',
        ];
        
        $page = $this->newPage($params);
        
        // unlink any previous cached asset file
        $file = $this->system->getWebPath('cache/asset/Vendor.Package/file.txt');
        @unlink($file);
        
        // now execute the page
        $xfer = $page->exec();
        
        // check that the content is a stream
        $fh = $xfer->getContent();
        $this->assertTrue(is_resource($fh));
        
        // read the stream to make sure it's the right thing
        $actual = fread($fh, 1000);
        $this->assertSame($actual, $this->content);
        
        // now check to see if it's been cached -- should not be there
        $this->assertFalse(file_exists($file));
    }
}
