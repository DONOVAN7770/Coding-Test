<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OrderExportController extends Controller
{
    public function __invoke(Request $request): StreamedResponse
    {
        $filters = $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date' => $request->filled('start_date')
                ? ['nullable', 'date', 'after_or_equal:start_date']
                : ['nullable', 'date'],
        ]);

        if (! empty($filters['start_date']) xor ! empty($filters['end_date'])) {
            throw ValidationException::withMessages([
                'start_date' => 'Choose both a start date and an end date to export a date range.',
            ]);
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Orders');
        $sheet->fromArray([
            ['Order Number', 'Customer Name', 'Customer Email', 'Total Amount', 'Status', 'Created Date'],
        ], null, 'A1');

        $row = 2;
        Order::query()
            ->when($filters['start_date'] ?? null, fn ($query, $date) => $query->whereDate('created_at', '>=', $date))
            ->when($filters['end_date'] ?? null, fn ($query, $date) => $query->whereDate('created_at', '<=', $date))
            ->orderBy('id')
            ->lazyById(500)
            ->each(function (Order $order) use ($sheet, &$row): void {
                foreach ([
                    'A' => $order->order_number,
                    'B' => $order->customer_name,
                    'C' => $order->customer_email,
                    'E' => $order->status,
                    'F' => $order->created_at->format('Y-m-d H:i:s'),
                ] as $column => $value) {
                    $sheet->setCellValueExplicit($column.$row, (string) $value, DataType::TYPE_STRING);
                }

                $sheet->setCellValue('D'.$row, (float) $order->total_amount);
                $row++;
            });

        $filename = 'orders-'.now()->format('Ymd-His').'.xlsx';
    }
}
