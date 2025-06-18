<?php

namespace Hyiplab\Controllers\Admin;

use Hyiplab\BackOffice\Request;
use Hyiplab\Controllers\Controller;
use Hyiplab\Lib\FormProcessor;
use Hyiplab\Models\Form;
class KycController extends Controller
{
    public function index()
    {
        $pageTitle = 'Manage Staking';
        $form  = Form::where('act', 'kyc_form')->first();
        $this->view('admin/kyc/index', compact('pageTitle', 'form'));
    }

    public function saveKyc()
    {
        $request = new Request();
        $request->validate([
        
        ]);

        $id = $request->id;
        if ($id) {
            $form          = Form::findOrFail($id);
            $formProcessor = new FormProcessor();
            $formProcessor->generate('kyc_form',true,'id',$form->id);
            $notification  = 'updated';
        } else {
            $formProcessor = new FormProcessor();
            $formProcessor->generate('kyc_form');
            $notification  = 'added';
        }

        $notify[] = ['success', 'Kyc form ' . $notification . ' successfully'];
        hyiplab_back($notify);
    }

    public function kycStatus(){
        $request = new Request();
    }

    

}
