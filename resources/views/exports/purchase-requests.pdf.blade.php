<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Exportación de Solicitudes</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #222; }
        h1 { font-size: 18px; margin: 0 0 8px 0; }
        .meta { font-size: 11px; color: #555; margin-bottom: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 6px 8px; }
        th { background: #f5f5f5; text-align: left; }
        tr:nth-child(even) td { background: #fafafa; }
    </style>
</head>
<body>
    <h1>{{ $title ?? 'Exportación' }}</h1>
    <div class="meta">Generado: {{ $generatedAt ?? now() }}</div>

    <table>
        <thead>
            <tr>
                @foreach ($columns as $key => $label)
                    <th>{{ is_string($key) ? $label : $label }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($records as $record)
                <tr>
                    @foreach (array_keys($columns) as $key)
                        <td>
                            @php($value = data_get($record, $key))
                            @if (is_bool($value))
                                {{ $value ? 'Sí' : 'No' }}
                            @elseif ($value instanceof \Carbon\CarbonInterface)
                                {{ $value->format('Y-m-d H:i:s') }}
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
