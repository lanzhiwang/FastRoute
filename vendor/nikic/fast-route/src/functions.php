<?php

namespace FastRoute;

if (!function_exists('FastRoute\simpleDispatcher')) {
    /**
     * @param callable $routeDefinitionCallback
     * @param array $options
     *
     * @return Dispatcher
     */
    function simpleDispatcher(callable $routeDefinitionCallback, array $options = []) {
        $options += [
            'routeParser' => 'FastRoute\\RouteParser\\Std',
            'dataGenerator' => 'FastRoute\\DataGenerator\\GroupCountBased',
            'dispatcher' => 'FastRoute\\Dispatcher\\GroupCountBased',
            'routeCollector' => 'FastRoute\\RouteCollector',
        ];

        /*
         * Array
            (
                [routeParser] => FastRoute\RouteParser\Std
                [dataGenerator] => FastRoute\DataGenerator\GroupCountBased
                [dispatcher] => FastRoute\Dispatcher\GroupCountBased
                [routeCollector] => FastRoute\RouteCollector
            )
         *
         *
         */
        //print_r($options);

        $routeParser = new $options['routeParser'];
        /*
         * FastRoute\RouteParser\Std Object
            (
            )
         *
         */
        //print_r($routeParser);

        $dataGenerator = new $options['dataGenerator'];
        /*
         * FastRoute\DataGenerator\GroupCountBased Object
            (
                [staticRoutes:protected] => Array
                    (
                    )

                [methodToRegexToRoutesMap:protected] => Array
                    (
                    )

            )
         *
         */
        //print_r($dataGenerator);

        /** @var RouteCollector $routeCollector */
        $routeCollector = new $options['routeCollector']($routeParser, $dataGenerator);
        /*
         * FastRoute\RouteCollector Object
            (
                [routeParser:protected] => FastRoute\RouteParser\Std Object
                    (
                    )

                [dataGenerator:protected] => FastRoute\DataGenerator\GroupCountBased Object
                    (
                        [staticRoutes:protected] => Array
                            (
                            )

                        [methodToRegexToRoutesMap:protected] => Array
                            (
                            )

                    )

                [currentGroupPrefix:protected] =>
            )
         *
         *
         */
        //print_r($routeCollector);

//        $routeCollector->addGroup('qwe', function($routeCo) {
//
//        });

        $routeDefinitionCallback($routeCollector);

        return new $options['dispatcher']($routeCollector->getData());
    }

    /**
     * @param callable $routeDefinitionCallback
     * @param array $options
     *
     * @return Dispatcher
     */
    function cachedDispatcher(callable $routeDefinitionCallback, array $options = []) {
        $options += [
            'routeParser' => 'FastRoute\\RouteParser\\Std',
            'dataGenerator' => 'FastRoute\\DataGenerator\\GroupCountBased',
            'dispatcher' => 'FastRoute\\Dispatcher\\GroupCountBased',
            'routeCollector' => 'FastRoute\\RouteCollector',
            'cacheDisabled' => false,
        ];

        if (!isset($options['cacheFile'])) {
            throw new \LogicException('Must specify "cacheFile" option');
        }

        if (!$options['cacheDisabled'] && file_exists($options['cacheFile'])) {
            $dispatchData = require $options['cacheFile'];
            if (!is_array($dispatchData)) {
                throw new \RuntimeException('Invalid cache file "' . $options['cacheFile'] . '"');
            }
            return new $options['dispatcher']($dispatchData);
        }

        $routeCollector = new $options['routeCollector'](
            new $options['routeParser'], new $options['dataGenerator']
        );
        $routeDefinitionCallback($routeCollector);

        /** @var RouteCollector $routeCollector */
        $dispatchData = $routeCollector->getData();
        if (!$options['cacheDisabled']) {
            file_put_contents(
                $options['cacheFile'],
                '<?php return ' . var_export($dispatchData, true) . ';'
            );
        }

        return new $options['dispatcher']($dispatchData);
    }
}
