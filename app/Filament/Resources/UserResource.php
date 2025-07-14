<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\UserResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\UserResource\RelationManagers;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Kelola Admin';
    protected static ?string $pluralModelLabel = 'Kelola Admin';

   // app/Filament/Resources/UserResource.php
public static function form(Form $form): Form
{
    return $form->schema([
        TextInput::make('name')->required(),
        TextInput::make('email')->email()->required(),
        Select::make('role')
            ->options([
                'admin_organisasi' => 'Admin Organisasi',
            ])
            ->default('admin_organisasi')
            ->required(),
        Select::make('jurusan_id')
    ->relationship('jurusan', 'nama')
    ->label('Jurusan')
    ->nullable() // tambahkan ini supaya bisa kosong
    ->hint('Kosongkan jika organisasi bersifat umum '),

        TextInput::make('nim')->visible(fn ($get) => $get('role') === 'mahasiswa'),
        TextInput::make('no_hp')->tel(),
        TextInput::make('password')
            ->password()
            ->dehydrateStateUsing(fn ($state) => bcrypt($state))
            ->dehydrated(fn ($state) => filled($state))
            ->required(fn (string $context): bool => $context === 'create'),
    ]);
}


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nama')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('email')->sortable(),
            Tables\Columns\TextColumn::make('role')->label('Peran')->badge(),
            Tables\Columns\TextColumn::make('jurusan.nama')->label('Jurusan')->sortable(),
            Tables\Columns\TextColumn::make('nim')->label('NIM')->toggleable(isToggledHiddenByDefault: true),
            Tables\Columns\TextColumn::make('no_hp')->label('No. HP')->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
    public static function shouldRegisterNavigation(): bool
{
    return auth()->user()?->role === 'super_admin';
}
public static function getEloquentQuery(): Builder
{
    return parent::getEloquentQuery()
        ->where('role', 'admin_organisasi');
        // ->whereHas('organisasi'); 
}
public static function getNavigationLabel(): string
{
    return 'Admin Organisasi'; // ganti sesuai kebutuhan
}



}
