<?php
declare(strict_types=1);

namespace App\Http\Controllers\Auth\Traits;

use Illuminate\Support\Facades\Auth;

trait EnsureUniqueLoginSession {

    public function logoutOtherDevices(string $userPassword): void
    {
        Auth::logoutOtherDevices($userPassword);
    }
}
