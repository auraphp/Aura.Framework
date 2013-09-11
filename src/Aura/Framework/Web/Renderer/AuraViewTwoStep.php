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
namespace Aura\Framework\Web\Renderer;

use Aura\Framework\Inflect;
use Aura\View\TwoStep;
use Aura\Web\Renderer\AbstractRenderer;
use Aura\Web\Controller\ControllerInterface;
use Aura\Web\Accept;

/**
 * 
 * Incorporate the Aura.View two step rendering
 * 
 * @package Aura.Framework
 * 
 */
class AuraViewTwoStep extends AbstractRenderer
{
    /**
     * 
     * A TwoStep view object.
     * 
     * @var TwoStep
     * 
     */
    protected $twostep;

    /**
     * 
     * An inflection object.
     * 
     * @var Inflect
     * 
     */
    protected $inflect;

    /**
     * 
     * An accept object.
     * 
     * @var Accept
     * 
     */
    protected $accept;

    /**
     * 
     * Constructor.
     * 
     * @param TwoStep $twostep A two-step view object.
     * 
     * @param Inflect $inflect An inflection object.
     * 
     * @param Accept $accept An Accept object for content types.
     * 
     */
    public function __construct(
        TwoStep $twostep,
        Inflect $inflect,
        Accept  $accept
    ) {
        $this->twostep = $twostep;
        $this->inflect = $inflect;
        $this->accept  = $accept;
    }

    /**
     * 
     * Allows us to call, e.g., $renderer->addInnerPath() to override stuff
     * in a seemingly-direct manner.
     *
     * @param string $method Method to call.
     * 
     * @param array $params Params for the method.
     * 
     */
    public function __call($method, array $params)
    {
        return call_user_func_array([$this->twostep, $method], $params);
    }

    /**
     * 
     * Sets the controller object; prepares the renderer with view and layout
     * paths based on the controller path.
     * 
     * @param ControllerInterface $controller The controller object.
     * 
     * @return void
     * 
     */
    public function setController(ControllerInterface $controller)
    {
        // retain the controller
        $this->controller = $controller;

        // get all included files
        $includes = array_reverse(get_included_files());

        // get the controller class hierarchy stack
        $class = get_class($this->controller);
        $stack = class_parents($class);

        // remove from the stack these classes without template dirs:
        array_pop($stack); // Aura\Framework\Web\Controller\AbstractPage
        array_pop($stack); // Aura\Web\Controller\AbstractPage
        array_pop($stack); // Aura\Web\Controller\AbstractController

        // add the controller class itself
        array_unshift($stack, $class);

        // go through the hierarchy and look for each class file.
        // N.b.: this will not work if we concatenate all the classes into a
        // single file.
        foreach ($stack as $class) {
            $match = $this->inflect->classToFile($class);
            $len = strlen($match) * -1;
            foreach ($includes as $i => $include) {
                if (substr($include, $len) == $match) {
                    $dir = dirname($include);
                    $this->twostep->addInnerPath($dir . DIRECTORY_SEPARATOR . 'views');
                    $this->twostep->addOuterPath($dir . DIRECTORY_SEPARATOR . 'layouts');
                    unset($includes[$i]);
                    break;
                }
            }
        }
    }

    /**
     * 
     * Executes the renderer.
     * 
     * @return void
     * 
     */
    public function exec()
    {
        if (is_array($this->controller->getView()) && $this->controller->getFormat() == null) {
            $format = $this->twostep->getFormatTypes()
                    ->matchAcceptFormats(
                        array_keys($this->accept->getContentType()),
                        array_keys($this->controller->getView())
                    );
        } else {
            $format = $this->controller->getFormat();
        }
        $this->twostep->setFormat($format);
        $response = $this->controller->getResponse();
        if (! $response->getContent()) {
            $this->twostep->setData((array) $this->controller->getData());
            $this->twostep->setAccept($this->accept->getContentType());
            $this->twostep->setInnerView($this->controller->getView());
            $this->twostep->setOuterView($this->controller->getLayout());
            $response->setContent($this->twostep->render());
        }
        $response->setContentType($this->twostep->getContentType());
    }
}
