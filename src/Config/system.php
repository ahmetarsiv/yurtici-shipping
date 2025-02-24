<?php

return [
    [
        'key'    => 'sales.carriers.yurticishipping',
        'info'   => 'yurticishipping::app.yurticishipping.info',
        'name'   => 'yurticishipping::app.yurticishipping.name',
        'sort'   => 1,
        'fields' => [
            [
                'name'          => 'title',
                'title'         => 'yurticishipping::app.yurticishipping.system.title',
                'type'          => 'text',
                'validation'    => 'required',
                'channel_based' => false,
                'locale_based'  => true,
            ], [
                'name'          => 'description',
                'title'         => 'yurticishipping::app.yurticishipping.system.description',
                'type'          => 'textarea',
                'channel_based' => false,
                'locale_based'  => true,
            ], [
                'name'          => 'active',
                'title'         => 'yurticishipping::app.yurticishipping.system.status',
                'type'          => 'boolean',
                'validation'    => 'required',
                'channel_based' => false,
                'locale_based'  => true,
            ]
        ]
    ]
];
