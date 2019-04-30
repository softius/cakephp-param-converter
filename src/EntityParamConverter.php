<?php

namespace ParamConverter;

use Cake\Core\App;
use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;

/**
 * Class EntityParamConverter
 *
 * Param Converter for Entity classes
 *
 * @package ParamConverter
 */
class EntityParamConverter implements ParamConverterInterface
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
     *
     * @throws \Cake\Datasource\Exception\InvalidPrimaryKeyException When $value as a primaryKey has an
     *      incorrect number of elements.
     *
     * @return \Cake\ORM\Entity
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
