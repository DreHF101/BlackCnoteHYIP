<?php

namespace Hyiplab\Controllers\Admin;

use Hyiplab\BackOffice\Request;
use Hyiplab\Controllers\Controller;
use Hyiplab\Models\TimeSetting;

class TimeController extends Controller
{
    public function index()
    {
        $pageTitle = "Time Settings";
        $times     = TimeSetting::orderBy('id')->get();
        $this->view('admin/time/index', compact('pageTitle', 'times'));
    }

    public function store()
    {
        $request = new Request();
        $request->validate([
            'name' => 'required',
            'time' => 'required|numeric|min:1'
        ]);
        $time       = new TimeSetting();
        $time->name = sanitize_text_field($request->name);
        $time->time = sanitize_text_field($request->time);
        $time->status = 1;
        $time->save();
        $notify[] = ['success', 'Time added successfully'];
        hyiplab_back($notify);
    }

    public function update()
    {
        $request = new Request();
        $request->validate([
            'name' => 'required',
            'time' => 'required|numeric|min:1'
        ]);

        $time       = TimeSetting::findOrFail($request->id);
        $time->name = sanitize_text_field($request->name);
        $time->time = sanitize_text_field($request->time);
        $time->save();
        $notify[] = ['success', 'Time updated successfully'];
        hyiplab_back($notify);
    }

    public function status() 
    {
        $request      = new Request();
        $time         = TimeSetting::findOrFail($request->id);
        $time->status = $time->status ? 0 : 1;
        $time->save();
        $notify[] = ['success', "Time Settings status changed successfully"];
        hyiplab_back($notify);
    }
}
