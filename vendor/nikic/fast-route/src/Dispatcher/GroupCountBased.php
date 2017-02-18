<?php

namespace FastRoute\Dispatcher;


class GroupCountBased extends RegexBasedAbstract {

    //new $options['dispatcher']($routeCollector->getData())
    public function __construct($data) {
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

        list($this->staticRouteMap, $this->variableRouteData) = $data;

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
        //print_r($this->staticRouteMap);

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
        //print_r($this->variableRouteData);


    }

    protected function dispatchVariableRoute($routeData, $uri) {

        /*
         *
                    Array
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
         *
         *
         *
         */
        //print_r($routeData);

        foreach ($routeData as $data) {
            /*
             * preg_match — 执行一个正则表达式匹配
             * int preg_match ( string $pattern , string $subject [, array &$matches [, int $flags = 0 [, int $offset = 0 ]]] )
             * preg_match()返回 pattern 的匹配次数。它的值将是0次（不匹配）或1次，因为preg_match()在第一次匹配后 将会停止搜索。
             * preg_match_all()不同于此，它会一直搜索subject 直到到达结尾。 如果发生错误preg_match()返回 FALSE。
             */
            if (!preg_match($data['regex'], $uri, $matches)) {
                continue;
            }

            /*
             * string(3) "GET"
             * string(8) "/user/12"
             * Array
                (
                    [0] => /user/12
                    [1] => 12
                )
             *
             * string(3) "GET"
             * string(12) "/articles/15"
             * Array
                (
                    [0] => /articles/15
                    [1] => 15
                    [2] =>
                )
             *
             * string(3) "GET"
             * string(14) "/articles/15/t"
             * Array
                (
                    [0] => /articles/15/t
                    [1] => 15
                    [2] => t
                    [3] =>
                )
             *
             *
             * string(6) "DELETE"
             * string(14) "/articles/15/t"
             * Array
                (
                    [0] => /articles/15/t
                    [1] => 15
                    [2] => t
                    [3] =>
                )
             *
             *
             */
            print_r($matches);

            list($handler, $varNames) = $data['routeMap'][count($matches)];
            /*
             * string(16) "get_user_handler"
             *
             * string(19) "get_article_handler"
             *
             * string(19) "get_article_handler"
             *
             * string(19) "get_article_handler"
             *
             *
             */
            var_dump($handler);

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
                    [title] => title
                )
             *
             * Array
                (
                    [id] => id
                    [title] => title
                )
             *
             *
             */
            print_r($varNames);

            $vars = [];
            $i = 0;
            foreach ($varNames as $varName) {
                $vars[$varName] = $matches[++$i];
            }
            /*
             * Array
                (
                    [id] => 12
                )
             *
             *
             * Array
                (
                    [id] => 15
                )
             *
             *
             * Array
                (
                    [id] => 15
                    [title] => t
                )
             *
             *
             * Array
                (
                    [id] => 15
                    [title] => t
                )
             *
             */
            print_r($vars);
            return [self::FOUND, $handler, $vars];
        }

        return [self::NOT_FOUND];
    }
}
