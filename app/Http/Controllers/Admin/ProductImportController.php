<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Throwable;

class ProductImportController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'file' => ['required', 'file', 'max:10240', 'mimes:xlsx,csv,txt', 'extensions:xlsx,csv'],
        ]);

        try {
            $sheet = IOFactory::load($validated['file']->getRealPath())->getSheet(0);
            $rows = $sheet->toArray(null, true, false, false);
        } catch (Throwable) {
            throw ValidationException::withMessages([
                'file' => 'The uploaded file could not be read. Upload a valid .xlsx or .csv file.',
            ]);
        }

        $requiredHeaders = ['name', 'sku', 'price', 'stock'];
        $headerRowIndex = null;
        $headerPositions = [];
        $firstRowHeaders = array_map(
            fn (mixed $header): string => $this->normalizeHeader($header),
            $rows[0] ?? []
        );

        foreach (array_slice($rows, 0, 10, true) as $rowIndex => $cells) {
            $headers = array_map(
                fn (mixed $header): string => $this->normalizeHeader($header),
                $cells
            );

            if (array_diff($requiredHeaders, $headers) === []) {
                $headerRowIndex = $rowIndex;
                $headerPositions = array_flip($headers);
                break;
            }
        }

        if ($headerRowIndex === null) {
            throw ValidationException::withMessages([
                'file' => 'Could not find Name, Sku, Price, Stock.',
            ]);
        }

        $extraHeaders = array_values(array_filter($headers, fn (string $header): bool => $header !== '' && ! in_array($header, $requiredHeaders, true)));

        if ($extraHeaders !== []) {
            throw ValidationException::withMessages([
                'file' => 'Unexpected column header(s): '.implode(', ', $extraHeaders).'. Only Name, Sku, Price, and Stock are allowed.',
            ]);
        }

        $allowedColumnPositions = array_values(array_intersect_key($headerPositions, array_flip($requiredHeaders)));

        $dataRows = array_slice($rows, $headerRowIndex + 1);

        if ($dataRows === []) {
            throw ValidationException::withMessages([
                'file' => 'The file needs at least one product row after the header row.',
            ]);
        }

        $fields = ['name', 'sku', 'price', 'stock'];
        $validRows = [];
        $errors = [];

        foreach ($dataRows as $index => $cells) {
            $rowNumber = $headerRowIndex + $index + 2;
            $values = [];

            foreach ($fields as $field) {
                $values[$field] = trim((string) ($cells[$headerPositions[$field]] ?? ''));
            }

            if (count(array_filter($values, fn (string $value): bool => $value !== '')) === 0) {
                continue;
            }

            foreach ($cells as $columnIndex => $cell) {
                if (! in_array($columnIndex, $allowedColumnPositions, true) && trim((string) $cell) !== '') {
                    $columnName = trim((string) ($rows[$headerRowIndex][$columnIndex] ?? '')) ?: 'Column '.($columnIndex + 1);
                    $errors[] = 'Row '.$rowNumber.': unexpected value "'.$cell.'" in extra column "'.$columnName.'".';
                }
            }

            $validator = Validator::make($values, [
                'name' => ['required', 'string', 'max:255'],
                'sku' => ['required', 'string', 'max:255'],
                'price' => ['required', 'numeric', 'min:100'],
                'stock' => ['required', 'integer', 'min:0'],
            ]);

            if ($validator->fails()) {
                $errors[] = 'Row '.$rowNumber.': '.implode(' ', $validator->errors()->all());
                continue;
            }

            $validRows[] = $values;
        }

        if ($errors !== []) {
            throw ValidationException::withMessages(['file' => $errors]);
        }

        $created = 0;
        $updated = 0;

        DB::transaction(function () use ($validRows, &$created, &$updated): void {
            foreach ($validRows as $values) {
                $product = Product::withTrashed()->where('sku', $values['sku'])->first();

                if ($product) {
                    if ($product->trashed()) {
                        $product->restore();
                    }

                    $product->update(['price' => $values['price'], 'stock' => $values['stock']]);
                    $updated++;
                    continue;
                }

                Product::create($values);
                $created++;
            }
        });

        return redirect()->route('admin.products.index')
            ->with('status', "Import complete: {$created} created, {$updated} updated.");
    }

    private function normalizeHeader(mixed $header): string
    {
        $header = mb_strtolower((string) $header);

        return preg_replace('/[\\p{Z}\\s\\x{FEFF}]+/u', '', $header) ?? $header;
    }
}
