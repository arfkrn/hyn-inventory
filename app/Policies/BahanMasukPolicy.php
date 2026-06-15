<?php

namespace App\Policies;

use App\Models\BahanMasuk;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BahanMasukPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('kepala gudang');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, BahanMasuk $bahanMasuk): bool
    {
        return $user->hasRole('kepala gudang');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('kepala gudang');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, BahanMasuk $bahanMasuk): bool
    {
        return $user->hasRole('kepala gudang');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, BahanMasuk $bahanMasuk): bool
    {
        return $user->hasRole('kepala gudang');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, BahanMasuk $bahanMasuk): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, BahanMasuk $bahanMasuk): bool
    {
        return false;
    }
}
