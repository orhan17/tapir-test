<?php

namespace App\Http\Controllers;

use App\Models\Car;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function index(Request $request): \Illuminate\Http\JsonResponse
    {
        $query = Car::query();

        if ($request->has('year_from')) {
            $query->where('year', '>=', $request->get('year_from'));
        }
        if ($request->has('year_to')) {
            $query->where('year', '<=', $request->get('year_to'));
        }
        if ($request->has('price_less')) {
            $query->where('price', '<=', $request->get('price_less'));
        }
        if ($request->has('brand')) {
            $query->where('brand', $request->get('brand'));
        }
        if ($request->has('model')) {
            $query->where('model', $request->get('model'));
        }

        $cars = $query->paginate(10);

        return response()->json($cars);
    }
}
