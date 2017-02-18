<?php

namespace FastRoute\DataGenerator;

use FastRoute\DataGenerator;
use FastRoute\BadRouteException;
use FastRoute\Route;

abstract class RegexBasedAbstract implements DataGenerator {

    /*
     * $this->staticRoutes[$httpMethod][$routeStr] = $handler
     */
    protected $staticRoutes = [];

    /*
     * $this->methodToRegexToRoutesMap[$httpMethod][$regex] = new Route(
            $httpMethod, $handler, $regex, $variables
        );
     */
    protected $methodToRegexToRoutesMap = [];

    protected abstract function getApproxChunkSize();
    protected abstract function processChunk($regexToRoutesMap);

    /*
     *
     */
    //$this->dataGenerator->addRoute($method, $routeData, $handler)
    public function addRoute($httpMethod, $routeData, $handler) {
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
         * Array
            (
                [0] => /users
            )
         *
         * Array
            (
                [0] => /user/
                [1] => Array
                    (
                        [0] => id
                        [1] => \d+
                    )

            )
         *
         *
         * Array
            (
                [0] => /articles/
                [1] => Array
                    (
                        [0] => id
                        [1] => \d+
                    )

            )
         *
         *
         * Array
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
         *
         *
         */
        //print_r($routeData);

        /*
         * string(21) "get_all_users_handler"
         *
         * string(16) "get_user_handler"
         *
         */
        //var_dump($handler);

        /*
         * bool(true)
         *
         * bool(false)
         *
         * bool(false)
         *
         * bool(false)
         *
         */
        //var_dump($this->isStaticRoute($routeData));
        if ($this->isStaticRoute($routeData)) {
            $this->addStaticRoute($httpMethod, $routeData, $handler);
        } else {
            $this->addVariableRoute($httpMethod, $routeData, $handler);
        }
    }

    private function isStaticRoute($routeData) {
        return count($routeData) === 1 && is_string($routeData[0]);
    }

    private function addStaticRoute($httpMethod, $routeData, $handler) {
        //string(3) "GET"
        //var_dump($httpMethod);

        /*
         * Array
            (
                [0] => /users
            )
         *
         */
        //print_r($routeData);

        //string(21) "get_all_users_handler"
        //var_dump($handler);

        $routeStr = $routeData[0];
        /*
         * string(6) "/users"
         */
        //var_dump($routeStr);

        if (isset($this->staticRoutes[$httpMethod][$routeStr])) {
            throw new BadRouteException(sprintf(
                'Cannot register two routes matching "%s" for method "%s"',
                $routeStr, $httpMethod
            ));
        }

        if (isset($this->methodToRegexToRoutesMap[$httpMethod])) {
            foreach ($this->methodToRegexToRoutesMap[$httpMethod] as $route) {
                if ($route->matches($routeStr)) {
                    throw new BadRouteException(sprintf(
                        'Static route "%s" is shadowed by previously defined variable route "%s" for method "%s"',
                        $routeStr, $route->regex, $httpMethod
                    ));
                }
            }
        }

        $this->staticRoutes[$httpMethod][$routeStr] = $handler;
        //print_r($this->staticRoutes);
        /*
         * Array
            (
                [GET] => Array
                    (
                        [/users] => get_all_users_handler
                    )

            )
         *
         */


    }


    private function addVariableRoute($httpMethod, $routeData, $handler) {
        /*
         * Array
            (
                [0] => /user/
                [1] => Array
                    (
                        [0] => id
                        [1] => \d+
                    )

            )
         *
         *
         * Array
            (
                [0] => /articles/
                [1] => Array
                    (
                        [0] => id
                        [1] => \d+
                    )

            )
         *
         *
         * Array
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
         *
         *
         */
        //print_r($routeData);

        /*
         * Array
            (
                [0] => /user/(\d+)
                [1] => Array
                    (
                        [id] => id
                    )

            )
         *
         *
         * Array
            (
                [0] => /articles/(\d+)
                [1] => Array
                    (
                        [id] => id
                    )

            )
         *
         *
         * Array
            (
                [0] => /articles/(\d+)/([^/]+)
                [1] => Array
                    (
                        [id] => id
                        [title] => title
                    )

            )
         *
         *
         */

        list($regex, $variables) = $this->buildRegexForRoute($routeData);

        if (isset($this->methodToRegexToRoutesMap[$httpMethod][$regex])) {
            throw new BadRouteException(sprintf(
                'Cannot register two routes matching "%s" for method "%s"',
                $regex, $httpMethod
            ));
        }

        $this->methodToRegexToRoutesMap[$httpMethod][$regex] = new Route(
            $httpMethod, $handler, $regex, $variables
        );

        //print_r($this->methodToRegexToRoutesMap);
        /*
         * Array
            (
                [GET] => Array
                    (
                        [/user/(\d+)] => FastRoute\Route Object
                            (
                                [httpMethod] => GET
                                [regex] => /user/(\d+)
                                [variables] => Array
                                    (
                                        [id] => id
                                    )

                                [handler] => get_user_handler
                            )

                    )

            )
            Array
            (
                [GET] => Array
                    (
                        [/user/(\d+)] => FastRoute\Route Object
                            (
                                [httpMethod] => GET
                                [regex] => /user/(\d+)
                                [variables] => Array
                                    (
                                        [id] => id
                                    )

                                [handler] => get_user_handler
                            )

                        [/articles/(\d+)] => FastRoute\Route Object
                            (
                                [httpMethod] => GET
                                [regex] => /articles/(\d+)
                                [variables] => Array
                                    (
                                        [id] => id
                                    )

                                [handler] => get_article_handler
                            )

                    )

            )
            Array
            (
                [GET] => Array
                    (
                        [/user/(\d+)] => FastRoute\Route Object
                            (
                                [httpMethod] => GET
                                [regex] => /user/(\d+)
                                [variables] => Array
                                    (
                                        [id] => id
                                    )

                                [handler] => get_user_handler
                            )

                        [/articles/(\d+)] => FastRoute\Route Object
                            (
                                [httpMethod] => GET
                                [regex] => /articles/(\d+)
                                [variables] => Array
                                    (
                                        [id] => id
                                    )

                                [handler] => get_article_handler
                            )

                        [/articles/(\d+)/([^/]+)] => FastRoute\Route Object
                            (
                                [httpMethod] => GET
                                [regex] => /articles/(\d+)/([^/]+)
                                [variables] => Array
                                    (
                                        [id] => id
                                        [title] => title
                                    )

                                [handler] => get_article_handler
                            )

                    )

            )
         *
         */
    }

    private function buildRegexForRoute($routeData) {
        /*
         * Array
            (
                [0] => /user/
                [1] => Array
                    (
                        [0] => id
                        [1] => \d+
                    )

            )
         *
         *
         * Array
            (
                [0] => /articles/
                [1] => Array
                    (
                        [0] => id
                        [1] => \d+
                    )

            )
         *
         *
         * Array
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
         *
         *
         */
        //print_r($routeData);


        $regex = '';
        $variables = [];
        foreach ($routeData as $part) {
            if (is_string($part)) {
                /*
                 * preg_quote — 转义正则表达式字符
                 */
                $regex .= preg_quote($part, '~');
                /*
                 * string(6) "/user/"
                 *
                 * string(10) "/articles/"
                 *
                 * string(10) "/articles/"
                 *
                 * string(16) "/articles/(\d+)/"
                 *
                 *
                 */
                //var_dump($regex);
                continue;
            }

            list($varName, $regexPart) = $part;
            /*
             * string(2) "id"
             * string(3) "\d+"
             *
             * string(2) "id"
             * string(3) "\d+"
             *
             * string(2) "id"
             * string(3) "\d+"
             *
             * string(5) "title"
             * string(5) "[^/]+"
             *
             *
             *
             *
             *
             *
             */
            //var_dump($varName, $regexPart);


            if (isset($variables[$varName])) {
                throw new BadRouteException(sprintf(
                    'Cannot use the same placeholder "%s" twice', $varName
                ));
            }

            if ($this->regexHasCapturingGroups($regexPart)) {
                throw new BadRouteException(sprintf(
                    'Regex "%s" for parameter "%s" contains a capturing group',
                    $regexPart, $varName
                ));
            }

            $variables[$varName] = $varName;
            $regex .= '(' . $regexPart . ')';
            /*
             * Array
                (
                    [id] => id
                )
             *
             *
             * Array
                (
                    [id] => id
                )
             *
             *
             * Array
                (
                    [id] => id
                )
             *
             *
             * Array
                (
                    [id] => id
                    [title] => title
                )
             *
             */
            //print_r($variables);

            /*
             * string(11) "/user/(\d+)"
             *
             * string(15) "/articles/(\d+)"
             *
             * string(15) "/articles/(\d+)"
             *
             * string(23) "/articles/(\d+)/([^/]+)"
             *
             *
             *
             */
            //var_dump($regex);


        }

        $result = [$regex, $variables];

        /*
         * Array
            (
                [0] => /user/(\d+)
                [1] => Array
                    (
                        [id] => id
                    )

            )
         *
         *
         * Array
            (
                [0] => /articles/(\d+)
                [1] => Array
                    (
                        [id] => id
                    )

            )
         *
         *
         * Array
            (
                [0] => /articles/(\d+)/([^/]+)
                [1] => Array
                    (
                        [id] => id
                        [title] => title
                    )

            )
         *
         *
         */
        //print_r($result);

        return $result;
    }

    private function regexHasCapturingGroups($regex) {
        if (false === strpos($regex, '(')) {
            // Needs to have at least a ( to contain a capturing group
            return false;
        }

        // Semi-accurate detection for capturing groups
        return preg_match(
            '~
                (?:
                    \(\?\(
                  | \[ [^\]\\\\]* (?: \\\\ . [^\]\\\\]* )* \]
                  | \\\\ .
                ) (*SKIP)(*FAIL) |
                \(
                (?!
                    \? (?! <(?![!=]) | P< | \' )
                  | \*
                )
            ~x',
            $regex
        );
    }

    //$this->dataGenerator->getData()
    public function getData() {
        if (empty($this->methodToRegexToRoutesMap)) {
            return [$this->staticRoutes, []];
        }

        return [$this->staticRoutes, $this->generateVariableRouteData()];
    }

    private function generateVariableRouteData() {
        $data = [];

        /*
         *
            Array
            (
                [GET] => Array
                    (
                        [/user/(\d+)] => FastRoute\Route Object
                            (
                                [httpMethod] => GET
                                [regex] => /user/(\d+)
                                [variables] => Array
                                    (
                                        [id] => id
                                    )

                                [handler] => get_user_handler
                            )

                        [/articles/(\d+)] => FastRoute\Route Object
                            (
                                [httpMethod] => GET
                                [regex] => /articles/(\d+)
                                [variables] => Array
                                    (
                                        [id] => id
                                    )

                                [handler] => get_article_handler
                            )

                        [/articles/(\d+)/([^/]+)] => FastRoute\Route Object
                            (
                                [httpMethod] => GET
                                [regex] => /articles/(\d+)/([^/]+)
                                [variables] => Array
                                    (
                                        [id] => id
                                        [title] => title
                                    )

                                [handler] => get_article_handler
                            )

                    )

            )
         *
         */
        foreach ($this->methodToRegexToRoutesMap as $method => $regexToRoutesMap) {
            $chunkSize = $this->computeChunkSize(count($regexToRoutesMap));
            //var_dump($chunkSize);// double(3)

            /*
             * array_chunk — 将一个数组分割成多个
             * array array_chunk ( array $input , int $size [, bool $preserve_keys = false ] )
             * 将一个数组分割成多个数组，其中每个数组的单元数目由 size 决定。最后一个数组的单元数目可能会少于 size 个。
             *
             */
            $chunks = array_chunk($regexToRoutesMap, $chunkSize, true);
            //print_r($chunks);
            /*
             * Array
                (
                    [0] => Array
                        (
                            [/user/(\d+)] => FastRoute\Route Object
                                (
                                    [httpMethod] => GET
                                    [regex] => /user/(\d+)
                                    [variables] => Array
                                        (
                                            [id] => id
                                        )

                                    [handler] => get_user_handler
                                )

                            [/articles/(\d+)] => FastRoute\Route Object
                                (
                                    [httpMethod] => GET
                                    [regex] => /articles/(\d+)
                                    [variables] => Array
                                        (
                                            [id] => id
                                        )

                                    [handler] => get_article_handler
                                )

                            [/articles/(\d+)/([^/]+)] => FastRoute\Route Object
                                (
                                    [httpMethod] => GET
                                    [regex] => /articles/(\d+)/([^/]+)
                                    [variables] => Array
                                        (
                                            [id] => id
                                            [title] => title
                                        )

                                    [handler] => get_article_handler
                                )

                        )

                )
             *
             */

            /*
             * array_map — 为数组的每个元素应用回调函数
             * array array_map ( callable $callback , array $array1 [, array $... ] )
             * array_map()：返回数组，是为 array1 每个元素应用 callback函数之后的数组。
             * callback 函数形参的数量和传给 array_map() 数组数量，两者必须一样。
             *
             */
            $data[$method] =  array_map([$this, 'processChunk'], $chunks);
        }

        //print_r($data);
        /*
         * Array
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
         *
         *
         */
        return $data;
    }

    private function computeChunkSize($count) {
        $numParts = max(1, round($count / $this->getApproxChunkSize()));
        return ceil($count / $numParts);
    }

}
