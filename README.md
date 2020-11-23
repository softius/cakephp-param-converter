# Param Converter

CakePHP v4.x plugin for converting request parameters to objects. These objects replace the original parameters before dispatching the controller action and hence they can be injected as controller method arguments.

Heavily inspired by [Symfony ParamConverter](https://symfony.com/doc/current/bundles/SensioFrameworkExtraBundle/annotations/converters.html)

## Install

Using Composer:

```
composer require softius/cakephp-param-converter
```

You then need to load the plugin. You can use the shell command:

```
bin/cake plugin load ParamConverter
```

## Usage

To use, your AppController needs to extended `ParamConvertedController`
```php
<?php // AppController.php
class AppController extends ParamConvertedController
{
    // AppController methods
}
```

### Configuration

By default, the plugin provides and registers converters that can be used to convert request parameters to Entity and DateTime instances as well as various scalar types.
Converters can be removed / added by adjusting the following configuration in a new file in config/param_converters.php:

``` php
<?php
// config/param_converter.php
return [
    'ParamConverter' => [
        'converters' => [
            \ParamConverter\Converter\EntityConverter::class,
            \ParamConverter\Converter\DateTimeConverter::class,
            \ParamConverter\Converter\FrozenDateTimeConverter::class,
            \ParamConverter\Converter\BooleanConverter::class,
            \ParamConverter\Converter\IntegerConverter::class,
            \ParamConverter\Converter\FloatConverter::class,
        ]
    ]
];
```

### Creating a converter

All converters must implement the `ParamConverterInterface`.

Here is an example custom converter. This one extends the EntityConverter making it more powerful.

```php
<?php // src/ParamConverter/ConfigurableEntityConverter

namespace App\ParamConverter;

use Cake\Core\App;
use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;
use ParamConverter\Converter\EntityConverter;

/**
 * Class ConfigurableEntityParamConverter
 *
 * Alternative Param Converter for Entity classes that allows custom get methods
 */
class ConfigurableEntityParamConverter extends EntityConverter
{
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

        $tableGetMethod = empty($table->paramConverterGetMethod) ? 'get' : $table->paramConverterGetMethod;

        return $table->$tableGetMethod($value);
    }
}
```

Then create a this file in config/

```php
<?php // config/param_converter.php

return [
    'ParamConverter' => [
        'converters' => [
            \App\ParamConverter\ConfigurableEntityConverter::class,
            \ParamConverter\Converter\EntityConverter::class,
            \ParamConverter\Converter\DateTimeConverter::class,
            \ParamConverter\Converter\FrozenDateTimeConverter::class,
            \ParamConverter\Converter\BooleanConverter::class,
            \ParamConverter\Converter\IntegerConverter::class,
            \ParamConverter\Converter\FloatConverter::class,
        ],
    ],
];

```
## Security

If you discover any security related issues, please email softius@gmail.com instead of using the issue tracker.

## Credits

- [Iacovos Constantinou][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.

[link-author]: https://github.com/softius
[link-contributors]: ../../contributors
