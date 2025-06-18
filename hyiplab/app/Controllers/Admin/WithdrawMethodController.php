<?php

namespace Hyiplab\Controllers\Admin;

use Hyiplab\BackOffice\Request;
use Hyiplab\Controllers\Controller;
use Hyiplab\Lib\FormProcessor;
use Hyiplab\Models\Form;
use Hyiplab\Models\WithdrawMethod;

class WithdrawMethodController extends Controller
{

    public function methods()
    {
        $pageTitle = 'Withdrawal Methods';
        $methods   = WithdrawMethod::orderBy('name', 'asc')->get();
        return $this->view('admin/withdraw/index', compact('pageTitle', 'methods'));
    }

    public function create()
    {
        $pageTitle = "New Withdrawal Method";
        $this->view('admin/withdraw/create', compact('pageTitle'));
    }

    public function store()
    {
        $request = new Request();
        $request->validate([
            'name'           => 'required',
            'currency'       => 'required',
            'rate'           => 'required|numeric',
            'min_limit'      => 'required|numeric',
            'max_limit'      => 'required|numeric',
            'fixed_charge'   => 'required|numeric',
            'percent_charge' => 'required|numeric'
        ]);

        $formProcessor = new FormProcessor();
        $generate      = $formProcessor->generate('withdraw_method');

        $data = [
            'name'           => sanitize_text_field($request->name),
            'form_id'        => $generate->data->id ?? 0,
            'currency'       => sanitize_text_field($request->currency),
            'rate'           => sanitize_text_field($request->rate),
            'min_limit'      => sanitize_text_field($request->min_limit),
            'max_limit'      => sanitize_text_field($request->max_limit),
            'fixed_charge'   => sanitize_text_field($request->fixed_charge),
            'percent_charge' => sanitize_text_field($request->percent_charge),
            'description'    => balanceTags(wp_kses($request->instruction, hyiplab_allowed_html())),
            'status'         => 1
        ];

        $withdrawMethod = new WithdrawMethod();
        $withdrawMethod->insert($data);

        $notify[] = ['success', 'Withdrawal method added successfully'];
        hyiplab_back($notify);
    }

    public function edit()
    {
        $request = new Request();
        $method  = WithdrawMethod::findOrFail($request->id);
        $form      = Form::where('id', $method->form_id)->first();
        $pageTitle = "Edit Withdrawal Method";
        $this->view('admin/withdraw/edit', compact('pageTitle', 'method', 'form'));
    }

    public function update()
    {
        $request = new Request();
        $request->validate([
            'name'           => 'required',
            'currency'       => 'required',
            'rate'           => 'required|numeric',
            'min_limit'      => 'required|numeric',
            'max_limit'      => 'required|numeric',
            'fixed_charge'   => 'required|numeric',
            'percent_charge' => 'required|numeric'
        ]);

        $formProcessor = new FormProcessor();
        $method        = WithdrawMethod::findOrFail($request->id);

        $formProcessor = new FormProcessor();
        if ($method->form_id) {
            $generate = $formProcessor->generate('withdraw_method', true, 'id', $method->form_id);
        } else {
            $generate = $formProcessor->generate('withdraw_method');
        }

        $method->name           = sanitize_text_field($request->name);
        $method->form_id        = $generate->data->id ?? 0;
        $method->currency       = sanitize_text_field($request->currency);
        $method->rate           = floatval($request->rate);
        $method->min_limit      = floatval($request->min_limit);
        $method->max_limit      = floatval($request->max_limit);
        $method->fixed_charge   = floatval($request->fixed_charge);
        $method->percent_charge = floatval($request->percent_charge);
        $method->description    = balanceTags(wp_kses($request->instruction, hyiplab_allowed_html()));
        $method->save();

        $notify[] = ['success', 'Withdrawal method updated successfully'];
        hyiplab_back($notify);
    }

    public function status()
    {
        $request = new Request();
        $method  = WithdrawMethod::findOrFail($request->id);
        $method->status = $method->status ? 0 : 1;
        $method->save();
        $notify[] = ['success', 'Status changed successfully'];
        hyiplab_back($notify);
    }
}
