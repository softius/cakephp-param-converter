<?php

namespace ParamConverter;

use Cake\Controller\Controller;
use Cake\Core\Configure;
use Closure;
use Psr\Http\Message\ResponseInterface;

class ParamConvertedController extends Controller
{
    /**
     * @param \Closure $action action
     * @param array $args arguments
     *
     * @throws \ReflectionException
     * @return void
     */
    public function invokeAction(Closure $action, array $args): void
    {
        $converters = [];
        foreach (Configure::readOrFail('ParamConverter.converters') as $converter) {
            $converters[] = new $converter();
        }
        $manager = new ParamConverterManager($converters);

        parent::invokeAction($action, $manager->apply($args, $this->name, $action));
    }
}
