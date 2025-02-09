<?php

namespace App\Orchid\Screens;

use App\Models\CrmFailure;
use Orchid\Screen\Screen;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\TD;
use Orchid\Screen\Actions\Link;
use Orchid\Support\Facades\Layout;
use App\Services\CRMService;

class CrmFailureListScreen extends Screen
{
    public $name = 'Неудачные отправки в CRM';

    public function query(): array
    {
        return [
            'failures' => CrmFailure::where('resolved', false)->paginate(10),
        ];
    }

    public function commandBar(): array
    {
        return [];
    }

    public function layout(): array
    {
        return [
            Layout::table('failures', [
                TD::make('id', 'ID'),
                TD::make('application_id', 'Заявка'),
                TD::make('attempts', 'Попыток'),
                TD::make('last_attempt_at', 'Последняя попытка'),
                TD::make('error_message', 'Ошибка'),
                TD::make('Действие')->render(function (CrmFailure $failure) {
                    return Link::make('Повторить')
                        ->route('platform.crm-failure.resend', $failure->id);
                }),
            ])
        ];
    }

    // Обработчик переотправки
    public function resend(CRMService $crmService, $id)
    {
        $failure = CrmFailure::findOrFail($id);
        $application = $failure->application;

        $crmService->sendApplicationToCRM($application);

        return redirect()->route('platform.crm-failures');
    }
}
