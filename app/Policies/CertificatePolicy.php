<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Certificate;

class CertificatePolicy
{
    public function view(User $user, Certificate $certificate)
    {
        return $certificate->student && $certificate->student->franchise_id === $user->franchise_id;
    }
}
