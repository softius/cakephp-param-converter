<?php

namespace ParamConverter;

use Cake\Controller\ControllerFactory;
use Cake\Core\Configure;
use Cake\Core\Container;
use Cake\Http\ServerRequest;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use ReflectionException;

class ParamConverterMiddleware implements \Psr\Http\Server\MiddlewareInterface
{
    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     *
     * @throws ReflectionException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $container = new Container();

        $container->add(ServerRequest::class, $request);

        $factory = new ControllerFactory($container);
        $class = $factory->getControllerClass($request);
        if (!$class) {
            // Controller does not exist. Let Cake handle it naturally (MissingControllerException)
            $class = get_class($factory->create($request));
        }

        $converters = [];
        foreach (Configure::readOrFail('ParamConverter.converters') as $converter) {
            $converters[] = new $converter();
        }
        $manager = new ParamConverterManager($converters);
        $action = $request->getParam('action');
        $request = $manager->apply($request, $class, $action);

        return $handler->handle($request);
    }
}
