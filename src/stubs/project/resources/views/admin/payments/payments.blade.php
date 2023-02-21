<x-admin.main>
    <div class="container-fluid">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route("{$route_namespace}.home")}}"><i class="fa fa-home"></i></a></li>
            <li class="breadcrumb-item active">@lang('admin/payments/payments.payments')</li>
        </ol>

        @can('create', App\Models\PaymentMethod::class)
            <a href="{{ route("{$route_namespace}.payments.create") }}"
               class="btn btn-sm btn-primary h5"
               title="create">
                <i class="fa fa-plus mr-1"></i>@lang('admin/payments/payments.create')
            </a>
        @endcan

        <x-admin.box_messages />

        <div class="table-responsive rounded ">
            <table class="table table-sm">
                <thead class="thead-light">
                <tr class="">
                    <th scope="col" class="text-center">@lang('admin/payments/payments.id')</th>
                    {{-- @HOOK_AFTER_ID_TH --}}

                    <th scope="col" class="w-75">@lang('admin/payments/payments.name')</th>
                    {{-- @HOOK_AFTER_NAME_TH --}}

                    <th scope="col" class="text-center">@lang('admin/payments/payments.edit')</th>
                    {{-- @HOOK_AFTER_EDIT_TH --}}

                    <th colspan="2" scope="col" class="text-center">@lang('admin/payments/payments.move_th')</th>
                    {{-- @HOOK_AFTER_MOVE_TH --}}

                    <th scope="col" class="text-center">@lang('admin/payments/payments.remove')</th>
                    {{-- @HOOK_AFTER_REMOVE_TH --}}
                </tr>
                </thead>
                <tbody>
                @forelse($paymentMethods as $paymentMethod)
                    @php
                        $paymentMethodEditUri = route("{$route_namespace}.payments.edit", $paymentMethod->id);
                        $canUpdate = $authUser->can('update', $paymentMethod);
                    @endphp
                    @if($loop->first)
                        @php $prevPayment = $paymentMethod->getPrevious(); @endphp
                    @endif
                    @if($loop->last)
                        @php $nextPayment = $paymentMethod->getNext(); @endphp
                    @endif
                    <tr data-id="{{$paymentMethod->id}}"
                        data-parent="{{$paymentMethod->parent_id}}"
                        data-show="1">
                        <td scope="row" class="text-center align-middle"><a href="{{ $paymentMethodEditUri }}"
                                                                            title="@lang('admin/payments/payments.edit')"
                            >{{ $paymentMethod->id }}</a></td>
                        {{-- @HOOK_AFTER_ID --}}

                        {{--    REAL NAME    --}}
                        <td class="w-75 align-middle">
                            <a href="{{ $paymentMethodEditUri }}"
                               title="@lang('admin/payments/payments.edit')"
                               class="@if($paymentMethod->active) @else text-danger @endif"
                            >{{ \Illuminate\Support\Str::words($paymentMethod->aVar('name'), 12,'....') }}</a>
                            @if($paymentMethod->default)<span class="badge badge-success">@lang('admin/payments/payments.default')</span>@endif
                            @if($paymentMethod->test_mode)<span class="badge badge-warning">@lang('admin/payments/payments.test_mode')</span>@endif
                        </td>
                        {{-- @HOOK_AFTER_NAME --}}

                        {{--    EDIT    --}}
                        <td class="text-center">
                            <a class="btn btn-link text-success"
                               href="{{ $paymentMethodEditUri }}"
                               title="@lang('admin/payments/payments.edit')"><i class="fa fa-edit"></i></a></td>
                        {{-- @HOOK_AFTER_EDIT--}}

                        {{--    MOVE DOWN    --}}
                        <td class="text-center">
                            @if($canUpdate && (!$loop->last || $nextPayment))
                                <a class="btn btn-link"
                                   href="{{route("{$route_namespace}.payments.move", [$paymentMethod, 'down'])}}"
                                   title="@lang('admin/payments/payments.move_down')"><i class="fa fa-arrow-down"></i></a>
                            @endif
                        </td>

                        {{--    MOVE UP   --}}
                        <td class="text-center">
                            @if($canUpdate && (!$loop->first || $prevPayment))
                                <a class="btn btn-link"
                                   href="{{route("{$route_namespace}.payments.move", [$paymentMethod,'up'])}}"
                                   title="@lang('admin/payments/payments.move_up')"><i class="fa fa-arrow-up"></i></a>
                            @endif
                        </td>
                        {{-- @HOOK_AFTER_MOVE--}}

                        {{--    DELETE    --}}
                        <td class="text-center">
                            @can('delete', $paymentMethod)
                                <form action="{{ route("{$route_namespace}.payments.destroy", $paymentMethod->id) }}"
                                      method="POST"
                                      id="delete[{{$paymentMethod->id}}]">
                                    @csrf
                                    @method('DELETE')
                                    @php
                                        $redirectTo = (!$paymentMethods->onFirstPage() && $paymentMethods->count() == 1)?
                                                $paymentMethods->previousPageUrl() :
                                                url()->full();
                                    @endphp
                                    <input type="hidden" name="redirect_to" value="{{$redirectTo}}" />
                                    <button class="btn btn-link text-danger"
                                            title="@lang('admin/payments/payments.remove')"
                                            onclick="if(confirm('@lang("admin/payments/payments.remove_ask")')) document.querySelector( '#delete\\[{{$paymentMethod->id}}\\] ').submit() "
                                            type="button"><i class="fa fa-trash"></i></button>
                                </form>
                            @endcan
                        </td>
                        {{-- @HOOK_AFTER_REMOVE --}}
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">@lang('admin/payments/payments.no_payments')</td>
                    </tr>
                @endforelse
                </tbody>
            </table>

            {{$paymentMethods->links('admin.paging')}}

        </div>
    </div>
</x-admin.main>
