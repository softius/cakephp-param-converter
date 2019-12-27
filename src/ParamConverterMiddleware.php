<?php

namespace ParamConverter;

use Cake\Controller\ControllerFactory;
use Cake\Core\Configure;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ParamConverterMiddleware implements \Psr\Http\Server\MiddlewareInterface
{
    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     *
     * @throws \ReflectionException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $factory = new ControllerFactory();
        $class = $factory->getControllerClass($request);
        if (!$class) {
            // Controller does not exist. Let Cake handle it naturally (MissingControllerException)
            $class = get_class($factory->create($request, $response));
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
