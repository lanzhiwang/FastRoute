<?php

require './vendor/autoload.php';

$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
    //$r->addRoute($method, $routePattern, $handler);
    $r->addRoute('GET', '/users', 'get_all_users_handler');
    // {id} must be a number (\d+)
    $r->addRoute('GET', '/user/{id:\d+}', 'get_user_handler');
    // The /{title} suffix is optional
    $r->addRoute('GET', '/articles/{id:\d+}[/{title}]', 'get_article_handler');

//    $r->addGroup('/admin', function (RouteCollector $r) {
//        $r->addRoute('GET', '/do-something', 'handler');
//        $r->addRoute('GET', '/do-another-thing', 'handler');
//        $r->addRoute('GET', '/do-something-else', 'handler');
//    });

});

$dispatcher = FastRoute\cachedDispatcher(function(FastRoute\RouteCollector $r) {
    $r->addRoute('GET', '/user/{name}/{id:[0-9]+}', 'handler0');
    $r->addRoute('GET', '/user/{id:[0-9]+}', 'handler1');
    $r->addRoute('GET', '/user/{name}', 'handler2');
}, [
    'cacheFile' => __DIR__ . '/route.cache', /* required */
    'cacheDisabled' => IS_DEBUG_ENABLED,     /* optional, enabled by default */
]);






// Fetch method and URI from somewhere
//$httpMethod = $_SERVER['REQUEST_METHOD'];// 访问页面使用的请求方法；例如，“GET”, “HEAD”，“POST”，“PUT”。
//$uri = $_SERVER['REQUEST_URI'];// URI 用来指定要访问的页面。例如 “/index.html”。

$httpMethod = 'DELETE';// 访问页面使用的请求方法；例如，“GET”, “HEAD”，“POST”，“PUT”。
$uri = '/articles/15/t';// URI 用来指定要访问的页面。例如 “/index.html”。



// Strip query string (?foo=bar) and decode URI
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

/*
 * string(3) "GET"
 * string(8) "/user/12"
 *
 * string(3) "GET"
 * string(12) "/articles/15"
 *
 * string(3) "GET"
 * string(14) "/articles/15/t"
 *
 * string(6) "DELETE"
 * string(14) "/articles/15/t"
 *
 */
var_dump($httpMethod, $uri);
$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
print_r($routeInfo);
/*
 * [self::FOUND, $handler, []];
 *
 * Array
    (
        [0] => 0
    )
 *
 *
 */


switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        // ... 404 Not Found
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        // ... 405 Method Not Allowed
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];
        // ... call $handler with $vars
        break;
}