<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preventivo {{ $record->number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            color: #333;
        }

        .header {
            margin-bottom: 30px;
        }

        .client-info {
            float: right;
            text-align: right;
        }

        .company-info {
            float: left;
        }

        .clear {
            clear: both;
        }

        .title {
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            margin: 20px 0;
            text-transform: uppercase;
        }

        .public-notes {
            margin-bottom: 20px;
            border: 1px solid #ddd;
            padding: 10px;
            background-color: #f9f9f9;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
        }

        th {
            background-color: #eee;
        }

        .text-left {
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        .totals {
            width: 40%;
            float: right;
        }

        .totals td {
            border: none;
            padding: 5px;
        }

        .total-row {
            font-weight: bold;
            font-size: 16px;
        }

        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #777;
        }
    </style>
</head>

<body>
    @php
        $company = \App\Models\Company::first();
    @endphp

    <div class="header">
        <div class="company-info">
            @if($company && $company->logo_path)
                <img src="{{ public_path('storage/' . $company->logo_path) }}" alt="Logo"
                    style="max-height: 80px; margin-bottom: 10px;"><br>
            @elseif($company)
                <strong>{{ $company->name }}</strong><br>
            @else
                <strong>Tua Azienda Serramenti</strong><br>
            @endif

            @if($company)
                {{ $company->address }}<br>
                {{ $company->city }} {{ $company->cap }}<br>
                P.IVA: {{ $company->p_iva }}<br>
                Email: {{ $company->email }} | Tel: {{ $company->phone }}
            @else
                Via Esempio 123, 00100 Roma<br>
                P.IVA: 12345678901<br>
                Email: info@serramenti.it
            @endif
        </div>
        <div class="client-info">
            <strong>Spett.le Cliente</strong><br>
            {{ $record->client->name }}<br>
            {{ $record->client->address ?? '' }}<br>
            {{ $record->client->city ?? '' }} {{ $record->client->cap ?? '' }}<br>
            Partita IVA: {{ $record->client->vat_number ?? '-' }}
        </div>
        <div class="clear"></div>
    </div>

    <div class="title">
        Preventivo N. {{ $record->number }} del {{ $record->date->format('d/m/Y') }}
    </div>

    @if($record->public_notes)
        <div class="public-notes">
            <strong>Note Generali:</strong><br>
            {!! nl2br(e($record->public_notes)) !!}
        </div>
    @endif

    <table>
        <thead>
            <tr>
                <th>Pos</th>
                <th>Pz</th>
                <th>Base</th>
                <th>Altezza</th>
                <th>Descrizione</th>
                <th>Prezzo</th>
                <th>Totale</th>
            </tr>
        </thead>
        <tbody>
            @foreach($record->items as $index => $item)
                <tr>
                    <td>{{ chr(65 + $index) }}</td> <!-- A, B, C... -->
                    <td>{{ $item->quantity }}</td>
                    <td>{{ $item->width }} mm</td>
                    <td>{{ $item->height }} mm</td>
                    <td class="text-left">
                        <strong>{{ $item->name ?: $item->product_type }}</strong><br>
                        <small>
                            @foreach($item->attributes as $key => $value)
                                {{ $key }}: {{ $value }}<br>
                            @endforeach
                        </small>
                    </td>
                    <td class="text-right">€ {{ number_format($item->unit_price, 2, ',', '.') }}</td>
                    <td class="text-right">€ {{ number_format($item->total_price, 2, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <table>
            <tr>
                <td class="text-right">Totale Materiale:</td>
                <td class="text-right">€ {{ number_format($record->subtotal, 2, ',', '.') }}</td>
            </tr>
            @if($record->installation_amount > 0)
                <tr>
                    <td class="text-right">Totale Posa:</td>
                    <td class="text-right">€ {{ number_format($record->installation_amount, 2, ',', '.') }}</td>
                </tr>
            @endif
            <tr>
                <td class="text-right">Imponibile:</td>
                <td class="text-right">€ {{ number_format($record->total - $record->tax_amount, 2, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="text-right">IVA (22%):</td>
                <td class="text-right">€ {{ number_format($record->tax_amount, 2, ',', '.') }}</td>
            </tr>
            <tr class="total-row">
                <td class="text-right">Totale (Iva incl.):</td>
                <td class="text-right">€ {{ number_format($record->total, 2, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <div class="clear"></div>

    <div class="footer">
        Offerta valida fino al {{ $record->valid_until ? $record->valid_until->format('d/m/Y') : '-' }}<br>
        @if($company && $company->iban)
            IBAN: {{ $company->iban }}<br>
        @endif
        @if($company && $company->footer_notes)
            <br>{!! nl2br(e($company->footer_notes)) !!}
        @endif
    </div>

</body>

</html>