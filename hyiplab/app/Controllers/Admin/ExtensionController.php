<?php

namespace Hyiplab\Controllers\Admin;

use Hyiplab\BackOffice\Request;
use Hyiplab\Controllers\Controller;
use Hyiplab\Models\Extension;

class ExtensionController extends Controller
{
    public function index()
    {
        $pageTitle = "Extensions";
        $extensions = Extension::orderBy('name', 'asc')->get();
        return $this->view('admin/extension/index', compact('pageTitle', 'extensions'));
    }

    public function update()
    {
        $request = new Request();
        $extension = Extension::findOrFail($request->id);
        $validation_rule = [];
        foreach (json_decode($extension->shortcode) as $key => $val) {
            $validation_rule = array_merge($validation_rule, [$key => 'required']);
        }
        $request->validate($validation_rule);

        $shortcode = json_decode($extension->shortcode, true);
        foreach ($shortcode as $key => $value) {
            $shortcode[$key]['value'] = $request->$key;
        }

        $extension->shortcode = json_encode($shortcode);
        $extension->save();
        $notify[] = ['success', $extension->name . ' updated successfully'];
        hyiplab_back($notify);
    }

    public function status()
    {
        $request = new Request();
        $extension = Extension::findOrFail($request->id);
        $extension->status = $extension->status ? 0 : 1;
        $extension->save();
        $notify[] = ['success', 'Status changed successfully'];
        hyiplab_back($notify);
    }
}
