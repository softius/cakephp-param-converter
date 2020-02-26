<?php

namespace ParamConverter\Controller\Traits;

use Cake\Core\Configure;
use Closure;
use ParamConverter\ParamConverterManager;

/**
 * Trait ParamConverterTrait
 *
 * @package ParamConverter\Controller\Traits
 *
 * @mixin \Cake\Controller\Controller
 */
trait ParamConverterTrait
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
