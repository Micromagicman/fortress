<?php

namespace fortress\core;

use Exception;
use fortress\command\Command;
use fortress\core\controller\ControllerAction;
use fortress\core\di\ContainerBuilder;
use fortress\core\di\loader\MapLoader;
use fortress\core\router\RouterInitializer;
use Laminas\Diactoros\Response\HtmlResponse;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;

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

    private ContainerBuilder $containerBuilder;

    public function __construct(ContainerBuilder $containerBuilder) {
        $this->containerBuilder = $containerBuilder;
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
            foreach ([RouterInitializer::class, ControllerAction::class] as $action) {
                $pipeline->pipe($action);
            }
            return $pipeline->run($request);
        } catch (Exception $exception) {
            return new HtmlResponse(sprintf(
                "%s: %s",
                get_class($exception),
                $exception->getMessage()
            ));
        }
    }

    /**
     * Обработка консольной команды
     * @param Command $command
     */
    public function handleCommand(Command $command) {
        $command->run();
    }
}
