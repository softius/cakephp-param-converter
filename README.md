# Param Converter

CakePHP v4.x plugin for converting request parameters to objects. These objects replace the original parameters before dispatching the controller action and hence they can be injected as controller method arguments.

Heavily inspired by [Symfony ParamConverter](https://symfony.com/doc/current/bundles/SensioFrameworkExtraBundle/annotations/converters.html)

Test currently need updated since re-writing code with Middleware.

## Install

Using Composer:

```
composer require ali1/cakephp-param-converter
```

You then need to load the plugin. You can use the shell command:

```
bin/cake plugin load ParamConverter
```

## Usage

To use, start using typed arguments in controller methods.

Entity and FrozenDatetime examples
``` php
<?php
// src/Controller/AppointmentsController.php

class AppointmentsController extends AppController {
    public function view(Appointment $appointment): void
        {
            // users will still navigate to yoursite.com/appointments/view/65
            // but the param converter removes the need for this line: $appointment = $this->Appointment->get($id);
            // use the ConfigurableEntityConverter (see below) to use a customised getter instead of Table->get
            $this->set('appointment', $appointment);
        }
    public function onDate(FrozenDate $date): void
    {
        // navigate to yoursite.com/appointments/onDate/2023-02-22
        // $date will be the FrozenDate object
        $appointments = $this->Appointments->find('all')
            ->where([
                'Appointments.start >=' => $date->toDateString(),
                'Appointments.start <' => $date->addDay(), 'cancelled IS NULL',
            ]);
        $this->set('appointments', $appointments);
    }
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

Instead of just using the $Table->get($id) method to get an entity from the database, it allows
for custom methods as defined by table's $paramConverterGetMethod

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

Then create this file in config/

```php
<?php // config/param_converter.php

return [
    'ParamConverter' => [
        'converters' => [
            \App\ParamConverter\ConfigurableEntityConverter::class,
            // \ParamConverter\Converter\EntityConverter::class,
            \ParamConverter\Converter\DateTimeConverter::class,
            \ParamConverter\Converter\FrozenDateTimeConverter::class,
            \ParamConverter\Converter\BooleanConverter::class,
            \ParamConverter\Converter\IntegerConverter::class,
            \ParamConverter\Converter\FloatConverter::class,
        ],
    ],
];

```

Now one of the tables can utilise a more useful getter:

``` php
<?php

class AppointmentsTable extends Table {
    /**
     * @var string will be checked by Param Converter Entity Converter to see which Table method to use
     * i.e. getComprehensive($id)
     */
    public string $paramConverterGetMethod = 'getComprehensive';

    public function getComprehensive($id) {
        return $this->get(
            $id,
            ['contain' => ['Payments' => ['Users']]]
        );
    }
}
```

## Credits

- [Iacovos Constantinou][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.

[link-author]: https://github.com/softius
[link-contributors]: ../../contributors
