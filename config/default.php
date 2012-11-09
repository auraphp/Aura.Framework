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

$di->setter['Aura\Framework\Cli\MakeTest\Command'] = [
    'setPhpunit' => 'phpunit',
    'setBootstrap' => dirname(__DIR__) . '/tests/bootstrap.php',
    'setSystem'  => $di->lazyGet('framework_system'),
];

$di->params['Aura\Framework\Web\Controller\Factory'] = [
    'forge' => $di->getForge(),
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

$di->setter['Aura\Framework\Web\Asset\Page'] = [
    'setSystem'           => $di->lazyGet('framework_system'),
    'setWebCacheDir'      => 'cache/asset',
    'setCacheConfigModes' => ['prod', 'staging'],
];

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
];

/**
 * Dependency services.
 */
$di->set('framework_inflect', $di->lazyNew('Aura\Framework\Inflect'));
$di->set('web_front', $di->lazyNew('Aura\Framework\Web\Controller\Front'));
$di->set('signal_manager', $di->lazyNew('Aura\Framework\Signal\Manager'));

/**
 * Overrides from other packages
 */
$di->params['Aura\Intl\TranslatorLocator']['factory'] = $di->lazyNew('Aura\Framework\Intl\TranslatorFactory');
