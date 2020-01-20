<?php

namespace fortress\command;

use fortress\cache\RouteCacheManager;
use fortress\core\exception\RouteException;
use fortress\core\router\Route;
use fortress\util\common\StringUtils;
use fortress\util\fs\FileUtils;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;

/**
 * Class CreateRoutesCommand
 * Команда создает маршруты на основе аннотаций методов контроллеров
 * Контроллеры должны располагаться в директории app/controller и иметь namespace app\controller*
 * Сигнатура аннотации маршрута: @<http_метод>(<url>, <имя_параметра>=<значение>,...)
 * Имена параметров:
 *      - name - имя маршрута
 *      - middleware
 * Сохраняет все распознаные маршруты в кэш-файл cache/cache_routes
 * @package fortress\command
 */
class CreateRoutesCommand extends Command {

    private const ROUTE_ANNOTATION_REGEX = "/@(get|post|put|delete|patch)\((.*?)\)/i";

    /**
     * Имя команды
     * @return string
     */
    public function getName() {
        return "create-routes";
    }

    /**
     * Описание команды
     * @return string
     */
    public function getDescription() {
        return "Create routes from controller's method annotations";
    }

    /**
     * Запуск команды с аргументами
     * @throws ReflectionException
     * @throws RouteException
     */
    public function run() {
        // Получить все неймспейсы в директории с контроллерами
        $this->writeWithData("Search for controllers");
        $composerJson = json_decode(file_get_contents("composer.json"), true);
        $namespaces = $composerJson["autoload"]["psr-4"];
        $controllerNamespaces = $this->getControllersNamespaces("app/controller", $namespaces);
        // Получить все аннотации у методов полученных выше классов, создать на основе них маршруты
        $routes = [];
        $this->writeWithData("Parsing routes");
        foreach ($controllerNamespaces as $cn) {
            $this->parseControllerAnnotationRoutes($cn, $routes);
        }
        // Закешировать маршруты
        $this->writeWithData("Caching");
        $cacheManager = new RouteCacheManager();
        $cacheManager->save($routes);
        $this->writeWithData("Done");
    }

    /**
     * Поиск максимально похожего неймспейса из composer.json, соответствующего пути
     * Если неймспейс не полный, добиваем кусками из переданного пути
     * Далее, рекурсивно обходим директорию $controllersPath, пр
     * @param string $controllersPath
     * @param array $namespaceMap
     * @param array $results
     * @return array
     */
    private function getControllersNamespaces(string $controllersPath, array $namespaceMap, &$results = []) {
        if (!is_dir($controllersPath)) {
            return [];
        }
        $maxPath = ""; $maxNamespace = "";
        foreach ($namespaceMap as $ns => $path) {
            $len = mb_strlen($path);
            if (StringUtils::startsWith($controllersPath, $path) && $len > mb_strlen($maxPath)) {
                $maxPath = $path;
                $maxNamespace = $ns;
            }
        }
        $namespaceSuffix = str_replace(DIRECTORY_SEPARATOR, "\\", mb_substr($controllersPath, mb_strlen($maxPath)));
        $controllersNamespace = $maxNamespace . $namespaceSuffix;
        $phpFiles = FileUtils::listByCondition($controllersPath, function($file) {
            return is_file($file) && StringUtils::endsWith($file, FileUtils::EXTENSION_PHP);
        });
        foreach ($phpFiles as $phpFile) {
            $results[] = "$controllersNamespace\\" . basename($phpFile, FileUtils::EXTENSION_PHP);
        }
        foreach (FileUtils::listDirs($controllersPath) as $subDir) {
            $this->getControllersNamespaces($subDir, $namespaceMap, $results);
        }
        return $results;
    }

    /**
     * Парсинг маршрутов, определенных через аннотации в контроллере
     * @param string $controllerNamespace
     * @param array $routes
     * @throws ReflectionException
     * @throws RouteException
     */
    private function parseControllerAnnotationRoutes(string $controllerNamespace, &$routes = []) {
        $ref = new ReflectionClass($controllerNamespace);
        foreach ($ref->getMethods() as $method) {
            $route = $this->createRoute($ref->getName(), $method);
            if ($route) {
                $routes[] = $route;
            }
        }
    }

    /**
     * Создание маршрута из комментария метода контроллера, содержащего
     * аннотацию, удовлетворяющую паттерну
     * @param string $controllerName
     * @param ReflectionMethod $method
     * @return Route|null
     * @throws RouteException
     */
    private function createRoute(string $controllerName, ReflectionMethod $method) {
        $comment = $method->getDocComment();
        if (!$comment) {
            return null;
        }
        preg_match(self::ROUTE_ANNOTATION_REGEX, $comment, $match);
        if (count($match) < 3) {
            return null;
        }
        $requestMethod = $match[1];
        $routeParameters = explode(",", str_replace(" ", "", $match[2]));
        $parsedRouteParams = $this->parseAnnotationRouteParams($routeParameters);
        return new Route(
            $routeParameters["name"] ?? "$controllerName",
            $parsedRouteParams["url"],
            $controllerName,
            [$requestMethod]
        );
    }

    /**
     * Парсинг параметров из аннотации маршрута
     * @param array $routeParams
     * @return mixed
     * @throws RouteException
     */
    private function parseAnnotationRouteParams(array $routeParams) {
        if (empty($routeParams)) {
            throw new RouteException("Route annotation params is empty");
        }
        for($i = 0; $i < count($routeParams); $i++) {
            if (0 === $i) {
                $parsedParams["url"] = str_replace("\"", "", $routeParams[$i]);
            } else {
                $keyValue = explode("=", $routeParams[$i]);
                if (count($keyValue) >= 2) {
                    $parsedParams[$keyValue[0]] = str_replace("\"", "", $keyValue[1]);
                }
            }
        }
        return $parsedParams;
    }
}