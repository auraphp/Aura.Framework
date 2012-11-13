<?php
$di->params['Aura\Framework\Web\Controller\Factory']['map']['aura.framework.asset'] = 'Aura\Framework\Web\Asset\Page';

$di->setter['Aura\Framework\Web\Asset\Page'] = [
    'setSystem'           => $di->lazyGet('framework_system'),
    'setWebCacheDir'      => 'cache/asset',
    'setCacheConfigModes' => ['prod', 'staging'],
];

$di->get('router_map')->add(null, '/asset/{:package}/{:file:(.*?)}{:format:(\..+)?}', [
    'values' => [
        'controller' => 'aura.framework.asset',
        'action' => 'index',
    ],
]);

