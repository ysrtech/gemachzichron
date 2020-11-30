<?php

namespace App\Services;

class ExportService
{
    public static function streamCsv(array $data, string $fileName = 'file.csv', array $columnHeaders = null)
    {
        $callback = function () use ($data, $columnHeaders) {

            $file = fopen('php://output', 'w');

            fwrite($file, "\xEF\xBB\xBF");

            if ($columnHeaders) {
                fputcsv($file, $columnHeaders);
            }

            foreach ($data as $member) {
                fputcsv($file, $member);
            }

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
