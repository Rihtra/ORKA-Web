<?php

namespace App\Filament\Resources\DivisiResource\Pages;

use App\Filament\Resources\DivisiResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateDivisi extends CreateRecord
{
    protected static string $resource = DivisiResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
{
    if (auth()->user()->role === 'admin_organisasi') {
        $data['organisasi_id'] = auth()->user()->organisasi->id; // pastikan relasi ada
    }

    return $data;
}

}
