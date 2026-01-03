<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <title>Ordine Fornitore {{ $order->number }}</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 14px;
            color: #333;
        }

        .header {
            width: 100%;
            border-bottom: 2px solid #ddd;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .logo {
            max-height: 80px;
        }

        .company-details {
            float: right;
            text-align: right;
            font-size: 12px;
        }

        .supplier-details {
            margin-bottom: 30px;
            padding: 15px;
            background-color: #f9f9f9;
            border: 1px solid #eee;
        }

        .order-meta {
            margin-bottom: 20px;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .items-table th,
        .items-table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        .items-table th {
            background-color: #f4f4f4;
        }

        .total-section {
            text-align: right;
            font-size: 16px;
            font-weight: bold;
        }

        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 10px;
            color: #888;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }

        .attachments {
            margin-top: 30px;
            page-break-before: always;
        }

        .attachment-img {
            max-width: 100%;
            max-height: 800px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            padding: 5px;
        }
    </style>
</head>

<body>

    <div class="header">
        <table width="100%">
            <tr>
                <td valign="top">
                    @if($company->logo)
                        <img src="{{ public_path('storage/' . $company->logo) }}" class="logo" alt="Logo">
                    @else
                        <h1>{{ $company->name }}</h1>
                    @endif
                </td>
                <td valign="top" class="company-details">
                    <strong>{{ $company->name }}</strong><br>
                    {{ $company->address }}<br>
                    {{ $company->city }} ({{ $company->province }})<br>
                    P.IVA: {{ $company->vat_number }}<br>
                    Email: {{ $company->email }}
                </td>
            </tr>
        </table>
    </div>

    <div class="supplier-details">
        <strong>Spett.le Fornitore:</strong><br>
        {{ $order->supplier->name }}<br>
        {{ $order->supplier->email }}<br>
        {{ $order->supplier->phone }}
    </div>

    <div class="order-meta">
        <h2>Ordine Acquisto N. {{ $order->number }}</h2>
        <p>Data: {{ $order->date->format('d/m/Y') }}</p>
        @if($order->estimate)
            <p>Rif. Nostro Preventivo: {{ $order->estimate->number }}</p>
        @endif
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th>Prodotto / Materiale</th>
                <th width="100">Qta</th>
                <th>Note</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
                <tr>
                    <td>
                        <strong>{{ $item->material ? $item->material->name : $item->name }}</strong>
                        @if($item->name && $item->material) <br><small>{{ $item->name }}</small> @endif
                    </td>
                    <td>{{ $item->quantity }}</td>
                    <td>
                        {{-- Future notes column if needed --}}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total-section">
        {{-- Only show total if needed, usually suppliers send their own invoice --}}
        {{-- Totale Presunto: € {{ number_format($order->total_amount, 2, ',', '.') }} --}}
    </div>

    @if($order->notes)
        <div style="margin-top: 20px; padding: 10px; background: #fffde7; border: 1px solid #fdd835;">
            <strong>Note:</strong><br>
            {!! nl2br(e($order->notes)) !!}
        </div>
    @endif

    {{-- Attachments Section --}}
    @if(!empty($order->attachments))
        <div class="attachments">
            <h3>Allegati Grafici</h3>
            @foreach($order->attachments as $attachment)
                @php
                    $extension = pathinfo($attachment, PATHINFO_EXTENSION);
                @endphp

                @if(in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'webp']))
                    <div style="text-align: center; margin-bottom: 30px;">
                        <img src="{{ public_path('storage/' . $attachment) }}" class="attachment-img">
                        <p><small>{{ basename($attachment) }}</small></p>
                    </div>
                @endif
            @endforeach
        </div>
    @endif

    <div class="footer">
        {{ $company->name }} - Pagina generata il {{ now()->format('d/m/Y H:i') }}
    </div>

</body>

</html>