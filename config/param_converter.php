<?php
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