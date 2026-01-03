<!DOCTYPE html>
<html>

<head>
    <title>Nuovo Ordine</title>
</head>

<body>
    <p>Gentile Fornitore,</p>
    <p>In allegato trova il nostro ordine N. <strong>{{ $order->number }}</strong>.</p>
    <p>Restiamo in attesa di vostra conferma.</p>
    <br>
    <p>Cordiali Saluti,</p>
    <p>{{ config('app.name') }}</p>
</body>

</html>