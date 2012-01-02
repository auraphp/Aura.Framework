<?php
namespace Aura\Framework\Web\Hello;
use Aura\Framework\Web\AbstractPageTest;
class PageTest extends AbstractPageTest
{
    protected $page_name = 'Hello';
    
    public function testActionWorld()
    {
        $page = $this->newPage([
            'action' => 'world',
        ]);
        $xfer = $page->exec();
        
        $this->assertInstanceOf('Aura\Web\Response', $xfer);
        $this->assertSame(200, $xfer->getStatusCode());
        $this->assertSame($xfer->getContent(), 'Hello World!');
    }
    
    public function testActionAsset()
    {
        $page = $this->newPage([
            'action' => 'asset',
        ]);
        $xfer = $page->exec();
        
        $this->assertInstanceOf('Aura\Web\Response', $xfer);
        $this->assertSame(200, $xfer->getStatusCode());
    }
}
