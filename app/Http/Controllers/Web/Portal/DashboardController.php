<?php

namespace App\Http\Controllers\Web\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(Request $request): Response
    {
        return Inertia::render('Dashboard', [
            'stats' => [
                [
                    'label' => 'Bounded contexts predisposti',
                    'value' => count(config('portal.bounded_contexts', [])),
                    'description' => 'Contesti mappati a livello di bootstrap, non ancora implementati.',
                ],
                [
                    'label' => 'Navigation items attivi',
                    'value' => count(config('portal.navigation', [])),
                    'description' => 'Shell iniziale del portale guidata da capability backend.',
                ],
                [
                    'label' => 'API foundation',
                    'value' => 'v1',
                    'description' => 'Prima convenzione per endpoint autenticati e mobile readiness.',
                ],
            ],
            'nextSteps' => [
                'Aprire il foundation module Identity + Organization.',
                'Confermare strategia ruoli, permessi e assegnazioni dal legacy.',
                'Stabilire il modello target per capability e ownership.',
            ],
        ]);
    }
}
