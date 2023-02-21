<?php
    namespace Marinar\Payments\Database\Seeders;

    use Illuminate\Database\Seeder;
    use Marinar\Payments\MarinarPayments;

    class MarinarPaymentsInstallSeeder extends Seeder {

        use \Marinar\Marinar\Traits\MarinarSeedersTrait;

        public static function configure() {
            static::$packageName = 'marinar_payments';
            static::$packageDir = MarinarPayments::getPackageMainDir();
        }

        public function run() {
            if(!in_array(env('APP_ENV'), ['dev', 'local'])) return;

            $this->autoInstall();

            $this->refComponents->info("Done!");
        }

    }
