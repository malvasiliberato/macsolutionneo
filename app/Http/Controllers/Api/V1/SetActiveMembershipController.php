<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\CurrentUserResource;
use App\Models\OrganizationMembership;
use App\Support\Auth\CurrentUserCapabilities;
use App\Support\Auth\ResolveAuthenticatedPortalContext;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class SetActiveMembershipController extends Controller
{
    public function __invoke(
        Request $request,
        CurrentUserCapabilities $capabilities,
        ResolveAuthenticatedPortalContext $context,
    ): CurrentUserResource {
        $payload = $request->validate([
            'membership_id' => ['required', 'integer'],
        ]);

        $membership = OrganizationMembership::query()
            ->where('user_id', $request->user()->id)
            ->where('status', 'active')
            ->find($payload['membership_id']);

        if (! $membership) {
            throw ValidationException::withMessages([
                'membership_id' => 'The selected membership is not available for the current user.',
            ]);
        }

        $request->session()->put(
            config('portal.active_membership_session_key', 'portal.active_membership_id'),
            $membership->id,
        );

        $resolvedContext = $context->for($request->user(), $request);

        return new CurrentUserResource(
            $request->user(),
            $capabilities->for($request->user(), $resolvedContext),
            $resolvedContext,
        );
    }
}
