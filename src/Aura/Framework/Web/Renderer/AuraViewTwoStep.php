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
     * Constructor.
     * 
     * @param TwoStep $twostep TwoStep View of Aura.View
     * 
     * @param Inflect $inflect Inflect class to file
     * 
     */
    public function __construct(
        TwoStep $twostep,
        Inflect $inflect
    ) {
        $this->twostep = $twostep;
        $this->inflect = $inflect;
    }

    /**
     * 
     * allows us to call, e.g., $renderer->addInnerPath() to override stuff
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
     * Prepares the renderer after setController().
     * 
     * @return void
     * 
     */
    protected function prep()
    {
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
        $this->twostep->setFormat($this->controller->getFormat());

        $response = $this->controller->getResponse();
        if (! $response->getContent()) {
            $this->twostep->setData((array) $this->controller->getData());
            $this->twostep->setAccept($this->controller->getContext()->getAccept());
            $this->twostep->setInnerView($this->controller->getView());
            $this->twostep->setOuterView($this->controller->getLayout());
            $response->setContent($this->twostep->render());
        }

        $response->setContentType($this->twostep->getContentType());
    }
}

