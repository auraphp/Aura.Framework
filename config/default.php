<?php
/**
 * Package prefix for autoloader.
 */

$loader->add('Aura\Framework\\', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'src');

/**
 * Instance params and setter values.
 */
 
$di->setter['Aura\Framework\Cli\Command']['setSignal'] = $di->lazyGet('signal_manager');

$di->setter['Aura\Framework\Cli\CacheClassmap\Command'] = [
    'setSystem'  => $di->lazyGet('framework_system'),
];

$di->setter['Aura\Framework\Cli\CacheConfig\Command'] = [
    'setSystem'  => $di->lazyGet('framework_system'),
];

$di->setter['Aura\Framework\Cli\MakeTest\Command'] = [
    'setInflect' => $di->lazyGet('framework_inflect'),
    'setSystem'  => $di->lazyGet('framework_system'),
];

$phpunit = 'php -d include_path=' . dirname(__DIR__) . '/pear/php '
         . dirname(__DIR__) . '/pear/bin/phpunit --verbose';

$di->params['Aura\Framework\Web\Factory'] = [
    'forge' => $di->getForge(),
];

$di->params['Aura\Framework\Web\Front'] = [
    'signal'    => $di->lazyGet('signal_manager'),
    'context'   => $di->lazyGet('web_context'),
    'router'    => $di->lazyGet('router_map'),
    'factory'   => $di->lazyNew('Aura\Framework\Web\Factory'),
    'response'  => $di->lazyNew('Aura\Http\Response'),
];

$di->setter['Aura\Framework\Web\AbstractPage'] = [
    'setInflect' => $di->lazyGet('framework_inflect'),
    'setRouter'  => $di->lazyGet('router_map'),
    'setSignal'  => $di->lazyGet('signal_manager'),
    'setSystem'  => $di->lazyGet('framework_system'),
    'setView'    => $di->lazyNew('Aura\View\TwoStep'),
];

$di->setter['Aura\Framework\Web\Asset\Page'] = [
    'setSystem'           => $di->lazyGet('framework_system'),
    'setWebCacheDir'      => 'cache/asset',
    'setCacheConfigModes' => ['prod', 'staging'],
];

$di->params['Aura\View\HelperLocator']['registry']['assetHref'] = function() use ($di) {
    return $di->newInstance('Aura\Framework\View\Helper\AssetHref');
};

$di->params['Aura\View\HelperLocator']['registry']['route'] = function() use ($di) {
    return $di->newInstance('Aura\Framework\View\Helper\Route');
};

/**
 * Dependency services.
 */
$di->set('framework_inflect', function() {
    return new Aura\Framework\Inflect;
});

$di->set('web_front', function() use ($di) {
    return $di->newInstance('Aura\Framework\Web\Front');
});
