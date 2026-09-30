<?php

namespace App\Policies;

use App\Models\Donation;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DonationPolicy
{
    use HandlesAuthorization;

    public function view(User $user, Donation $donation)
    {
        return $user->id === $donation->user_id;
    }

    public function update(User $user, Donation $donation)
    {
        return $user->id === $donation->user_id;
    }

    public function delete(User $user, Donation $donation)
    {
        return $user->id === $donation->user_id;
    }
}