<?php
return [
    'models' => [
        'gem' => Farajzadeh\GemsCounter\Models\Gem::class,
        'transaction' => Farajzadeh\GemsCounter\Models\Transaction::class,
    ],

    'table_names' => [
        'gems' => 'gems',
        'transactions' => 'transactions',
    ],

    'column_names' => [
        'user_foreign_key' => 'user_id',
        'transaction_foreign_key' => 'transaction_id'
    ],
];