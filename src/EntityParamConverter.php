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
        if (empty($class) || !is_subclass_of($class, Entity::class)) {
            return false;
        }

        if (!preg_match('/^(.*)\\\Model\\\Entity\\\(.*)$/', $class, $matches)) {
            return false;
        }

        $tableClass = $matches[1] . '\Model\Table\\' . Inflector::pluralize(App::shortName($class, 'Model/Entity')) . 'Table';
        if (!class_exists($tableClass)) {
            return false;
        }
        return !empty($class) && is_subclass_of($class, Entity::class);
    }

    /**
     * @inheritDoc
     */
    public function convertTo(string $value, string $class)
    {
        preg_match('/^(.*)\\\Model\\\Entity\\\(.*)$/', $class, $matches);

        $tableClass = $matches[1] . '\Model\Table\\' . Inflector::pluralize(App::shortName($class, 'Model/Entity')) . 'Table';

        $table = App::shortName($class, 'Model/Entity');

        TableRegistry::getTableLocator()->set(Inflector::tableize($table), new $tableClass());
        $table = TableRegistry::getTableLocator()->get(
            Inflector::tableize($table)
        );

        return $table->get($value);
    }
}
