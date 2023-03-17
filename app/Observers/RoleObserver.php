<?php

namespace App\Observers;

use App\Models\Role;
use Illuminate\Support\Facades\Gate;

class RoleObserver
{
    /**
     * Handle the Role "created" event.
     */
    public function created(Role $role): void
    {
        Gate::define($role->name, function ($user) use ($role) {
            return $user->hasRole($role->name);
        });
    }

    /**
     * Handle the Role "updated" event.
     */
    public function updated(Role $role): void
    {
        Gate::define($role->name, function ($user) use ($role) {
            return $user->hasRole($role->name);
        });
    }

    /**
     * Handle the Role "deleted" event.
     */
    public function deleted(Role $role): void
    {
        //
    }

    /**
     * Handle the Role "restored" event.
     */
    public function restored(Role $role): void
    {
        //
    }

    /**
     * Handle the Role "force deleted" event.
     */
    public function forceDeleted(Role $role): void
    {
        //
    }
}
