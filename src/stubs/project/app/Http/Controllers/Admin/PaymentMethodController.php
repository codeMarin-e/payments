<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\PaymentMethodRequest;
use App\Models\PaymentMethod;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller {
    public function __construct() {
        if(!request()->route()) return;

        $this->db_table = PaymentMethod::getModel()->getTable();
        $this->routeNamespace = Str::before(request()->route()->getName(), '.payments');
        View::composer('admin/payments/*', function($view)  {
            $viewData = [
                'route_namespace' => $this->routeNamespace,
            ];
            // @HOOK_VIEW_COMPOSERS
            $view->with($viewData);
        });
        // @HOOK_CONSTRUCT
    }

    public function index() {
        $viewData = [];
        $bldQry = PaymentMethod::where("{$this->db_table}.site_id", app()->make('Site')->id)->orderBy("{$this->db_table}.ord", 'ASC');

        // @HOOK_INDEX_END

        $viewData['paymentMethods'] = $bldQry->paginate(20)->appends( request()->query() );

        return view('admin/payments/payments', $viewData);
    }

    public function create() {
        $viewData = [];
        // @HOOK_CREATE

        return view('admin/payments/payment', $viewData);
    }

    public function edit(PaymentMethod $chPaymentMethod) {
        $viewData = [];
        $viewData['chPaymentMethod'] = $chPaymentMethod;

        // @HOOK_EDIT

        return view('admin/payments/payment', $viewData);
    }

    public function store(PaymentMethodRequest $request) {
        $validatedData = $request->validated();

        // @HOOK_STORE_VALIDATE

        $chPaymentMethod = PaymentMethod::create( array_merge([
            'site_id' => app()->make('Site')->id,
        ], $validatedData));

        // @HOOK_STORE_INSTANCE

        $chPaymentMethod->setAVars($validatedData['add']);
        $chPaymentMethod->setDefault($validatedData['default2']);

        // @HOOK_STORE_END
        event( 'payment.submited', [$chPaymentMethod, $validatedData] );

        return redirect()->route($this->routeNamespace.'.payments.edit', $chPaymentMethod)
            ->with('message_success', trans('admin/payments/payment.created'));
    }

    public function update(PaymentMethod $chPaymentMethod, PaymentMethodRequest $request) {
        $validatedData = $request->validated();

        // @HOOK_UPDATE_VALIDATE

        $chPaymentMethod->update( $validatedData );
        $chPaymentMethod->setAVars($validatedData['add']);
        $chPaymentMethod->setDefault($validatedData['default2']);

        // @HOOK_UPDATE_END

        event( 'payment.submited', [$chPaymentMethod, $validatedData] );
        if($request->has('action')) {
            return redirect()->route($this->routeNamespace.'.payments.index')
                ->with('message_success', trans('admin/payments/payment.updated'));
        }
        return back()->with('message_success', trans('admin/payments/payment.updated'));
    }

    public function move(PaymentMethod $chPaymentMethod, $direction) {
        // @HOOK_MOVE

        $chPaymentMethod->orderMove($direction);

        // @HOOK_MOVE_END

        return back();
    }

    public function destroy(PaymentMethod $chPaymentMethod, Request $request) {
        // @HOOK_DESTROY

        $chPaymentMethod->delete();

        // @HOOK_DESTROY_END

        if($request->redirect_to)
            return redirect()->to($request->redirect_to)
                ->with('message_danger', trans('admin/payments/payment.deleted'));

        return redirect()->route($this->routeNamespace.'.payments.index')
            ->with('message_danger', trans('admin/payments/payment.deleted'));
    }
}
