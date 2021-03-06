<?php


namespace App\Helpers;


class RouterHelper
{

    public const API_NAMESPACE =  '\\App\\Http\\Controllers\\Api';
    private const INDEX_METHOD = 'index';

    public static function parsePath($path): array
    {
        $pathElements = explode('/', $path);
        $pathElements = array_map('ucfirst', $pathElements);

        return $pathElements;
    }

    /**
     * @param array $pathElements
     * @param string $namespace
     *
     * @param string|null $indexMethod
     *
     * @return string[]
     */
    public static function getController(array $pathElements, string $namespace, ?string $indexMethod = null): array
    {
        if ($indexMethod) {
            $method = $indexMethod;
        } else {
            $method = lcfirst(array_pop($pathElements));
        }

        $controller = array_pop($pathElements) . 'Controller';
        $classElements = array_merge([$namespace], $pathElements, [$controller]);
        $classPath = implode('\\', $classElements);


        return [$classPath, $method];
    }

    public static function getControllerFallback(array $pathElements, string $namespace) {
        return static::getController($pathElements, $namespace, static::INDEX_METHOD);
    }

    /**
     * @param $controllers
     *
     * @return string|null
     */
    public static function getFirstExistingController($controllers): ?string
    {
        foreach ($controllers as [$class, $method]) {
            if (is_callable([$class, $method])) {
                return "{$class}@{$method}";
            }
        }
        return null;
    }

    public static function response404() {
        return response()->json(['error' => 'Wrong api path'])
            ->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
            ->setStatusCode(404)
        ;
    }

    public static function response400(array $errors, int $code = 0) {
        if ($code < 300 || $code >= 500 ) {
            $code = 400;
        }

        return response()
            ->json(['errors' => $errors])
            ->setStatusCode($code)
        ;
    }


}
