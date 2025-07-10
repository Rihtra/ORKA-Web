<?php

namespace App\Filament\Resources\OrganisasiResource\RelationManagers;

use App\Models\User;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DetachAction;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UsersRelationManager extends RelationManager
{
    protected static string $relationship = 'users';

    public function form(Forms\Form $form): Forms\Form
{
    return $form->schema([
        Forms\Components\Select::make('user_id')
            ->label('Pilih Mahasiswa')
            ->options(
                \App\Models\User::whereNotNull('nim')->pluck('name', 'id')
            )
            ->searchable()
            ->required(),
        Forms\Components\Select::make('role')
            ->label('Peran di Organisasi')
            ->options([
                'Ketua' => 'Ketua',
                'Wakil' => 'Wakil',
                'Bendahara 1' => 'Bendahara 1',
                'Bendahara 2' => 'Bendahara 2',
                'Sekretaris 1' => 'Sekretaris 1',
                'Sekretaris 2' => 'Sekretaris 2',
                'Anggota' => 'Anggota',
            ])
            ->required(),
    ]);
}


    public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('pivot.role'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->form([
                        Select::make('user_id')
    ->label('Pilih Mahasiswa')
    ->searchable()
    ->getSearchResultsUsing(function (string $search) {
        return User::whereNotNull('nim')
            ->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('nim', 'like', "%{$search}%");
            })
            ->limit(10)
            ->get()
            ->mapWithKeys(fn ($user) => [$user->id => "{$user->nim} - {$user->name}"]);
    })
    ->getOptionLabelUsing(fn ($value) => User::find($value)?->nim . ' - ' . User::find($value)?->name)
    ->required(),


                        Select::make('role')
                            ->label('Peran di Organisasi')
                            ->options([
                                'Ketua' => 'Ketua',
                                'Wakil' => 'Wakil',
                                'Bendahara 1' => 'Bendahara 1',
                                'Bendahara 2' => 'Bendahara 2',
                                'Sekretaris 1' => 'Sekretaris 1',
                                'Sekretaris 2' => 'Sekretaris 2',
                                'Anggota' => 'Anggota',
                            ])
                            ->required(),
                    ])
                    ->action(function ($livewire, $data) {
            $livewire->getOwnerRecord() // dapet model Organisasi
                ->users()
                ->attach($data['user_id'], ['role' => $data['role']]);
        })

                    ->successNotificationTitle('Anggota berhasil ditambahkan')
                    ->modalHeading('Tambah Anggota')
                    ->modalSubmitActionLabel('Simpan'),
            ])
            ->actions([
    DetachAction::make()
        ->label('Keluarkan dari Organisasi')
        ->modalHeading('Keluarkan Anggota')
        ->modalSubmitActionLabel('Keluarkan'),
            ]);

    }
    

    public function canViewTable(): bool
{
    return auth()->user()?->role === 'admin_organisasi';
}

public function canCreate(): bool
{
    return auth()->user()?->role === 'admin_organisasi';
}

public function canDelete($record): bool
{
    return auth()->user()?->role === 'admin_organisasi';
}


}
