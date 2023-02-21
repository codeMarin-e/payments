@php $inputBag = 'payment'; @endphp
{{-- JUST FOR EXAMPLE --}}
{{--@pushonce('below_templates')--}}
{{--<div id="js_cod_template">--}}
{{--    <div>Ohaaaa</div>--}}
{{--</div>--}}
{{--@endpushonce--}}

{{-- @HOOK_TYPE_TEMPLATES --}}

@pushonceOnReady('below_js_on_ready')
<script>
    var $typeSelect = $('#{{$inputBag}}\\[type\\]');

    $(document).on('change', '#{{$inputBag}}\\[type\\]', function() {
        var $selected = $typeSelect.find("option:selected").first();

        @can('system', \App\Models\PaymentMethod::class)
            var $overviewInput = $('#{{$inputBag}}\\[overview\\]');
            //OVERVIEW
            if($overviewInput.length) {
                $overviewInput.val( $selected.attr('data-overview') );
            }
        @endcan

        $(document).trigger('type_template');
    })

    var $typeTemplateCon = $('#js_type_template_con');
    $(document).on('type_template', function() {
        var $typeTemplate = $('#js_' + $.escapeSelector( $typeSelect.val() )  + '_template');
        var content = $typeTemplate.length? $typeTemplate.html() : '';
        $typeTemplateCon.html( content );
    });
    @isset($chPaymentMethod)
        $(document).trigger('type_template');
    @else
        $('#{{$inputBag}}\\[type\\]').trigger('change');
    @endisset

</script>
@endpushonceOnReady

@pushonce('below_templates')
@if(isset($chPaymentMethod) && $authUser->can('delete', $chPaymentMethod))
    <form action="{{ route("{$route_namespace}.payments.destroy", $chPaymentMethod->id) }}"
          method="POST"
          id="delete[{{$chPaymentMethod->id}}]">
        @csrf
        @method('DELETE')
    </form>
@endif
@endpushonce

<x-admin.main>
    <div class="container-fluid">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route("{$route_namespace}.home")}}"><i class="fa fa-home"></i></a></li>
            <li class="breadcrumb-item"><a href="{{ route("{$route_namespace}.payments.index") }}">@lang('admin/payments/payments.payments')</a></li>
            <li class="breadcrumb-item active">@isset($chPaymentMethod){{ $chPaymentMethod->aVar('name') }}@else @lang('admin/payments/payment.create') @endisset</li>
        </ol>

        <div class="card">
            <div class="card-body">
                <form action="@isset($chPaymentMethod){{ route("{$route_namespace}.payments.update", [ $chPaymentMethod->id ]) }}@else{{ route("{$route_namespace}.payments.store") }}@endisset"
                      method="POST"
                      autocomplete="off"
                      enctype="multipart/form-data">
                    @csrf
                    @isset($chPaymentMethod)@method('PATCH')@endisset

                    <x-admin.box_messages />

                    <x-admin.box_errors :inputBag="$inputBag" />
                    {{-- @HOOK_BEGINING --}}

                    @php
                        $sType = old("{$inputBag}.type", (isset($chPaymentMethod)? $chPaymentMethod->type : array_key_first(\App\Models\PaymentMethod::$types)));
                    @endphp
                    <div class="form-group row">
                        <label for="{{$inputBag}}[type]"
                               class="col-lg-1 col-form-label">@lang('admin/payments/payment.types'):</label>
                        <div class="col-lg-4">
                            <select class="form-control @if($errors->$inputBag->has('type')) is-invalid @endif"
                                    id="{{$inputBag}}[type]"
                                    name="{{$inputBag}}[type]">
                                    @foreach(\App\Models\PaymentMethod::$types as $paymentType => $paymentClass)
                                        <option value="{{$paymentType}}"
                                                data-overview="{{$paymentClass::getOverviewTPLName()}}"
                                                @if($sType == $paymentType)selected='selected'@endif
                                        >{{call_user_func(array($paymentClass, 'getName'), [])}}</option>
                                    @endforeach
                            </select>
                        </div>
                    </div>
                    {{-- @HOOK_AFTER_TYPE --}}

                    <div class="form-group row">
                        <label for="{{$inputBag}}[name]"
                               class="col-lg-2 col-form-label"
                        >@lang('admin/payments/payment.name'):</label>
                        <div class="col-lg-10">
                            <input type="text"
                                   name="{{$inputBag}}[add][name]"
                                   id="{{$inputBag}}[add][name]"
                                   value="{{ old("{$inputBag}.add.name", (isset($chPaymentMethod)? $chPaymentMethod->aVar('name') : '')) }}"
                                   class="form-control @if($errors->$inputBag->has('add.name')) is-invalid @endif"
                            />
                        </div>
                    </div>
                    {{-- @HOOK_AFTER_NAME --}}

                    <div class="form-group row">
                        <label for="{{$inputBag}}[tax]"
                               class="col-lg-2 col-form-label"
                        >@lang('admin/payments/payment.tax'):</label>
                        <div class="col-lg-2">
                            <div class="input-group">
                                <input type="text"
                                       name="{{$inputBag}}[tax]"
                                       id="{{$inputBag}}[tax]"
                                       value="{{ old("{$inputBag}.tax", (isset($chPaymentMethod)? $chPaymentMethod->tax : '')) }}"
                                       class="form-control @if($errors->$inputBag->has('tax')) is-invalid @endif"
                                />
                                <div class="input-group-append">
                                    <span class="input-group-text">{{$siteCurrency}}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- @HOOK_AFTER_TAX --}}

                    @can('system', \App\Models\PaymentMethod::class)
                        {{-- OVERVIEW --}}
                        <div class="form-group row">
                            <label for="{{$inputBag}}[overview]"
                                   class="col-lg-2 col-form-label"
                            >@lang('admin/payments/payment.overview'):</label>
                            <div class="col-lg-3">
                                <input type="text"
                                       name="{{$inputBag}}[overview]"
                                       id="{{$inputBag}}[overview]"
                                       value="{{ old("{$inputBag}.overview", (isset($chPaymentMethod)? $chPaymentMethod->overview : '')) }}"
                                       class="form-control @if($errors->$inputBag->has('overview')) is-invalid @endif"
                                />
                            </div>
                        </div>
                        {{-- @HOOK_AFTER_OVERVIEW --}}
                    @endcan

                    <div class="form-group row">
                        <label for="{{$inputBag}}[add][description]"
                               class="col-lg-2 col-form-label @if($errors->$inputBag->has('add.description')) text-danger @endif"
                        >@lang('admin/payments/payment.description'):</label>
                        <div class="col-lg-10">
                            <x-admin.editor
                                :inputName="$inputBag.'[add][description]'"
                                :otherClasses="[ 'form-controll', ]"
                            >{{old("{$inputBag}.add.description", (isset($chPaymentMethod)? $chPaymentMethod->aVar('description') : ''))}}</x-admin.editor>
                        </div>
                    </div>
                    {{-- @HOOK_AFTER_DESCRIPTION --}}

                    <div id="js_type_template_con"></div>

                    {{-- @HOOK_AFTER_END --}}

                    <div class="form-group row form-check">
                        <div class="col-lg-6">
                            <input type="checkbox"
                                   value="1"
                                   id="{{$inputBag}}[default2]"
                                   name="{{$inputBag}}[default2]"
                                   class="form-check-input @if($errors->$inputBag->has('default2'))is-invalid @endif"
                                   @if(old("{$inputBag}.default2") || (is_null(old("{$inputBag}.default2")) && isset($chPaymentMethod) && $chPaymentMethod->default ))checked="checked"@endif
                            />
                            <label class="form-check-label"
                                   for="{{$inputBag}}[default2]">@lang('admin/payments/payment.default')</label>
                        </div>
                    </div>
                    {{-- @HOOK_AFTER_DEFAULT --}}

                    <div class="form-group row form-check">
                        <div class="col-lg-6">
                            <input type="checkbox"
                                   value="1"
                                   id="{{$inputBag}}[test_mode]"
                                   name="{{$inputBag}}[test_mode]"
                                   class="form-check-input @if($errors->$inputBag->has('test_mode'))is-invalid @endif"
                                   @if(old("{$inputBag}.test_mode") || (is_null(old("{$inputBag}.test_mode")) && isset($chPaymentMethod) && $chPaymentMethod->test_mode ))checked="checked"@endif
                            />
                            <label class="form-check-label"
                                   for="{{$inputBag}}[test_mode]">@lang('admin/payments/payment.test_mode')</label>
                        </div>
                    </div>
                    {{-- @HOOK_AFTER_TEST_MODE --}}

                    <div class="form-group row form-check">
                        <div class="col-lg-6">
                            <input type="checkbox"
                                   value="1"
                                   id="{{$inputBag}}[active]"
                                   name="{{$inputBag}}[active]"
                                   class="form-check-input @if($errors->$inputBag->has('active'))is-invalid @endif"
                                   @if(old("{$inputBag}.active") || (is_null(old("{$inputBag}.active")) && isset($chPaymentMethod) && $chPaymentMethod->active ))checked="checked"@endif
                            />
                            <label class="form-check-label"
                                   for="{{$inputBag}}[active]">@lang('admin/payments/payment.active')</label>
                        </div>
                    </div>
                    {{-- @HOOK_AFTER_ACTIVE --}}

                    <div class="form-group row">
                        @isset($chPaymentMethod)
                            @can('update', $chPaymentMethod)
                                <button class='btn btn-success mr-2'
                                        type='submit'
                                        name='action'>@lang('admin/payments/payment.save')
                                </button>

                                <button class='btn btn-primary mr-2'
                                        type='submit'
                                        name='update'>@lang('admin/payments/payment.update')</button>
                            @endcan

                            @can('delete', $chPaymentMethod)
                                <button class='btn btn-danger mr-2'
                                        type='button'
                                        onclick="if(confirm('@lang("admin/payments/payment.delete_ask")')) document.querySelector( '#delete\\[{{$chPaymentMethod->id}}\\] ').submit() "
                                        name='delete'>@lang('admin/payments/payment.delete')</button>
                            @endcan
                        @else
                            @can('create', App\Models\PaymentMethod::class)
                                <button class='btn btn-success mr-2'
                                        type='submit'
                                        name='create'>@lang('admin/payments/payment.create')</button>
                            @endcan
                        @endisset
                        <a class='btn btn-warning'
                           href="{{ route("{$route_namespace}.payments.index") }}"
                        >@lang('admin/payments/payment.cancel')</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-admin.main>
