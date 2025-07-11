<?php

namespace App\Filament\Resources\OrganisasiResource\Pages;

use App\Filament\Resources\OrganisasiResource;
use Filament\Resources\Pages\Page;

class ManageAnggota extends Page
{
    protected static string $resource = OrganisasiResource::class;

    protected static string $view = 'filament.resources.organisasi-resource.pages.manage-anggota';
}
