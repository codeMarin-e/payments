@if($authUser->can('view', \App\Models\PaymentMethod::class))
    {{--   PAYMENT METHODS --}}
    <li class="nav-item @if(request()->route()->named("{$whereIam}.payments.*")) active @endif">
        <a class="nav-link " href="{{route("{$whereIam}.payments.index")}}">
            <i class="fa fa-fw fa-money-bill-alt mr-1"></i>
            <span>@lang("admin/payments/payments.sidebar")</span>
        </a>
    </li>
@endif
