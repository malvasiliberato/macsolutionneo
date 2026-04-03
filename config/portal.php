<?php

return [
    'active_membership_session_key' => 'portal.active_membership_id',

    'bounded_contexts' => [
        'Identity',
        'Organization',
        'Catalog',
        'Dealer',
        'Quotes',
        'Practices',
        'Documents',
        'Notifications',
        'Finance',
        'Signature',
    ],

    'base_capabilities' => [
        'portal.access',
        'profile.manage',
    ],

    'role_capability_map' => [
        'platform_admin' => [
            'workspace.roadmap.view',
            'api.me.read',
        ],
    ],

    'membership_role_capability_map' => [
        'workspace_owner' => [
            'organization.context.read',
            'organization.context.manage',
        ],
        'workspace_admin' => [
            'organization.context.read',
        ],
        'owner' => [
            'organization.context.read',
            'organization.context.manage',
        ],
        'member' => [
            'organization.context.read',
        ],
        'dealer_admin' => [
            'organization.context.read',
        ],
        'dealer_seller' => [
            'organization.context.read',
        ],
        'dealer_operator_member' => [
            'organization.context.read',
        ],
    ],

    'assignment_role_capability_map' => [
        'dealer_operator' => [
            'dealer.assignment.read',
        ],
        'dealer_account_manager' => [
            'dealer.assignment.read',
        ],
        'dealer_commercial' => [
            'dealer.assignment.read',
        ],
    ],

    'context_capability_map' => [
        'has_active_organization' => [
            'organization.context.active',
        ],
        'has_assignments' => [
            'dealer.assignment.active',
        ],
        'can_switch_context' => [
            'organization.context.switch',
        ],
    ],

    'navigation' => [
        [
            'key' => 'dashboard',
            'label' => 'Dashboard',
            'route' => 'dashboard',
            'description' => 'Stato del bootstrap e focus corrente.',
            'required_capabilities' => ['portal.access'],
            'state' => 'available',
            'scope' => 'global',
        ],
        [
            'key' => 'workspace-roadmap',
            'label' => 'Workspace Roadmap',
            'route' => 'workspace.roadmap',
            'description' => 'Contesti predisposti e aree volutamente rimandate.',
            'required_capabilities' => ['workspace.roadmap.view', 'organization.context.active'],
            'state' => 'available',
            'scope' => 'contextual',
            'requires_active_organization' => true,
        ],
        [
            'key' => 'profile',
            'label' => 'Profilo',
            'route' => 'profile.edit',
            'description' => 'Profilo utente e impostazioni di accesso.',
            'required_capabilities' => ['profile.manage'],
            'state' => 'available',
            'scope' => 'global',
        ],
    ],

    'deferred_items' => [
        'Organization model finale e relativi boundary.',
        'ACL di dominio completa con permessi granulari.',
        'Catalog foundation completo.',
        'Preventivi, pratiche, documenti, finanziamenti e firma.',
        'Sidebar capability-based definitiva derivata dal dominio consolidato.',
    ],
];
