<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CurrentUserResource extends JsonResource
{
    public function __construct(
        $resource,
        private readonly array $capabilities,
        private readonly array $context = [],
    )
    {
        parent::__construct($resource);
    }

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'is_active' => $this->is_active,
            'account_status' => $this->is_active ? 'active' : 'inactive',
            'last_login_at' => optional($this->last_login_at)?->toIso8601String(),
            'roles' => $this->roles->map(fn ($role) => [
                'code' => $role->code,
                'name' => $role->name,
            ])->values(),
            'capabilities' => $this->capabilities,
            'context' => $this->context,
        ];
    }
}
