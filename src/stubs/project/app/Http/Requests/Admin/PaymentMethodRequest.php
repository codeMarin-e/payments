<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\PaymentMethod;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;

class PaymentMethodRequest extends FormRequest
{

    private $mergeReturn = [];

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
//        $chPaymentMethod = request()->route('chPaymentMethod');
        $rules = [
            'type' => ['required', function($attribute, $value, $fail) {
                if(!isset( PaymentMethod::$types[$value] ))
                    return $fail(trans('admin/payments/validation.type.not_found'));
            }],
            'add.name' => 'required|max:255',
            'tax' => 'numeric',
            'overview' => 'nullable|max:255',
            'add.description' => 'nullable',
            'default2' => 'boolean',
            'test_mode' => 'boolean',
            'active' => 'boolean',
        ];

        // @HOOK_REQUEST_RULES

        return $rules;
    }

    public function messages() {
        $return = Arr::dot((array)trans('admin/payments/validation'));

        // @HOOK_REQUEST_MESSAGES

        return $return;
    }

    public function validationData() {
        $inputBag = 'payment';
        $this->errorBag = $inputBag;
        $inputs = $this->all();
        if(!isset($inputs[$inputBag])) {
            throw ValidationException::withMessages([
                $inputBag => trans('admin/payments/validation.no_inputs'),
            ])->errorBag($inputBag);;
        }
        $inputs[$inputBag]['default2'] = isset($inputs[$inputBag]['default2']);
        $inputs[$inputBag]['test_mode'] = isset($inputs[$inputBag]['test_mode']);
        $inputs[$inputBag]['active'] = isset($inputs[$inputBag]['active']);
        $inputs[$inputBag]['tax'] = isset($inputs[$inputBag]['tax'])? (float)$inputs[$inputBag]['tax'] : 0;

        if(!auth()->user()->can('system', PaymentMethod::class)) {
            if($chPaymentMethod = request()->route('chPaymentMethod' )) {
                $inputs[$inputBag]['overview'] = $chPaymentMethod->overview;
            } elseif(isset($inputs[$inputBag]['type']) && isset(PaymentMethod::$types[ $inputs[$inputBag]['type'] ])) {
                $inputs[$inputBag]['overview'] = PaymentMethod::$types[ $inputs[$inputBag]['type'] ]::getOverviewTPLName();
            }
        }

        // @HOOK_REQUEST_PREPARE

        $this->replace($inputs);
        request()->replace($inputs); //global request should be replaced, too
        return $inputs[$inputBag];
    }

    public function validated($key = null, $default = null) {
        $validatedData = parent::validated($key, $default);

        // @HOOK_REQUEST_VALIDATED

        if(is_null($key)) {

            // @HOOK_REQUEST_AFTER_VALIDATED

            return array_merge($validatedData, $this->mergeReturn);
        }

        // @HOOK_REQUEST_AFTER_VALIDATED_KEY

        return $validatedData;
    }
}
