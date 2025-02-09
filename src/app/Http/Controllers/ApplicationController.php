<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Car;
use App\Services\CRMService;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    public function store(Request $request, CRMService $crmService)
    {
        // Валидация
        $data = $request->validate([
            'phone' => 'required',
            'car_id' => 'required|exists:cars,id',
        ]);

        // Создание заявки
        $application = Application::create([
            'car_id' => $data['car_id'],
            'phone'  => $data['phone'],
            'status' => 'new',
        ]);

        // Отправка почты (но по условию используем mailhog/log – настройки в .env)
        // Mail::to('manager@company.com')->send(new NewApplicationMail($application));
        // В демо-проекте можно просто пропустить/логировать

        // Отправка в CRM (с повторными попытками)
        $crmService->sendApplicationToCRM($application);

        return response()->json([
            'message' => 'Application created',
            'application_id' => $application->id,
        ], 201);
    }
}
