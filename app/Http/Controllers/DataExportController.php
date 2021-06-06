<?php

namespace App\Http\Controllers;

use App\Models\Dependent;
use App\Models\Loan;
use App\Models\Member;
use App\Models\PaymentMethod;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\User;
use App\Services\CsvExportService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Inertia\Inertia;

class DataExportController extends Controller
{
    public function index()
    {
        return Inertia::render('DataExport/Index', [
//            'models' => [
//                'members'         => ['columns' => $this->getColumns(Member::class)],
//                'children'        => ['columns' => $this->getColumns(Dependent::class)],
//                'subscriptions'   => ['columns' => $this->getColumns(Subscription::class)],
//                'transactions'    => ['columns' => $this->getColumns(Transaction::class)],
//                'loans'           => ['columns' => $this->getColumns(Loan::class)],
//                'guarantors'      => ['columns' => Schema::getColumnListing('guarantors')],
//                'payment_methods' => ['columns' => $this->getColumns(PaymentMethod::class)],
//            ]
        ]);
    }

    public function show(Request $request, $model)
    {
        try {
            $model = Str::model($model);
        } catch (Exception $exception) {
            // model is a (relation) table
            return CsvExportService::stream(
                DB::table($model)->get(),
                "$model.csv",
                Schema::getColumnListing($model)
            );
        }

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
