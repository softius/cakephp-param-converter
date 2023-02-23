<?php

namespace ParamConverter\Converter;

use Cake\Core\App;
use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;
use ParamConverter\ParamConverterInterface;

/**
 * Class EntityConverter
 *
 * Param Converter for Entity classes
 *
 * @package ParamConverter
 */
class EntityConverter implements ParamConverterInterface
{
    /**
     * @inheritDoc
     */
    public function supports(string $class): bool
    {
        return !empty($class) && is_subclass_of($class, Entity::class);
    }

    /**
     * @inheritDoc
     */
    public function convertTo(string $value, string $class)
    {
        $table = App::shortName($class, 'Model/Entity');
        $table = TableRegistry::getTableLocator()->get(
            Inflector::tableize($table)
        );

        return $table->get($value);
    }
}
