<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\AddVariable;
use App\Traits\MacroableModel;
use App\Traits\Orderable;
use App\Traits\Discountable;
use Illuminate\Support\Facades\DB;

class PaymentMethod extends Model  {
    use MacroableModel;

    protected $fillable = ['site_id', 'type', 'tax', 'default', 'test_mode', 'active', 'ord', 'overview'];

    public static $types = [
        'cod' => \App\Models\FacturaPaymentMethod::class,
        // @HOOK_TYPES
    ];

    use Orderable;
    use AddVariable;
    use Discountable;

    // @HOOK_TRAITS

    public function setDefault($value) {
        if($this->default == $value) return;
        if($value) {
            static::query()
                ->addBinding([$this->id], 'join')
                ->update([
                    'default' => DB::raw("CASE WHEN id = ? then 1 ELSE 0 END"),
                    'updated_at' => new \Datetime(),
                ]);
            return;
        }
        $this->default = (bool)$value;
        $this->save();
    }

    public function getDiscountValue($tax = null) {
        $tax = is_numeric($tax)? $tax : $this->getTax(true, false, false);
        $return = 0;
        foreach($this->activeDiscounts() as $discount) {
            //may put your logic here
            // @HOOK_DISCOUNT_VALUE_LOOP
            $return += $discount->getValue($tax);
        }
        return $return;
    }

    public function getVatPercent() {
        if(is_null($this->vat))
            return (float)config('app.VAT');
        return (float)$this->vat;
    }

    public function getVat($tax = null, $withDiscounts = true) {
        $tax = is_numeric($tax)? $tax : $this->getTax(true, $withDiscounts, false);
        $vatPercent = $this->getVatPercent();
        if(config('app.VAT_IN_PAYMENT')) {
            return $tax - ( $tax / ( 1 + ($vatPercent/100) ) );
        }
        return $tax * ($vatPercent/100);
    }

    public function getTax($withNew = true, $withDiscounts = true, $withVat = true) {
        $tax = $this->tax;
        if($withNew && ($newTax = $this->new_tax)) {
            $tax = $newTax;
        }
        if($withDiscounts) {
            $tax -= $this->getDiscountValue($tax);
        }
        if(config('app.VAT_IN_PAYMENT')) {
            if(!$withVat) {
                $tax -= $this->getVat($tax, $withDiscounts);
            }
        } else {
            if($withVat) {
                $tax += $this->getVat($tax, $withDiscounts);
            }
        }
        return $tax;
    }
}
