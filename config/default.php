<?php
/**
 * Package prefix for autoloader.
 */
$loader->add('Aura\Framework\\', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'src');

/**
 * Instance params and setter values.
 */

$di->setter['Aura\Framework\Cli\AbstractCommand']['setSignal'] = $di->lazyGet('signal_manager');

$di->setter['Aura\Framework\Cli\CacheClassmap\Command'] = [
    'setSystem'  => $di->lazyGet('framework_system'),
];

$di->setter['Aura\Framework\Cli\CacheConfig\Command'] = [
    'setSystem'  => $di->lazyGet('framework_system'),
];

$di->setter['Aura\Framework\Cli\Server\Command'] = [
    'setSystem'  => $di->lazyGet('framework_system'),
];

$di->params['Aura\Framework\Web\Controller\Factory'] = [
    'forge' => $di->getForge(),
    'not_found' => 'Aura\Framework\Web\NotFound\Page',
];

$di->params['Aura\Framework\Web\Controller\Front'] = [
    'signal'    => $di->lazyGet('signal_manager'),
    'context'   => $di->lazyGet('web_context'),
    'router'    => $di->lazyGet('router_map'),
    'factory'   => $di->lazyNew('Aura\Framework\Web\Controller\Factory'),
    'response'  => $di->lazyNew('Aura\Http\Message\Response'),
];

$di->setter['Aura\Framework\Web\Controller\AbstractPage'] = [
    'setRouter'  => $di->lazyGet('router_map'),
    'setSystem'  => $di->lazyGet('framework_system'),
];

$di->setter['Aura\Framework\View\Helper\AssetHref']['setBase'] = '/asset';

$di->params['Aura\View\HelperLocator']['registry']['assetHref'] = function() use ($di) {
    return $di->newInstance('Aura\Framework\View\Helper\AssetHref');
};

$di->params['Aura\Framework\View\Helper\Route'] = [
    'router' => $di->lazyGet('router_map'),
];

$di->params['Aura\View\HelperLocator']['registry']['route'] = function() use ($di) {
    return $di->newInstance('Aura\Framework\View\Helper\Route');
};

$di->params['Aura\Framework\Web\Renderer\AuraViewTwoStep'] = [
    'twostep' => $di->lazyNew('Aura\View\TwoStep'),
    'inflect' => $di->lazyGet('framework_inflect'),
    'accept'  => $di->lazyGet('web_accept'),
];

$di->params['Aura\Framework\Cli\Factory']['forge'] = $di->getForge();

$di->params['Aura\Framework\Bootstrap\Cli']['factory'] = $di->lazyNew('Aura\Framework\Cli\Factory');
$di->params['Aura\Framework\Bootstrap\Cli']['context'] = $di->lazyGet('cli_context');

$di->params['Aura\Framework\Bootstrap\Web']['front_controller'] = $di->lazyGet('web_front');
$di->params['Aura\Framework\Bootstrap\Web']['http_transport'] = $di->lazyGet('http_transport');

$di->params['Aura\Framework\Cli\Factory']['map']["$system/package/Aura.Framework/cli/cache-classmap"] = 'Aura\Framework\Cli\CacheClassmap\Command';
$di->params['Aura\Framework\Cli\Factory']['map']["$system/package/Aura.Framework/cli/cache-config"] = 'Aura\Framework\Cli\CacheConfig\Command';
$di->params['Aura\Framework\Cli\Factory']['map']["$system/package/Aura.Framework/cli/server"] = 'Aura\Framework\Cli\Server\Command';


// override the factory for translator locator
$di->params['Aura\Intl\TranslatorLocator']['factory'] = $di->lazyNew('Aura\Framework\Intl\TranslatorFactory');

$di->params['Aura\Framework\Web\Controller\Factory']['map']['aura.framework.asset'] = 'Aura\Framework\Web\Asset\Page';

$di->setter['Aura\Framework\Web\Asset\Page'] = [
    'setSystem'           => $di->lazyGet('framework_system'),
    'setWebCacheDir'      => 'cache/asset',
    'setCacheConfigModes' => ['prod', 'staging'],
];

$di->params['Aura\Router\Map']['attach']['/asset'] = [
    'routes' => [
        [
            'path' => '/{:package}/{:file:(.*?)}{:format:(\..+)?}',
            'values' => [
                'controller' => 'aura.framework.asset',
                'action' => 'index',
            ],
        ]
    ]
];


/**
 * Dependency services.
 */
$di->set('framework_inflect', $di->lazyNew('Aura\Framework\Inflect'));
$di->set('web_front', $di->lazyNew('Aura\Framework\Web\Controller\Front'));

// override the service for signal manager
$di->set('signal_manager', $di->lazyNew('Aura\Framework\Signal\Manager'));
