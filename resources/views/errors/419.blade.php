@extends('errors::minimal')

@section('title', __('Pagina Scaduta'))
@section('code', '419')
@section('message')
    <div style="text-align: center;">
        <p>La sessione è scaduta o la pagina ha impiegato troppo tempo.</p>
        <p>Stiamo ricaricando la pagina, attendi un istante...</p>
        <div style="margin-top: 20px;">
            <button onclick="window.location.reload();"
                style="padding: 10px 20px; background-color: #f59e0b; color: white; border: none; border-radius: 5px; cursor: pointer;">
                Clicca qui per Ricaricare
            </button>
        </div>
        <script>
            // Auto refresh after 2 seconds
            setTimeout(function () {
                window.location.reload();
            }, 2000);
        </script>
    </div>
@endsection