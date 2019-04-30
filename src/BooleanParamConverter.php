<?php

namespace ParamConverter;

use Cake\Http\Exception\BadRequestException;

/**
 * Class BooleanParamConverter
 *
 * Param Converter for converting boolean like request strings to bool Controller parameters
 *
 * @package ParamConverter
 */
class BooleanParamConverter implements ParamConverterInterface
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
     * 
     * @throws BadRequestException if URL string value cannot be interpreted as true or false
     *
     * @return bool
     */
    public function convertTo(string $value, string $class)
    {
        if (in_array(strtolower($value), ['1', 'true', 'yes', 'on'])) {
            return true;
        } elseif (in_array(strtolower($value), ['0', 'false', 'no', 'off'])) {
            return false;
        }
        throw new BadRequestException();
    }
}
