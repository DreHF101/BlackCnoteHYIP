<?php

namespace Hyiplab\Controllers\Admin;

use Hyiplab\BackOffice\Request;
use Hyiplab\Controllers\Controller;
use Hyiplab\Models\PromotionalTool;

class PromotionalToolController extends Controller
{
    public function promotion()
    {
        $pageTitle = 'Promotional Tool';
        $promotionalTools  = PromotionalTool::orderBy('id', 'desc')->paginate(hyiplab_paginate());
        $this->view('admin/promotional/index', compact('pageTitle', 'promotionalTools'));
    }

    public function savePromotionl()
    {
        $request = new Request();
        $request->validate([
            'image_input' => 'required|image|mimes:jpeg,jpg,png,gif',
            'name'        => 'required',
        ]);

        $id = $request->id;
        if ($id) {
            $promotionalTool = PromotionalTool::findOrFail($id);
            $notification    = 'updated';
        } else {
            $promotionalTool = new PromotionalTool();
            $notification    = 'added';
        }

        if ($request->hasFile('image_input')) {
            $uploadAttachment = $this->storePromotionAttachment( $promotionalTool, $request );
            if ($uploadAttachment != 200) {
                hyiplab_back($uploadAttachment);
            }
        }

        $promotionalTool->name             = $request->name;
        $promotionalTool->created_at       = hyiplab_date()->now();
        $promotionalTool->updated_at       = hyiplab_date()->now();
        $promotionalTool->save();

        $notify[] = ['success', 'Promotional tool ' . $notification . ' successfully'];
        hyiplab_back($notify);
    }

    public function storePromotionAttachment( $promotionalTool, $request ){
        $path = hyiplab_file_path('promotional');
        try {
            $promotionalTool->banner = hyiplab_file_uploader($request->image_input, $path);
        } catch (\Exception $exp) {
            $notify[] = ['error', 'File could not upload'];
            return $notify;
        }
        return 200;
    }

    public function promotionDelete()
    {
        $request         = new Request();
        $PromotionalTool = PromotionalTool::findOrFail($request->id);
        $path = hyiplab_file_path('promotional');
        hyiplab_file_manager()->removeFile($path . '/' . $PromotionalTool->banner);
        $PromotionalTool->delete();

        $notify[] = ['success', "Promotional tool deleted successfully"];
        hyiplab_back($notify);
    }


}
