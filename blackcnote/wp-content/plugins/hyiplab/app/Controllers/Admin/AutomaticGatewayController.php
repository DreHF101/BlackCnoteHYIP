<?php

namespace Hyiplab\Controllers\Admin;

use Hyiplab\BackOffice\Request;
use Hyiplab\Controllers\Controller;
use Hyiplab\Models\Gateway;
use Hyiplab\Models\GatewayCurrency;
use Hyiplab\Services\GatewayService;

class AutomaticGatewayController extends Controller
{
    protected $gatewayService;

    public function __construct()
    {
        parent::__construct();
        $this->gatewayService = new GatewayService();
    }

    public function index()
    {
        $pageTitle = "Automatic Gateways";
        $gateways  = Gateway::where('code', '<', 1000)->orderBy('name', 'asc')->get();
        $this->view('admin/gateway/automatic/list', compact('pageTitle', 'gateways'));
    }

    public function status(Request $request)
    {
        $gateway = Gateway::findOrFail($request->id);
        $gateway->status = !$gateway->status;
        $gateway->save();
        
        $notify[] = ['success', 'Status changed successfully'];
        return hyiplab_back($notify);
    }

    public function edit(Request $request)
    {
        $pageTitle = 'Edit Gateway Method';
        $gateway = Gateway::findOrFail($request->id);
        $supportedCurrencies = json_decode($gateway->supported_currencies, true);
        
        $this->view('admin/gateway/automatic/edit', compact('pageTitle', 'gateway', 'supportedCurrencies'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'id'    => 'required|integer',
            'alias' => 'required',
        ]);

        $this->gatewayService->updateGateway($request->id, $request->all());

        $notify[] = ['success', 'Gateway updated successfully'];
        return hyiplab_back($notify);
    }

    public function currencyRemove(Request $request)
    {
        GatewayCurrency::where('id', $request->id)->delete();
        $notify[] = ['success', 'Gateway currency deleted successfully'];
        return hyiplab_back($notify);
    }
}
