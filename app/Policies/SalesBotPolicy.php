<?php

namespace App\Policies;

use App\Models\Tenant\SalesBot;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SalesBotPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any sales bots.
     */
    public function viewAny(User $user): bool
    {
        return true; // All authenticated users can view sales bots
    }

    /**
     * Determine whether the user can view the sales bot.
     */
    public function view(User $user, SalesBot $salesBot): bool
    {
        return $user->tenant_id === $salesBot->tenant_id;
    }

    /**
     * Determine whether the user can create sales bots.
     */
    public function create(User $user): bool
    {
        return true; // All authenticated users can create sales bots
    }

    /**
     * Determine whether the user can update the sales bot.
     */
    public function update(User $user, SalesBot $salesBot): bool
    {
        return $user->tenant_id === $salesBot->tenant_id;
    }

    /**
     * Determine whether the user can delete the sales bot.
     */
    public function delete(User $user, SalesBot $salesBot): bool
    {
        return $user->tenant_id === $salesBot->tenant_id;
    }
}
