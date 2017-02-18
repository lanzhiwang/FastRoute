<?php

namespace FastRoute;

class RouteCollector {
    protected $routeParser;
    protected $dataGenerator;
    protected $currentGroupPrefix;

    /**
     * Constructs a route collector.
     *
     * @param RouteParser   $routeParser
     * @param DataGenerator $dataGenerator
     */
    //'routeParser' => 'FastRoute\\RouteParser\\Std',
    //'dataGenerator' => 'FastRoute\\DataGenerator\\GroupCountBased',
    //new $options['routeCollector'](new $options['routeParser'], new $options['dataGenerator'])
    public function __construct(RouteParser $routeParser, DataGenerator $dataGenerator) {
        $this->routeParser = $routeParser;
        $this->dataGenerator = $dataGenerator;
        $this->currentGroupPrefix = '';
    }

    /**
     * Adds a route to the collection.
     *
     * The syntax used in the $route string depends on the used route parser.
     *
     * @param string|string[] $httpMethod
     * @param string $route
     * @param mixed  $handler
     */
    //$r->addRoute('GET', '/users', 'get_all_users_handler');
    //$r->addRoute('GET', '/user/{id:\d+}', 'get_user_handler');
    //$r->addRoute('GET', '/articles/{id:\d+}[/{title}]', 'get_article_handler');
    public function addRoute($httpMethod, $route, $handler) {
        /*
         * string(3) "GET"
         *
         * string(3) "GET"
         *
         * string(3) "GET"
         *
         */
        //var_dump($httpMethod);

        /*
         * string(6) "/users"
         *
         * string(14) "/user/{id:\d+}"
         *
         * string(28) "/articles/{id:\d+}[/{title}]"
         *
         */
        //var_dump($route);

        /*
         * string(21) "get_all_users_handler"
         *
         * string(16) "get_user_handler"
         *
         * string(19) "get_article_handler"
         *
         */
        //var_dump($handler);

        $route = $this->currentGroupPrefix . $route;
        /*
         * string(6) "/users"
         *
         * string(14) "/user/{id:\d+}"
         *
         * string(28) "/articles/{id:\d+}[/{title}]"
         *
         */
        var_dump($route);

        $routeDatas = $this->routeParser->parse($route);
        /*
         * string(6) "/users"
         * Array
            (
                [0] => Array
                    (
                        [0] => /users
                    )

            )
         *
         *
         * string(14) "/user/{id:\d+}"
         * Array
            (
                [0] => Array
                    (
                        [0] => /user/
                        [1] => Array
                            (
                                [0] => id
                                [1] => \d+
                            )

                    )

            )
         *
         *
         * string(28) "/articles/{id:\d+}[/{title}]"
         * Array
            (
                [0] => Array
                    (
                        [0] => /articles/
                        [1] => Array
                            (
                                [0] => id
                                [1] => \d+
                            )

                    )

                [1] => Array
                    (
                        [0] => /articles/
                        [1] => Array
                            (
                                [0] => id
                                [1] => \d+
                            )

                        [2] => /
                        [3] => Array
                            (
                                [0] => title
                                [1] => [^/]+
                            )

                    )

            )
         *
         */
        //print_r($routeDatas);

        /*
         * Array
            (
                [0] => GET
            )
         *
         * Array
            (
                [0] => GET
            )
         *
         *
         * Array
            (
                [0] => GET
            )
         *
         */
        //print_r((array) $httpMethod);
        foreach ((array) $httpMethod as $method) {
            foreach ($routeDatas as $routeData) {
                $this->dataGenerator->addRoute($method, $routeData, $handler);
            }
        }
    }

    /**
     * Create a route group with a common prefix.
     *
     * All routes created in the passed callback will have the given group prefix prepended.
     *
     * @param string $prefix
     * @param callable $callback
     */
    public function addGroup($prefix, callable $callback) {
        $previousGroupPrefix = $this->currentGroupPrefix;
        /*
         * string(0) ""
         *
         */
        var_dump($previousGroupPrefix);
        $this->currentGroupPrefix = $previousGroupPrefix . $prefix;
        /*
         * string(6) "/admin"
         *
         */
        var_dump($this->currentGroupPrefix);
        $callback($this);
        $this->currentGroupPrefix = $previousGroupPrefix;
    }
    
    /**
     * Adds a GET route to the collection
     * 
     * This is simply an alias of $this->addRoute('GET', $route, $handler)
     *
     * @param string $route
     * @param mixed  $handler
     */
    public function get($route, $handler) {
        $this->addRoute('GET', $route, $handler);
    }
    
    /**
     * Adds a POST route to the collection
     * 
     * This is simply an alias of $this->addRoute('POST', $route, $handler)
     *
     * @param string $route
     * @param mixed  $handler
     */
    public function post($route, $handler) {
        $this->addRoute('POST', $route, $handler);
    }
    
    /**
     * Adds a PUT route to the collection
     * 
     * This is simply an alias of $this->addRoute('PUT', $route, $handler)
     *
     * @param string $route
     * @param mixed  $handler
     */
    public function put($route, $handler) {
        $this->addRoute('PUT', $route, $handler);
    }
    
    /**
     * Adds a DELETE route to the collection
     * 
     * This is simply an alias of $this->addRoute('DELETE', $route, $handler)
     *
     * @param string $route
     * @param mixed  $handler
     */
    public function delete($route, $handler) {
        $this->addRoute('DELETE', $route, $handler);
    }
    
    /**
     * Adds a PATCH route to the collection
     * 
     * This is simply an alias of $this->addRoute('PATCH', $route, $handler)
     *
     * @param string $route
     * @param mixed  $handler
     */
    public function patch($route, $handler) {
        $this->addRoute('PATCH', $route, $handler);
    }

    /**
     * Adds a HEAD route to the collection
     *
     * This is simply an alias of $this->addRoute('HEAD', $route, $handler)
     *
     * @param string $route
     * @param mixed  $handler
     */
    public function head($route, $handler) {
        $this->addRoute('HEAD', $route, $handler);
    }

    /**
     * Returns the collected route data, as provided by the data generator.
     *
     * @return array
     */
    //$routeCollector->getData()
    public function getData() {
        $data = $this->dataGenerator->getData();
        /*
         * Array
            (
                [0] => Array
                    (
                        [GET] => Array
                            (
                                [/users] => get_all_users_handler
                            )

                    )

                [1] => Array
                    (
                        [GET] => Array
                            (
                                [0] => Array
                                    (
                                        [regex] => ~^(?|/user/(\d+)|/articles/(\d+)()|/articles/(\d+)/([^/]+)())$~
                                        [routeMap] => Array
                                            (
                                                [2] => Array
                                                    (
                                                        [0] => get_user_handler
                                                        [1] => Array
                                                            (
                                                                [id] => id
                                                            )

                                                    )

                                                [3] => Array
                                                    (
                                                        [0] => get_article_handler
                                                        [1] => Array
                                                            (
                                                                [id] => id
                                                            )

                                                    )

                                                [4] => Array
                                                    (
                                                        [0] => get_article_handler
                                                        [1] => Array
                                                            (
                                                                [id] => id
                                                                [title] => title
                                                            )

                                                    )

                                            )

                                    )

                            )

                    )

            )
         *
         *
         */
        //print_r($data);
        return $data;
    }
}
