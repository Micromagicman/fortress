<?php

namespace fortress\core;

use Exception;
use fortress\command\Command;
use fortress\core\controller\ControllerAction;
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

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }

    /**
     * Обработка HTTP-запроса
     * @param ServerRequestInterface $request
     * @return HtmlResponse|mixed
     */
    public function handleHttpRequest(ServerRequestInterface $request) {
        try {
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
