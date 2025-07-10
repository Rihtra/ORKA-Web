<?php

namespace App\Filament\Widgets;

use App\Models\Organisasi;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class OverviewStats extends BaseWidget
{
    protected function getCards(): array
    {
        $user = auth()->user();

        if ($user->role === 'super_admin') {
            return [
                Card::make('Total Organisasi', Organisasi::count()),
                Card::make('Total Anggota', Organisasi::withCount('users')->get()->sum('users_count')),
            ];
        }

        $organisasi = Organisasi::withCount('users')
            ->where('admin_user_id', $user->id)
            ->first();

        return [
            Card::make('Nama Organisasi', $organisasi->nama ?? '-'),
            Card::make('Jumlah Anggota', $organisasi->users_count ?? 0),
            Card::make('Jumlah Divisi', $organisasi?->divisis()->count() ?? 0),
        ];
    }
}
