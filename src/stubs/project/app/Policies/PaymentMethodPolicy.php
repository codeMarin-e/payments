<?php

namespace App\Policies;

use App\Models\PaymentMethod;
use App\Models\User;

class PaymentMethodPolicy
{
    public function before(User $user, $ability) {
        // @HOOK_POLICY_BEFORE
        if($user->hasRole('Super Admin', 'admin') )
            return true;
    }

    public function view(User $user) {
        // @HOOK_POLICY_VIEW
        return $user->hasPermissionTo('payments.view', request()->whereIam());
    }

    public function system(User $user) {
        // @HOOK_POLICY_SYSTEM
        return $user->hasPermissionTo('payment.system', request()->whereIam());
    }

    public function create(User $user) {
        // @HOOK_POLICY_CREATE
        return $user->hasPermissionTo('payment.create', request()->whereIam());
    }

    public function update(User $user, PaymentMethod $chPaymentMethod) {
        // @HOOK_POLICY_UPDATE
        if( !$user->hasPermissionTo('payment.update', request()->whereIam()) )
            return false;
        return true;
    }

    public function delete(User $user, PaymentMethod $chPaymentMethod) {
        // @HOOK_POLICY_DELETE
        if( !$user->hasPermissionTo('payment.delete', request()->whereIam()) )
            return false;
        return true;
    }

    // @HOOK_POLICY_END


}
