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
namespace Aura\Framework\Web\NotFound;

use Aura\Framework\Web\Controller\AbstractPage;

/**
 * 
 * Show this when a page controller could not be found for the request.
 * 
 * @package Aura.Framework
 * 
 */
class Page extends AbstractPage
{
    /**
     * 
     * Force the action to "index".
     * 
     * @return void
     * 
     */
    public function preExec()
    {
        $this->action = 'index';
    }

    /**
     * 
     * Shows information about what happened.
     * 
     * @return void
     * 
     */
    public function actionIndex()
    {
        $request_uri = $this->context->getServer('REQUEST_URI', '/');

        $uri = htmlspecialchars(
            var_export($request_uri, true),
            ENT_QUOTES,
            'UTF-8'
        );

        $path = htmlspecialchars(
            var_export(parse_url($request_uri, PHP_URL_PATH), true),
            ENT_QUOTES,
            'UTF-8'
        );

        $html = <<<HTML
<html>
    <head>
        <title>Not Found</title>
    </head>
    <body>
        <h1>404 Not Found</h1>
        <p>No controller found for <code>$uri</code></p>
        <p>Please check that your config has:</p>
        <ol>
            <li>An <code>Aura\\Router\\Map</code> route for the path <code>$path</code></li>
            <li>A <code>['values']['controller']</code> value for the mapped route</li>
            <li>
                A <code>\$di->params['Aura\\Framework\\Web\\Controller\\Factory']['map']</code>
                entry for the controller value.
            </li>
        </ol>
    </body>
</html>
HTML;

        $this->response->setContent($html);
        $this->response->setStatusCode(404);
    }
}
