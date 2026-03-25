<?php

declare(strict_types=1);

namespace App\Core\Support;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class Exporter
{
    /**
     * Export collection to CSV.
     *
     * @param  Collection  $collection
     * @param  array<string, string>  $columns  Array of [attribute => label]
     * @param  string  $filename
     * @return StreamedResponse
     */
    public static function csv(Collection $collection, array $columns, string $filename = 'export.csv'): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        return response()->stream(function () use ($collection, $columns) {
            $handle = fopen('php://output', 'w');

            // Add BOM for Excel compatibility
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            // Header
            fputcsv($handle, array_values($columns));

            // Data
            foreach ($collection as $item) {
                $row = [];
                foreach (array_keys($columns) as $attribute) {
                    $row[] = data_get($item, $attribute);
                }
                fputcsv($handle, $row);
            }

            fclose($handle);
        }, 200, $headers);
    }
}
