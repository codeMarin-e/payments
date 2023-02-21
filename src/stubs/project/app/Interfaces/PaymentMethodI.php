<?php
    namespace App\Interfaces;

    interface PaymentMethodI {
        public static function getName($replace = [], $locale = null);
        public function init($order = null);
        public function process($order = null);
        public static function getOverviewTPLName();

    }
