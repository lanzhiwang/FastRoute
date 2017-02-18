<?php

namespace FastRoute\DataGenerator;

class GroupCountBased extends RegexBasedAbstract {
    protected function getApproxChunkSize() {
        return 10;
    }

    protected function processChunk($regexToRoutesMap) {
        //print_r($regexToRoutesMap);
        /*
         * Array
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
         *
         */

        $routeMap = [];
        $regexes = [];
        $numGroups = 0;
        foreach ($regexToRoutesMap as $regex => $route) {
            $numVariables = count($route->variables);
            $numGroups = max($numGroups, $numVariables);
            /*
             * int(1)
             * int(1)
             *
             * int(1)
             * int(2)
             *
             * int(2)
             * int(3)
             *
             */
            //var_dump($numVariables, $numGroups);

            $regexes[] = $regex . str_repeat('()', $numGroups - $numVariables);
            $routeMap[$numGroups + 1] = [$route->handler, $route->variables];

            /*
             * Array
                (
                    [0] => /user/(\d+)
                )
             *
             * Array
                (
                    [0] => /user/(\d+)
                    [1] => /articles/(\d+)()
                )
             *
             *
             * Array
                (
                    [0] => /user/(\d+)
                    [1] => /articles/(\d+)()
                    [2] => /articles/(\d+)/([^/]+)()
                )
             *
             */
            //print_r($regexes);

            /*
             * Array
                (
                    [2] => Array
                        (
                            [0] => get_user_handler
                            [1] => Array
                                (
                                    [id] => id
                                )

                        )

                )
             *
             *
             * Array
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

                )
             *
             *
             * Array
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
             *
             *
             *
             */
            //print_r($routeMap);

            ++$numGroups;
            /*
             * int(2)
             *
             * int(3)
             *
             * int(4)
             *
             */
            //var_dump($numGroups);
        }

        $regex = '~^(?|' . implode('|', $regexes) . ')$~';
        /*
         * string(63) "~^(?|/user/(\d+)|/articles/(\d+)()|/articles/(\d+)/([^/]+)())$~"
         */
        //var_dump($regex);
        return ['regex' => $regex, 'routeMap' => $routeMap];
    }
}

