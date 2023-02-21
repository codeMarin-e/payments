<?php
    namespace Marinar\Payments\Database\Seeders;

    use App\Models\PaymentMethod;
    use Illuminate\Database\Seeder;
    use Marinar\Payments\MarinarPayments;
    use Spatie\Permission\Models\Permission;

    class MarinarPaymentsRemoveSeeder extends Seeder {

        use \Marinar\Marinar\Traits\MarinarSeedersTrait;

        public static function configure() {
            static::$packageName = 'marinar_payments';
            static::$packageDir = MarinarPayments::getPackageMainDir();
        }

        public function run() {
            if(!in_array(env('APP_ENV'), ['dev', 'local'])) return;

            $this->autoRemove();

            $this->refComponents->info("Done!");
        }

        public function clearMe() {
            $this->refComponents->task("Clear DB", function() {
                foreach(PaymentMethod::get() as $payment) {
                    $payment->delete();
                }
                Permission::whereIn('name', [
                    'payments.view',
                    'payment.system',
                    'payment.create',
                    'payment.update',
                    'payment.delete',
                ])
                ->where('guard_name', 'admin')
                ->delete();
                app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
                return true;
            });
        }
    }
