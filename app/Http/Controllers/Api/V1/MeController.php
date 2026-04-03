<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\CurrentUserResource;
use App\Support\Auth\CurrentUserCapabilities;
use App\Support\Auth\ResolveAuthenticatedPortalContext;
use Illuminate\Http\Request;

class MeController extends Controller
{
    public function __invoke(
        Request $request,
        CurrentUserCapabilities $capabilities,
        ResolveAuthenticatedPortalContext $context,
    ): CurrentUserResource
    {
        $resolvedContext = $context->for($request->user(), $request);

        return new CurrentUserResource(
            $request->user(),
            $capabilities->for($request->user(), $resolvedContext),
            $resolvedContext,
        );
    }
}
