<?php
	return [
		'install' => [
            'php artisan db:seed --class="\Marinar\Payments\Database\Seeders\MarinarPaymentsInstallSeeder"',
		],
		'remove' => [
            'php artisan db:seed --class="\Marinar\Payments\Database\Seeders\MarinarPaymentsRemoveSeeder"',
        ]
	];
