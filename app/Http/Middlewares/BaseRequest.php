<?php

namespace App\Http\Middlewares;

class BaseRequest
{
    /**
     * get route details
     * @param Request $request
     * @return array
     */
    public function getRouteDetails(
        $request
    ): ?array {
        $method = $request->method();
        $path = $this->fixPath($request->getPathInfo());
        $routes = $this->getRoutes();
        return $routes[$method . $path]['action'] ?? null;
    }

    /**
     * get route from lumen
     * @return array
     */
    public function getRoutes(): array
    {

        $app = $this->newApp();
        $routes = $app->router->getRoutes();
        $newRoutes = [];
        foreach ($routes as $key => $value) {
            $correctKey = $this->fixKey($key);
            $newRoutes[$correctKey] = $value;
        }
        return $newRoutes;
    }

    /**
     * fix key routes with parameters
     * @param string $key
     * @return string
     */
    public function fixKey(
        string $key
    ): string {
        $position = strpos($key, '{');
        if ($position === false) {
            return $key;
        }
        return substr($key, 0, $position - 1);
    }

    /**
     * fix routes paths with parameters
     * @param string $key
     * @return string
     */
    public function fixPath(
        string $path
    ): string {
        if ($path == '/') {
            return $path;
        }
        $arrayPath = explode('/', $path);
        return '/' . $arrayPath[1] . '/' . $arrayPath[2];
    }

    /**
     * @codeCoverageIgnore
     * return new class
     * @param string $class
     * @param array $construct
     * @return object
     */
    public function newClass(
        string $class,
        array $construct = []
    ) {
        if (empty($construct)) {
            return new $class();
        }
        return new $class($construct);
    }

    /**
     * @codeCoverageIgnore
     * return new app
     * @return mixed|\Laravel\Lumen\Application
     */
    public function newApp()
    {
        return app();
    }
}
