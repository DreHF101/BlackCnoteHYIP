<?php

namespace Hyiplab\Controllers\Admin;

use Hyiplab\BackOffice\Request;
use Hyiplab\Controllers\Controller;
use Hyiplab\Models\Form;
use Hyiplab\Models\Gateway;
use Hyiplab\Models\GatewayCurrency;
use Hyiplab\Services\GatewayService;

class ManualGatewayController extends Controller
{
    protected $gatewayService;

    public function __construct()
    {
        parent::__construct();
        $this->gatewayService = new GatewayService();
    }

    public function index()
    {
        $pageTitle = "Manual Gateways";
        $gateways  = Gateway::where('code', '>=', 1000)->orderBy('name', 'asc')->get();
        return $this->view('admin/gateway/manual/list', compact('pageTitle', 'gateways'));
    }

    public function create()
    {
        $pageTitle = "Add Manual Gateway";
        $this->view('admin/gateway/manual/create', compact('pageTitle'));
    }

    public function store(Request $request)
    {
        $this->validateManualGateway($request);
        
        $this->gatewayService->createManualGateway($request->all());

        $notify[] = ['success', 'Manual gateway has been added.'];
        return hyiplab_back($notify);
    }

    public function edit(Request $request)
    {
        $method = Gateway::findOrFail($request->id);
        $pageTitle = 'Edit Manual Gateway';
        $form = Form::where('id', $method->form_id)->first();
        
        $this->view('admin/gateway/manual/edit', compact('pageTitle', 'method', 'form'));
    }

    public function update(Request $request)
    {
        $this->validateManualGateway($request);
        
        $this->gatewayService->updateManualGateway($request->id, $request->all());

        $notify[] = ['success', 'Manual gateway updated successfully.'];
        return hyiplab_back($notify);
    }

    public function status(Request $request)
    {
        $gateway = Gateway::findOrFail($request->id);
        $gateway->status = !$gateway->status;
        $gateway->save();
        
        $notify[] = ['success', 'Gateway status changed successfully'];
        return hyiplab_back($notify);
    }
    
    protected function validateManualGateway(Request $request)
    {
        $request->validate([
            'name'           => 'required',
            'currency'       => 'required',
            'rate'           => 'required|numeric|gt:0',
            'min_limit'      => 'required|numeric|gt:0',
            'max_limit'      => 'required|numeric|gt:min_limit',
            'fixed_charge'   => 'required|numeric|min:0',
            'percent_charge' => 'required|numeric|min:0',
            'instruction'    => 'required',
        ]);
    }
}
