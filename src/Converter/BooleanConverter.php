<?php

namespace ParamConverter\Converter;

use Cake\Http\Exception\BadRequestException;
use ParamConverter\ParamConverterInterface;

/**
 * Class BooleanConverter
 *
 * Param Converter for converting boolean like request strings to bool Controller parameters
 *
 * @package ParamConverter
 */
class BooleanConverter implements ParamConverterInterface
{
    /**
     * @inheritDoc
     */
    public function supports(string $class): bool
    {
        return $class === 'bool';
    }

    /**
     * @inheritDoc
     */
    public function convertTo(string $value, string $class)
    {
        if (in_array(strtolower($value), ['1', 'true', 'yes', 'on'], true)) {
            return true;
        }

        if (in_array(strtolower($value), ['0', 'false', 'no', 'off'], true)) {
            return false;
        }
        throw new BadRequestException();
    }
}
