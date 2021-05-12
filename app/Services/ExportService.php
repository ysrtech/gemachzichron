<?php

namespace App\Services;

use Illuminate\Support\Collection;

class ExportService
{
    public static function streamCsv(Collection $data, string $fileName = 'file.csv', array $columnHeaders = null)
    {
        $callback = function () use ($data, $columnHeaders) {

            $file = fopen('php://output', 'w');

            fwrite($file, "\xEF\xBB\xBF");

            if ($columnHeaders) fputcsv($file, $columnHeaders);

            $data->each(fn($row) => fputcsv($file, (array) $row));

            fclose($file);
        };

        $headers = [
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename={$fileName}",
            "Expires"             => "0",
            "Pragma"              => "no-cache",
        ];

        return response()->stream(
            $callback,
            200,
            $headers
        );
    }
}
