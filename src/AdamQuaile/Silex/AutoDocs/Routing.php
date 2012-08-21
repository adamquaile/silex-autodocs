<?php

namespace AdamQuaile\Silex\AutoDocs;

/**
 * Document all the defined routes in this application. Pulls them from the
 * Silex Application, and its routing provider.
 *
 * TODO: Generalise to also work with Symfony routes, others..
 */
class Routing
{

    /**
     * Register this module to silex, creating all routes, and requiring
     * as little config as possible in bootstrap file.
     *
     * @param \Silex\Application $staticApp
     */
    public static function register(\Silex\Application $staticApp)
    {

        /**
         * @var \Twig_Environment $twig
         */
        $twig = $staticApp['twig'];

        // Add the paths to our twig templates here
        $fsLoader = new \Twig_Loader_Filesystem(array(
            __DIR__.'/views/'
        ));

        $twig->setLoader(new \Twig_Loader_Chain(array($twig->getLoader(), $fsLoader)));

        self::registerRoutes($staticApp);

    }


    /**
     * Set up the routes for the auto-generation.
     *
     * @param \Silex\Application $app
     *
     * @static
     */
    public static function registerRoutes(\Silex\Application $app)
    {
        $app->get('/autodocs/routes', array(__CLASS__, 'routesAction'));
    }

    /**
     * Show page documenting all routes available in this app.
     *
     * @param \Silex\Application $app
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function routesAction(\Silex\Application $app)
    {
        /**
         * @var \Symfony\Component\Routing\RouteCollection $routes
         */
        $routes = $app['routes'];
        $routes = self::pullRoutesFromCollection($routes);

        $docs = array();
        foreach ($routes as $route) {

            /**
             * @var \Silex\Route $route
             */
            $controller = $route->getDefault('_controller');

            $docKey = $route->getPattern();

            if (is_array($controller)) {
                $method = new \ReflectionMethod($controller[0], $controller[1]);
            } elseif ($controller instanceof \Closure) {
                $method = new \ReflectionFunction($controller);
            } else {
                $docs[$docKey] = '';
                continue;
            }

            $docs[$docKey] = self::parseSummaryFromDocComment($method->getDocComment());

        }

        $bootstrap = $_SERVER['SCRIPT_FILENAME'];

        return $app['twig']->render('list-routes.html.twig', array(
            'routes'    => $routes,
            'docs'      =>$docs,
            'bootstrap' => $bootstrap
        ));
    }

    /**
     * Get an array of all routes (1 level deep) for all routes in the system.
     *
     * @param \Symfony\Component\Routing\RouteCollection $routes
     *
     * @return array
     */
    private static function pullRoutesFromCollection(\Symfony\Component\Routing\RouteCollection $routes)
    {
        $routesArray = array();
        foreach ($routes->getIterator() as $key => $route) {

            if ($route instanceof \Symfony\Component\Routing\RouteCollection) {
                $routesArray += self::pullRoutesFromCollection($route);
            } else {
                $routesArray[] = $route;
            }
        }

        return $routesArray;
    }

    private static function parseSummaryFromDocComment($commentString)
    {

        $commentString = preg_replace('#(/\*\*|\*)#', '', $commentString);
        $parts = preg_split('#(\.|@)#', $commentString);

        $commentString = count($parts) > 0 ? $parts[0] : $commentString;

        $commentString = trim($commentString);

        return $commentString;
    }

}