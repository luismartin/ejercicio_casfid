<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use Illuminate\Http\Request;

class ReservasController extends Controller
{
    public function index()
    {
        try {
            $reservas = Reserva::calcular();
            return view('reservas', ['reservas' => $reservas]);
        } catch (\Throwable $th) {
            return view('reservas', ['error' => $th->getMessage()]);
        }
    }
}
