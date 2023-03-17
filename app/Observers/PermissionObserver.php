<?php

namespace App\Observers;

use App\Models\Permission;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;

class PermissionObserver
{
    /**
     * Handle the Permission "created" event.
     */
    public function created(Permission $permission): void
    {
        Gate::define($permission->name, function ($user) use ($permission) {
            return $user->hasPermission($permission->name);
        });
    }

    /**
     * Handle the Permission "updated" event.
     */
    public function updated(Permission $permission) :void
    {
        Gate::define($permission->name, function ($user) use ($permission) {
            return $user->hasPermission($permission->name);
        });
    }

    /**
     * Handle the Permission "deleted" event.
     */
    public function deleted(Permission $permission): void
    {

    }

    /**
     * Handle the Permission "restored" event.
     */
    public function restored(Permission $permission): void
    {
        //
    }

    /**
     * Handle the Permission "force deleted" event.
     */
    public function forceDeleted(Permission $permission): void
    {
        //
    }
}
