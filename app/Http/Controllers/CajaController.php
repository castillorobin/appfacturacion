<?php

// app/Http/Controllers/CajaController.php

namespace App\Http\Controllers;

use App\Models\Caja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CajaController extends Controller
{
    public function index()
    {
        $cajas = Caja::with('user')->orderByDesc('id')->paginate(10);
        return view('cajas.index', compact('cajas'));
    }

    public function movimientos(Caja $caja)
{
    $caja->load('movimientos'); // Carga la relación de movimientos
    return view('cajas.movimientos', compact('caja'));
}

public function cerrar(Caja $caja)
{
    // Validación de que la caja pertenece al usuario autenticado
    if ($caja->user_id !== auth()->id()) {
        abort(403);
    }

    // Si ya fue cerrada, no hacer nada
    if ($caja->fecha_cierre) {
        return back()->with('info', 'La caja ya está cerrada.');
    }

    // Calcular total de movimientos (ingresos - egresos)
    $totalMovimientos = $caja->movimientos->reduce(function ($carry, $mov) {
        return $carry + ($mov->tipo === 'ingreso' ? $mov->monto : -$mov->monto);
    }, 0);

    // Guardar fecha y monto final
    $caja->update([
        'fecha_cierre' => now(),
        'monto_final' => $caja->monto_inicial + $totalMovimientos,
        'estado' => 'cerrada',
    ]);

    return redirect()->route('cajas.index')->with('success', 'Caja cerrada exitosamente.');
}

    public function create()
    {
        return view('cajas.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'monto_inicial' => 'required|numeric|min:0',
        ]);

        $userId = Auth::id();

        // Validar que no tenga una caja abierta
        $cajaAbierta = Caja::where('user_id', $userId)
            ->where('estado', 'abierta')
            ->first();

        if ($cajaAbierta) {
            return back()->with('error', 'Ya tienes una caja abierta.');
        }

        Caja::create([
            'user_id' => $userId,
            'fecha_apertura' => now(),
            'monto_inicial' => $request->monto_inicial,
            'estado' => 'abierta',
        ]);

        return redirect()->route('cajas.index')->with('success', 'Caja abierta exitosamente.');
    }
}