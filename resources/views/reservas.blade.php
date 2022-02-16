@extends('layout')

@section('contenido')
<h1>Reservas</h1>

    @isset($error)
    Error: {{ $error  }}
    @endisset

    @isset($reservas)
        <ul>
            @foreach($reservas as $key => $reserva)
                <li>{{ $reserva }}</li>
            @endforeach
        </ul>
    @endisset
@endsection