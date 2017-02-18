<?php

namespace FastRoute\RouteParser;

use FastRoute\BadRouteException;
use FastRoute\RouteParser;

/**
 * Parses route strings of the following form:
 *
 * "/user/{name}[/{id:[0-9]+}]"
 */
class Std implements RouteParser {
    const VARIABLE_REGEX = <<<'REGEX'
\{
    \s* ([a-zA-Z_][a-zA-Z0-9_-]*) \s*
    (?:
        : \s* ([^{}]*(?:\{(?-1)\}[^{}]*)*)
    )?
\}
REGEX;
    const DEFAULT_DISPATCH_REGEX = '[^/]+';

    /*
     * string(6) "/users"
     *
     * string(14) "/user/{id:\d+}"
     *
     * string(28) "/articles/{id:\d+}[/{title}]"
     *
     */
    //$this->routeParser->parse($route)
    public function parse($route) {
        /*
         * rtrim — 删除字符串末端的空白字符（或者其他字符）
         */
        $routeWithoutClosingOptionals = rtrim($route, ']');
        /*
         * string(6) "/users"
         *
         * string(14) "/user/{id:\d+}"
         *
         * string(27) "/articles/{id:\d+}[/{title}"
         *
         *
         */
        //var_dump($routeWithoutClosingOptionals);

        $numOptionals = strlen($route) - strlen($routeWithoutClosingOptionals);
        /*
         * int(0)
         * int(0)
         * int(1)
         *
         */
        //var_dump($numOptionals);

        /*
         * string(125) "~\{
                \s* ([a-zA-Z_][a-zA-Z0-9_-]*) \s*
                (?:
                    : \s* ([^{}]*(?:\{(?-1)\}[^{}]*)*)
                )?
            \}(*SKIP)(*F) | \[~x"
         *
         *
         */
        //var_dump('~' . self::VARIABLE_REGEX . '(*SKIP)(*F) | \[~x');

        /*
         * preg_split — 通过一个正则表达式分隔字符串
         * array preg_split ( string $pattern , string $subject [, int $limit = -1 [, int $flags = 0 ]] )
         * 返回一个使用 pattern 边界分隔 subject 后得到 的子串组成的数组
         *
         */
        // Split on [ while skipping placeholders
        $segments = preg_split(
            '~' . self::VARIABLE_REGEX . '(*SKIP)(*F) | \[~x',
            $routeWithoutClosingOptionals
        );
        /*
         * string(6) "/users"
         * Array
            (
                [0] => /users
            )
         *
         * string(14) "/user/{id:\d+}"
         * Array
            (
                [0] => /user/{id:\d+}
            )
         *
         * string(27) "/articles/{id:\d+}[/{title}"
         * Array
            (
                [0] => /articles/{id:\d+}
                [1] => /{title}
            )
         *
         *
         *
         */
        //print_r($segments);

        if ($numOptionals !== count($segments) - 1) {
            // If there are any ] in the middle of the route, throw a more specific error message
            if (preg_match('~' . self::VARIABLE_REGEX . '(*SKIP)(*F) | \]~x', $routeWithoutClosingOptionals)) {
                throw new BadRouteException("Optional segments can only occur at the end of a route");
            }
            throw new BadRouteException("Number of opening '[' and closing ']' does not match");
        }

        $currentRoute = '';
        $routeDatas = [];
        foreach ($segments as $n => $segment) {
            if ($segment === '' && $n !== 0) {
                throw new BadRouteException("Empty optional part");
            }

            $currentRoute .= $segment;
            /*
             * string(6) "/users"
             *
             * string(14) "/user/{id:\d+}"
             *
             * string(18) "/articles/{id:\d+}"
             * string(26) "/articles/{id:\d+}/{title}"
             *
             *
             */
            //var_dump($currentRoute);
            $routeDatas[] = $this->parsePlaceholders($currentRoute);

            /*
             * Array
                (
                    [0] => Array
                        (
                            [0] => /users
                        )

                )
             *
             *
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

                )
             *
             *
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
             *
             *
             *
             */
            //print_r($routeDatas);
        }
        return $routeDatas;
    }

    /**
     * Parses a route string that does not contain optional segments.
     */
    private function parsePlaceholders($route) {
        /*
         * string(6) "/users"
         *
         * string(14) "/user/{id:\d+}"
         *
         * string(18) "/articles/{id:\d+}"
         *
         * string(26) "/articles/{id:\d+}/{title}"
         *
         */
        //var_dump($route);

        /*
         * preg_match_all — 执行一个全局正则表达式匹配
         * 返回完整匹配次数（可能是0），或者如果发生错误返回FALSE。
         *
         */

        /*
         * int(0)
         * int(1)
         * int(1)
         * int(2)
         *
         */
//        var_dump(preg_match_all(
//            '~' . self::VARIABLE_REGEX . '~x', $route, $matches,
//            PREG_OFFSET_CAPTURE | PREG_SET_ORDER
//        ));

        if (!preg_match_all(
            '~' . self::VARIABLE_REGEX . '~x', $route, $matches,
            PREG_OFFSET_CAPTURE | PREG_SET_ORDER
        )) {
            return [$route];
        }

        /*
         * string(14) "/user/{id:\d+}"
         * Array
            (
                [0] => Array
                    (
                        [0] => Array
                            (
                                [0] => {id:\d+}
                                [1] => 6
                            )

                        [1] => Array
                            (
                                [0] => id
                                [1] => 7
                            )

                        [2] => Array
                            (
                                [0] => \d+
                                [1] => 10
                            )

                    )

            )
         *
         *
         * string(18) "/articles/{id:\d+}"
         * Array
            (
                [0] => Array
                    (
                        [0] => Array
                            (
                                [0] => {id:\d+}
                                [1] => 10
                            )

                        [1] => Array
                            (
                                [0] => id
                                [1] => 11
                            )

                        [2] => Array
                            (
                                [0] => \d+
                                [1] => 14
                            )

                    )

            )
         *
         *
         * string(26) "/articles/{id:\d+}/{title}"
         * Array
            (
                [0] => Array
                    (
                        [0] => Array
                            (
                                [0] => {id:\d+}
                                [1] => 10
                            )

                        [1] => Array
                            (
                                [0] => id
                                [1] => 11
                            )

                        [2] => Array
                            (
                                [0] => \d+
                                [1] => 14
                            )

                    )

                [1] => Array
                    (
                        [0] => Array
                            (
                                [0] => {title}
                                [1] => 19
                            )

                        [1] => Array
                            (
                                [0] => title
                                [1] => 20
                            )

                    )

            )
         *
         *
         *
         */
        //print_r($matches);

        $offset = 0;
        $routeData = [];
        foreach ($matches as $set) {
            if ($set[0][1] > $offset) {
                $routeData[] = substr($route, $offset, $set[0][1] - $offset);
            }
            $routeData[] = [
                $set[1][0],
                isset($set[2]) ? trim($set[2][0]) : self::DEFAULT_DISPATCH_REGEX
            ];
            $offset = $set[0][1] + strlen($set[0][0]);
        }

        if ($offset != strlen($route)) {
            $routeData[] = substr($route, $offset);
        }

        return $routeData;
    }
}
