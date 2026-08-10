<?php

namespace App\Policies;

use App\Models\Laporan;
use App\Models\User;

class LaporanPolicy
{
    public function viewAny(User $user)
    {
        return $user->hasRole(['admin', 'owner']);
    }

    public function view(User $user, Laporan $laporan)
    {
        if ($user->hasRole('owner')) {
            return in_array($laporan->status, ['terkirim', 'ditinjau']);
        }

        return $user->hasRole('admin');
    }

    public function create(User $user)
    {
        return $user->hasRole('admin');
    }

    public function kirim(User $user, Laporan $laporan)
    {
        return $user->hasRole('admin') && $laporan->status === 'draft';
    }

    public function putuskan(User $user, Laporan $laporan)
    {
        return $user->hasRole('owner') && $laporan->status === 'terkirim';
    }
}