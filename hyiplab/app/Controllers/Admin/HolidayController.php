<?php

namespace Hyiplab\Controllers\Admin;

use Hyiplab\BackOffice\Request;
use Hyiplab\Controllers\Controller;
use Hyiplab\Models\Holiday;
class HolidayController extends Controller
{
 
    public function index()
    {
        $pageTitle = 'Holidays';
        $holidays  = Holiday::orderBy('id', 'desc')->paginate(hyiplab_paginate(20));
        $offDays    = get_option('hyiplab_off_days', []);
        $this->view('admin/holiday/index', compact( 'pageTitle','holidays','offDays'));
    }

    public function saveOffDaySetting()
    {
        $request = new Request();
        $totalOffDay = count($request->off_day ?? []);
        if ($totalOffDay == 7) {
            $notify[] = ['error', 'You couldn\'t set all days as holiday'];
            hyiplab_back($notify);
        }
        update_option('hyiplab_off_days', $request->off_day);
        $notify[] = ['success', 'Weekly Holiday Setting Updated'];
        hyiplab_back($notify);
    }

    public function saveHoliday()
    {
        $request = new Request();

        $request->validate([
            'date'  => 'required',
            'title' => 'required',
        ]);

        $holiday             = new Holiday();
        $holiday->date       = $request->date;
        $holiday->title      = $request->title;
        $holiday->created_at = hyiplab_date()->now();
        $holiday->updated_at = hyiplab_date()->now();
        $holiday->save();

        $notify[] = ['success', 'Holiday added successfully'];
        hyiplab_back($notify);
    }

    public function deleteHoliday()
    {
        $request = new Request();
        $holiday = Holiday::findOrFail($request->id);
        $holiday->delete();
        $notify[] = ['success', 'Holiday deleted successfully'];
        hyiplab_back($notify);
    }
    
}
