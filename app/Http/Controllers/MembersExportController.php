<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Services\ExportService;

class MembersExportController extends Controller
{
    public function __invoke()
    {
        $columns = [
            'title',
            'first_name',
            'last_name',
            'hebrew_first_name',
            'hebrew_last_name',
            'wife_name',
            'address',
            'city',
            'postal_code',
            'email',
            'home_phone',
            'mobile_phone',
            'wife_mobile_phone',
            'shtibel',
            'father',
            'father_in_law',
        ];

        return ExportService::streamCsv(
            Member::toBase()->select($columns)->get(),
            'members.csv',
            $columns
        );
    }
}
