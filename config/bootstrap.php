<?php

use Cake\Core\Configure;
use Cake\Event\EventManager;

Configure::load('ParamConverter.param_converter');

EventManager::instance()->on(
    'Server.buildMiddleware',
    function ($event, $middlewareQueue) {
        $middlewareQueue->add(new \ParamConverter\ParamConverterMiddleware());
    });
