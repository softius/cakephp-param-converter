<?php
return [
    'ParamConverter' => [
        'converters' => [
            \ParamConverter\ParamConverter\EntityParamConverter::class,
            \ParamConverter\ParamConverter\DateTimeParamConverter::class,
            \ParamConverter\ParamConverter\FrozenDateTimeParamConverter::class,
            \ParamConverter\ParamConverter\BooleanParamConverter::class,
            \ParamConverter\ParamConverter\IntegerParamConverter::class,
            \ParamConverter\ParamConverter\FloatParamConverter::class,
        ],
    ],
];
