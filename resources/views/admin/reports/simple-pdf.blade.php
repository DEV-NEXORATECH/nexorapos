<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #0f172a; font-size: 12px; }
        h1 { color: #0057b8; font-size: 20px; margin-bottom: 6px; }
        .meta { color: #64748b; margin-bottom: 18px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #cfe2ff; padding: 8px; text-align: left; }
        th { background: #eaf4ff; color: #0057b8; }
    </style>
</head>
<body>
    <h1>{{ $title }}</h1>
    <div class="meta">Generated at {{ now()->format('Y-m-d H:i:s') }}</div>

    <table>
        <thead>
            <tr>
                @foreach(array_keys($rows[0] ?? ['empty' => '']) as $column)
                    <th>{{ ucwords(str_replace('_', ' ', $column)) }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $row)
                <tr>
                    @foreach($row as $value)
                        <td>{{ $value }}</td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td>No data</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
