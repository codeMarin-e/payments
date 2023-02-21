<?php

use App\Models\PaymentMethod;
use App\Policies\PaymentMethodPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;

Route::model('chPaymentMethod', PaymentMethod::class);
Gate::policy(PaymentMethod::class, PaymentMethodPolicy::class);

