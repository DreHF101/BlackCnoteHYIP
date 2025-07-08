<?php

namespace Hyiplab\Controllers\User;

use Hyiplab\BackOffice\Request;
use Hyiplab\Controllers\Controller;
use Hyiplab\Services\DashboardService;

class DashboardController extends Controller
{
    protected $dashboardService;

    public function __construct()
    {
        parent::__construct();
        $this->dashboardService = new DashboardService();
    }

    public function index()
    {
        global $user_ID;
        $this->pageTitle = 'Dashboard';

        $data = $this->dashboardService->getUserDashboardData($user_ID);
        $this->view('user/dashboard', compact('data'));
    }

    public function inactive()
    {
        if (hyiplab_auth()->user->status == 1) {
            return hyiplab_redirect(hyiplab_route_link('user.home'));
        }
        $this->pageTitle = "Account Inactive";
        $this->view('user/inactive');
    }

    public function promotionalBanner()
    {
        $this->pageTitle = 'Promotional Banner';
        $banners = $this->dashboardService->getPromotionalBanners();
        $this->view('user/promotional_banner', compact('banners'));
    }

    public function kycForm()
    {
        if (!$this->dashboardService->isKycEnabled()) {
            hyiplab_abort(404);
        }

        $user = hyiplab_auth()->user;
        $kycStatus = $this->dashboardService->getUserKycStatus($user->ID);

        if ($kycStatus == 2) {
            $notify[] = ['error', 'Your KYC is under review'];
            return hyiplab_back($notify);
        }

        if ($kycStatus == 1) {
            $notify[] = ['success', 'You are already KYC verified'];
            return hyiplab_back($notify);
        }

        $this->pageTitle = 'KYC Form';
        $form = $this->dashboardService->getKycForm();
        
        if (!$form) {
            $notify[] = ['error', 'KYC form not found'];
            return hyiplab_back($notify);
        }

        $this->view('user/kyc/form', compact('form'));
    }

    public function kycData()
    {
        if (!$this->dashboardService->isKycEnabled()) {
            hyiplab_abort(404);
        }

        $this->pageTitle = 'KYC Data';
        $user = hyiplab_auth()->user;
        $kycDetails = $this->dashboardService->getUserKycDetails($user->ID);
        
        $this->view('user/kyc/index', [
            'kycData' => $kycDetails['kycData'],
            'kyc' => $kycDetails['kyc'],
            'user' => $user,
            'rejectReason' => $kycDetails['rejectReason']
        ]);
    }

    public function kycSubmit(Request $request)
    {
        $validationRules = $this->dashboardService->getKycValidationRules();
        $request->validate($validationRules);

        try {
            $this->dashboardService->submitKycData(hyiplab_auth()->user->ID, $request->all());
            
            $notify[] = ['success', 'KYC data submitted successfully'];
            hyiplab_set_notify($notify);
            return hyiplab_redirect(hyiplab_route_link('user.kyc.data'));
        } catch (\RuntimeException $e) {
            $notify[] = ['error', $e->getMessage()];
            return hyiplab_back($notify);
        }
    }
    
    public function downloadKycAttachment(Request $request)
    {
        $request->validate(['file' => 'required|string']);
        
        try {
            $fileData = $this->dashboardService->downloadKycAttachment(
                $request->file,
                hyiplab_auth()->user->ID
            );

            header('Content-Disposition: attachment; filename="' . $fileData['file_name']);
            header("Content-Type: " . $fileData['mime_type']);
            ob_clean();
            flush();
            
            return readfile($fileData['file_path']);
        } catch (\Exception $e) {
            $notify[] = ['error', 'File not found'];
            hyiplab_set_notify($notify);
            return hyiplab_back();
        }
    }
}
