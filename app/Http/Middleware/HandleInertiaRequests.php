<?php

namespace App\Http\Middleware;

use App\Application\Portal\Navigation\BuildPortalNavigation;
use App\Support\Auth\CurrentUserCapabilities;
use App\Support\Auth\ResolveAuthenticatedPortalContext;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    public function __construct(
        private readonly CurrentUserCapabilities $capabilities,
        private readonly ResolveAuthenticatedPortalContext $context,
        private readonly BuildPortalNavigation $navigation,
    ) {
    }

    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): string|null
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();
        $context = $user ? $this->context->for($user, $request) : [];
        $capabilities = $user ? $this->capabilities->for($user, $context) : [];

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $user ? [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'is_active' => $user->is_active,
                    'account_status' => $user->is_active ? 'active' : 'inactive',
                    'last_login_at' => optional($user->last_login_at)?->toIso8601String(),
                    'role_codes' => $user->roles()->pluck('code')->values(),
                ] : null,
                'capabilities' => $capabilities,
                'context' => $context,
            ],
            'portal' => [
                'name' => config('app.name'),
                'boundedContexts' => config('portal.bounded_contexts'),
                'navigation' => $user ? $this->navigation->for($capabilities, $context) : [],
            ],
        ];
    }
}
