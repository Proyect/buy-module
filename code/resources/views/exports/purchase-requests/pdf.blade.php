<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>{{ $title ?? 'Exportación' }}</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color: #111; }
        h1 { font-size: 18px; margin: 0 0 10px; }
        .meta { font-size: 11px; color: #666; margin-bottom: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 6px 8px; }
        th { background: #f3f4f6; text-align: left; }
        tr:nth-child(even) { background: #fafafa; }
    </style>
</head>
<body>
    <h1>{{ $title ?? 'Exportación' }}</h1>
    <div class="meta">Generado: {{ $generatedAt ?? now() }}</div>

    <table>
        <thead>
            <tr>
                @foreach ($columns as $col)
                    <th>{{ \Illuminate\Support\Arr::get(\App\Filament\Resources\PurchaseRequests\PurchaseRequestResource::getExportableColumns(), $col, $col) }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($records as $record)
                <tr>
                    @foreach ($columns as $col)
                        <td>
                            @php($value = data_get($record, $col))
                            @if (is_bool($value))
                                {{ $value ? 'Sí' : 'No' }}
                            @elseif (in_array($col, ['total_amount']) && is_numeric($value))
                                ${{ number_format($value, 2, ',', '.') }} ARS
                            @else
                                {{ $value }}
                            @endif
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
