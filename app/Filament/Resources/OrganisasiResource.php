<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Forms\Components\RichEditor;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\Organisasi;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\OrganisasiResource\Pages;
use App\Filament\Resources\OrganisasiResource\RelationManagers;

class OrganisasiResource extends Resource
{
    protected static ?string $model = Organisasi::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
{
     $user = auth()->user();
   if ($user->role === 'super_admin') {
        return $form->schema([
            TextInput::make('nama'),
            Select::make('jurusan_id')->relationship('jurusan', 'nama'),
            Select::make('admin_user_id')->relationship('adminUser', 'name'),
        ]);
    }

    return $form->schema([
        FileUpload::make('logo'),
        Textarea::make('visi'),
        Textarea::make('misi'),
        RichEditor::make('syarat'),
    ]);
}


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama')->label('Nama Organisasi')->searchable(),
            Tables\Columns\TextColumn::make('jurusan.nama')->label('Jurusan')->sortable(),
            Tables\Columns\TextColumn::make('adminUser.name')->label('Admin'),
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
            'index' => Pages\ListOrganisasis::route('/'),
            'create' => Pages\CreateOrganisasi::route('/create'),
            'edit' => Pages\EditOrganisasi::route('/{record}/edit'),
        ];
    }
    public static function getEloquentQuery(): Builder
{
    $query = parent::getEloquentQuery();
    $user = auth()->user();

    if ($user->role === 'admin_organisasi') {
        return $query->where('admin_user_id', $user->id);
    }

    return $query; // super_admin bisa lihat semua
}

}
