<?php
    namespace Marinar\Payments;

    use Marinar\Payments\Database\Seeders\MarinarPaymentsInstallSeeder;

    class MarinarPayments {

        public static function getPackageMainDir() {
            return __DIR__;
        }

        public static function injects() {
            return MarinarPaymentsInstallSeeder::class;
        }
    }
