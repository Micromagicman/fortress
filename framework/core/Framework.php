<?php

namespace fortress\core;

use Exception;
use fortress\command\Command;
use fortress\core\controller\ControllerAction;
use fortress\core\di\ContainerBuilder;
use fortress\core\di\exception\DependencyNotFoundException;
use fortress\core\di\loader\MapLoader;
use fortress\core\exception\handler\ResponseExceptionHandler;
use fortress\core\router\RouterInitializer;
use fortress\security\csrf\CsrfTokenValidator;
use Laminas\Diactoros\Response\HtmlResponse;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

/**
 * Class Framework
 * @package fortress\core
 */
class Framework {

    /**
     * Контейнер внедрения зависимостей
     * @var ContainerInterface
     */
    private ContainerInterface $container;

    /**
     * Объект, управляющий предварительной настройкой контейнера зависимостей
     * @var ContainerBuilder
     */
    private ContainerBuilder $containerBuilder;

    /**
     * Флаг, указывающий на режим запуска приложения
     * @var bool
     */
    private bool $devMode;

    public function __construct(ContainerBuilder $containerBuilder, bool $devMode = true) {
        $this->containerBuilder = $containerBuilder;
        $this->devMode = $devMode;
    }

    /**
     * Обработка HTTP-запроса
     * @param ServerRequestInterface $request
     * @return HtmlResponse|mixed
     */
    public function handleHttpRequest(ServerRequestInterface $request) {
        try {
            $this->containerBuilder->withLoaders(new MapLoader([ServerRequestInterface::class => $request]));
            $this->container = $this->containerBuilder->build();
            $pipeline = new ActionPipeline($this->container);
            foreach ($this->getCoreActions() as $action) {
                $pipeline->pipe($action);
            }
            return $pipeline->run($request);
        } catch (Throwable $exception) {
            $exceptionHandler = new ResponseExceptionHandler($this->container, $this->devMode);
            return $exceptionHandler->handle($request, $exception);
        }
    }

    /**
     * Обработка консольной команды
     * @param string $commandName
     * @param array $arguments
     */
    public function handleCommand(string $commandName, array $arguments = []) {
        $this->container = $this->containerBuilder
            ->withLoaders(new MapLoader(Command::getNativeCliCommands()))
            ->build();
        try {
            $commandClass = $this->container->get($commandName);
            $command = $this->container->get($commandClass);
            $command->setArguments($arguments);
            $command->run();
        } catch (DependencyNotFoundException $exception) {
            die(sprintf("Command with name '%s' not defined", $commandName));
        } catch (Exception $exception) {
            die(sprintf("Command excution error: %s", $exception->getMessage()));
        }
    }

    /**
     * @return array
     */
    private function getCoreActions() {
        return [
            CsrfTokenValidator::class,
            RouterInitializer::class,
            ControllerAction::class
        ];
    }
}
