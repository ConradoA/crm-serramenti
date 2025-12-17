<!DOCTYPE html>
<html>

<head>
    <title>Preventivo {{ $estimate->number }}</title>
</head>

<body>
    @php
        $company = \App\Models\Company::first();
    @endphp

    <p>Gentile {{ $estimate->client->name }},</p>
    <p>In allegato trova il preventivo N. {{ $estimate->number }} relativo alla Vostra richiesta.</p>
    <p>Restiamo a disposizione per qualsiasi chiarimento.</p>
    <br>
    <p>Cordiali Saluti,</p>
    <p><strong>{{ $company ? $company->name : 'Tua Azienda Serramenti' }}</strong></p>
</body>

</html>