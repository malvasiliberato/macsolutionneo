<?php

namespace App\Http\Controllers\Web\Portal;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class RoadmapController extends Controller
{
    public function __invoke(): Response
    {
        return Inertia::render('Workspace/Roadmap', [
            'boundedContexts' => config('portal.bounded_contexts'),
            'deferredItems' => config('portal.deferred_items'),
        ]);
    }
}
