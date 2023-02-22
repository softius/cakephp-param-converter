<?php

namespace ParamConverter;

use Cake\Controller\Exception\MissingActionException;
use Cake\Http\Exception\BadRequestException;
use Cake\Http\ServerRequest;
use ReflectionException;
use ReflectionMethod;
use ReflectionParameter;

class ParamConverterManager
{
    /**
     * @var \ParamConverter\ParamConverterInterface[]
     */
    private $converters;

    /**
     * ParamConverterManager constructor.
     * @param \ParamConverter\ParamConverterInterface[] $paramConverters List of converters
     */
    public function __construct(array $paramConverters)
    {
        foreach ($paramConverters as $paramConverter) {
            $this->add($paramConverter);
        }
    }

    /**
     * Add the specified converter
     *
     * @param \ParamConverter\ParamConverterInterface $paramConverter Param Converter to be add
     * @return void
     */
    public function add(ParamConverterInterface $paramConverter): void
    {
        $this->converters[] = $paramConverter;
    }

    /**
     * Returns all the registered param converters
     *
     * @return \ParamConverter\ParamConverterInterface[]
     */
    public function all(): array
    {
        return $this->converters;
    }

    /**
     * Applies all the registered converters to the specified request
     *
     * @param ServerRequest $request
     * @param string $controller Controller name
     * @param string $action action name
     *
     * @return ServerRequest
     */
    public function apply(ServerRequest $request, string $controller, string $action): ServerRequest
    {
        try {
            $method = new ReflectionMethod($controller, $action);
        } catch (ReflectionException $e) {
            throw new MissingActionException([
                'controller' => $request->getParam('controller') . 'Controller',
                'action' => $request->getParam('action'),
                'prefix' => $request->getParam('prefix') ?: '',
                'plugin' => $request->getParam('plugin'),
            ]);
        }
        $methodParams = $method->getParameters();
        $requestParams = $request->getParam('pass');

        $stopAt = min(count($methodParams), count($requestParams));
        for ($i = 0; $i < $stopAt; $i++) {
            $classOrType = $this->getClassOrType($methodParams[$i]);
            if (!empty($classOrType)) {
                $requestParams[$i] = $this->convertParam($requestParams[$i], $classOrType);
            }
        }

        return $request->withParam('pass', $requestParams);
    }

    /**
     * Converts the specified string value to the specified class
     *
     * @param string $value Raw value to be converted to a class
     * @param string $class Target class
     * @return mixed
     */
    private function convertParam(string $value, string $class)
    {
        foreach ($this->all() as $converter) {
            if ($converter->supports($class)) {
                return $converter->convertTo($value, $class);
            }
        }

        throw new BadRequestException();
    }

    /**
     * Returns the class or type defined (type-hint) for the specified parameter
     *
     * @param \ReflectionParameter $parameter Parameter to be checked
     * @return null|string
     */
    private function getClassOrType(ReflectionParameter $parameter): ?string
    {
        $class = $parameter->getClass();
        if ($class !== null) {
            return $class->getName();
        }

        if ($parameter->getType() !== null && $parameter->getType()->getName() !== 'string') {
            return $parameter->getType()->getName();
        }

        return null;
    }
}
