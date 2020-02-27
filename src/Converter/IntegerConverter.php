<?php

namespace ParamConverter\Converter;

use Cake\Http\Exception\BadRequestException;
use ParamConverter\ParamConverterInterface;

/**
 * Class EntityConverter
 *
 * Param Converter for converting integer-like request strings to int Controller parameters
 *
 * @package ParamConverter
 */
class IntegerConverter implements ParamConverterInterface
{
    /**
     * @inheritDoc
     */
    public function supports(string $class): bool
    {
        return $class === 'int';
    }

    /**
     * @inheritDoc
     */
    public function convertTo(string $value, string $class)
    {
        if (ctype_digit($value)) {
            return (int)$value;
        }
        throw new BadRequestException();
    }
}
