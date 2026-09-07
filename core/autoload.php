<?php

/**
 * Legacy EpubClass bootstrap and router.
 */

spl_autoload_register(function ($className) {
    $files = [];

    if (strpos($className, 'Controller') === 0) {
        $controllerName = substr($className, strlen('Controller'));
        $files[] = _CONTROLLER_DIR . $controllerName . '.php';
    }

    $files[] = _PATH_ . 'class/' . $className . '.php';
    $files[] = _PATH_ . 'librerie/' . $className . '.php';

    if (defined('_MODEL_DIR')) {
        $files[] = _MODEL_DIR . $className . '.php';
    }

    foreach ($files as $file) {
        if (is_file($file)) {
            require_once $file;
            return;
        }
    }
});

$requestPath = '/';

if (isset($_SERVER['PATH_INFO']) && $_SERVER['PATH_INFO'] !== '') {
    $requestPath = $_SERVER['PATH_INFO'];
} elseif (isset($_SERVER['REQUEST_URI'])) {
    $uriPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $indexPosition = strpos($uriPath, '/index.php');

    if ($indexPosition !== false) {
        $requestPath = substr(
            $uriPath,
            $indexPosition + strlen('/index.php')
        );
    }
}

$requestPath = trim($requestPath, '/');
$segments = $requestPath === '' ? [] : explode('/', $requestPath);

$controllerSegment = $segments[0] ?? 'index';
$actionSegment = $segments[1] ?? 'init';

if (!preg_match('/^[a-zA-Z0-9_-]+$/', $controllerSegment)) {
    http_response_code(400);
    exit('Invalid controller.');
}

if (!preg_match('/^[a-zA-Z0-9_-]+$/', $actionSegment)) {
    http_response_code(400);
    exit('Invalid action.');
}

$controllerName = 'Controller' . ucfirst(strtolower($controllerSegment));
$controllerFile = _CONTROLLER_DIR . ucfirst(strtolower($controllerSegment)) . '.php';

if (!is_file($controllerFile)) {
    http_response_code(404);
    exit('Controller not found: ' . htmlspecialchars($controllerSegment));
}

require_once $controllerFile;

if (!class_exists($controllerName)) {
    http_response_code(500);
    exit('Controller class not found: ' . htmlspecialchars($controllerName));
}

$controller = new $controllerName();

if (
    strpos($actionSegment, '_') === 0 ||
    !is_callable([$controller, $actionSegment])
) {
    http_response_code(404);
    exit('Action not found: ' . htmlspecialchars($actionSegment));
}

$controller->{$actionSegment}();
