<?php

namespace App\Application\Portal\Navigation;

class BuildPortalNavigation
{
    public function for(array $capabilities, array $context = []): array
    {
        $navigation = config('portal.navigation', []);

        return array_values(array_filter(array_map(function (array $item) use ($capabilities, $context) {
            $requiredCapabilities = $item['required_capabilities'] ?? [];
            $requiredCapability = $item['required_capability'] ?? null;

            if ($requiredCapability !== null) {
                $requiredCapabilities[] = $requiredCapability;
            }

            $requiredCapabilities = array_values(array_unique(array_filter($requiredCapabilities)));

            if ($requiredCapabilities !== [] && array_diff($requiredCapabilities, $capabilities) !== []) {
                return null;
            }

            if (($item['requires_active_organization'] ?? false) && empty($context['active_organization'])) {
                return null;
            }

            if (($item['requires_context_switching'] ?? false) && ! data_get($context, 'context_switching.can_switch', false)) {
                return null;
            }

            $scope = $item['scope'] ?? 'global';

            return [
                'key' => $item['key'],
                'label' => $item['label'],
                'route' => $item['route'],
                'description' => $item['description'] ?? null,
                'state' => $item['state'] ?? 'available',
                'scope' => $scope,
                'context_label' => $scope === 'contextual' ? data_get($context, 'active_scope.label') : null,
                'required_capabilities' => $requiredCapabilities,
            ];
        }, $navigation)));
    }
}
