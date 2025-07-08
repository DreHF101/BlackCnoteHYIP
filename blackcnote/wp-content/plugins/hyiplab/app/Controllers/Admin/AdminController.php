<?php

namespace Hyiplab\Controllers\Admin;

use Hyiplab\BackOffice\Request as BackOfficeRequest;
use Hyiplab\Controllers\Controller;
use Hyiplab\Services\DashboardService;
use Hyiplab\Services\ReportService;
use Hyiplab\Services\DownloadService;

class AdminController extends Controller
{
    protected $dashboardService;
    protected $reportService;
    protected $downloadService;

    public function __construct()
    {
        parent::__construct();
        $this->dashboardService = new DashboardService();
        $this->reportService = new ReportService();
        $this->downloadService = new DownloadService();
    }

    public function dashboard()
    {
        $pageTitle = 'Dashboard';
        $data = $this->dashboardService->getDashboardData();
        
        $this->view('admin/dashboard', array_merge(compact('pageTitle'), $data));
    }

    public function requestReport()
    {
        $pageTitle = 'Your Listed Report & Request';
        $reports = $this->reportService->getReports();

        if (is_wp_error($reports)) {
            $notify[] = ['error', $reports->get_error_message()];
            return hyiplab_redirect(menu_page_url(HYIPLAB_PLUGIN_NAME, false), $notify);
        }

        $this->view('admin/reports', compact('pageTitle', 'reports'));
    }

    public function requestReportSubmit(BackOfficeRequest $request)
    {
        $request->validate([
            'type'    => 'required|in:bug,feature',
            'message' => 'required',
        ]);

        $result = $this->reportService->submitReport($request->type, $request->message);

        if (is_wp_error($result)) {
            $notify[] = ['error', $result->get_error_message()];
            return hyiplab_back($notify);
        }

        $notify[] = ['success', $result];
        return hyiplab_back($notify);
    }

    public function download()
    {
        $filePath = hyiplab_request()->file_path;
        $result = $this->downloadService->downloadFile($filePath);

        if (is_wp_error($result)) {
            // Since the service handles exit on success, we only need to handle error.
            // Perhaps redirect back with an error message.
            $notify[] = ['error', $result->get_error_message()];
            return hyiplab_back($notify);
        }
    }
}
