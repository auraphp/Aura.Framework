<?php
namespace Aura\Framework\Web\Renderer;

use Aura\Framework\Inflect;
use Aura\Framework\Mock\Page;
use Aura\Framework\Signal\Manager as SignalManager;
use Aura\Signal\HandlerFactory;
use Aura\Signal\ResultFactory;
use Aura\Signal\ResultCollection;
use Aura\View\EscaperFactory;
use Aura\View\FormatTypes;
use Aura\View\HelperLocator;
use Aura\View\Template;
use Aura\View\TemplateFinder;
use Aura\View\TwoStep;
use Aura\Web\Accept;
use Aura\Web\Context;
use Aura\Web\Response;

class AuraViewTwoStepTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AuraViewTwoStep
     */
    protected $renderer;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        // two step view
        $escaper_factory    = new EscaperFactory;
        $template_finder    = new TemplateFinder;
        $helper_locator     = new HelperLocator;
        $template           = new Template($escaper_factory, $template_finder, $helper_locator);
        $format_types       = new FormatTypes;
        $twostep            = new TwoStep($template, $format_types);
        
        // renderer
        $this->renderer     = new AuraViewTwoStep($twostep, new Inflect, new Accept($_SERVER));
        
        // page
        $this->page = new Page(
            new Context($GLOBALS),
            new Accept($GLOBALS['_SERVER']),
            new Response,
            new SignalManager(
                new HandlerFactory,
                new ResultFactory,
                new ResultCollection
            ),
            $this->renderer
        );
    }
    
    public function test__call()
    {
        $this->renderer->addInnerPath('/foo/bar');
        $dir = dirname(dirname(__DIR__));
        $expect = [
            $dir . '/Mock/views',
            '/foo/bar'
        ];
        $actual = $this->renderer->getInnerPaths();
        $this->assertSame($expect, $actual);
    }

    public function testExec()
    {
        $this->page->setView('index');
        $this->renderer->exec();
        $response = $this->page->getResponse();
        $content = $response->getContent();
        $this->assertSame('mock view', $content);
    }
}
