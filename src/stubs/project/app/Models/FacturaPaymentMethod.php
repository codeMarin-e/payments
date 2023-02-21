<?php
    namespace App\Models;

    use App\Interfaces\PaymentMethodI;

    class FacturaPaymentMethod implements PaymentMethodI {

        // @HOOK_TRAITS

        public static function getName($replace = [], $locale = null) {
            return trans('admin/payments/payment.type.cod', $replace, $locale);
        }

        public function init($order = null) {

        }

        public function process($order = null) {
            $currentCart = $order? $order : app()->make('Cart');
            $currentCart->confirm();
            return [
                'type' => 'done',
            ];
        }

        public static function getOverviewTPLName() {
            return null; //'admin/payments/cod_overview';
        }


    }
