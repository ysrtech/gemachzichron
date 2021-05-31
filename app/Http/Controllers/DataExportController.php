<?php

namespace App\Http\Controllers;

use App\Models\Dependent;
use App\Models\Member;
use App\Services\CsvExportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Inertia\Inertia;

class DataExportController extends Controller
{
    public function index()
    {
        return Inertia::render('DataExport/Index', [
            'members' => $this->getColumns(Member::class),
            'children' => $this->getColumns(Dependent::class),

        ]);
    }

    public function show(Request $request, $model)
    {
        $model = Str::model($model);

        $columns = $request->columns
            ? explode(',', $request->columns)
            : $this->getColumns($model);

        return CsvExportService::stream(
            $model::toBase()->select($columns)->get(),
            "{$this->getTable($model)}.csv",
            $columns
        );
    }

    private function getTable($model): string
    {
        return (new $model)->getTable();
    }

    private function getColumns($model): array
    {
        return array_diff(Schema::getColumnListing($this->getTable($model)), (new $model)->getHidden());
    }
}
