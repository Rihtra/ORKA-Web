<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrganisasiResource\RelationManagers\UsersRelationManager;
use App\Filament\Resources\OrganisasiResource\RelationManagers\UsersRelationManagerRelationManager;
use App\Models\User;
use Filament\Forms\Get;
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
use Filament\Forms\Components\Repeater;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\OrganisasiResource\Pages;
use App\Filament\Resources\OrganisasiResource\RelationManagers;

class OrganisasiResource extends Resource
{
    protected static ?string $model = Organisasi::class;
    protected static ?string $navigationLabel = 'Kelola Organisasi';
    protected static ?string $pluralModelLabel = 'Kelola Organisasi';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        $user = auth()->user();

        if ($user->role === 'super_admin') {
            return $form->schema([
                TextInput::make('nama')->required(),
                Select::make('tipe')
                    ->label('Tipe Organisasi')
                    ->options([
                        'ukm' => 'Umum',
                        'himpunan' => 'Khusus (Sesuai Jurusan)',
                    ])
                    ->required()
                    ->reactive(),
                Select::make('admin_user_id')
                    ->label('Admin Organisasi')
                    ->options(function (Get $get) {
                        $usedAdminIds = Organisasi::query()
                            ->when($get('id'), fn ($query, $id) => $query->where('id', '!=', $id))
                            ->pluck('admin_user_id')
                            ->filter()
                            ->toArray();

                        return User::where('role', 'admin_organisasi')
                            ->whereNotIn('id', $usedAdminIds)
                            ->pluck('name', 'id');
                    })
                    ->searchable()
                    ->required()
                    ->reactive()
                    ->afterStateHydrated(function ($component, $state) {
                        $admin = User::find($state);
                        if ($admin) {
                            $component->options([$admin->id => $admin->name] + $component->getOptions());
                        }
                    }),
                Select::make('jurusan_id')
                    ->label('Jurusan')
                    ->relationship('jurusan', 'nama')
                    ->nullable()
                    ->visible(fn (Get $get) => $get('tipe') === 'himpunan')
                    ->required(fn (Get $get) => $get('tipe') === 'himpunan'),
            ]);
        }

        // Form buat admin_organisasi, fokus ke kelola anggota
        if ($user->role === 'admin_organisasi') {
            return $form->schema([
                FileUpload::make('logo'),
                Textarea::make('deskripsi'),
                Textarea::make('visi'),
                Textarea::make('misi'),
                Textarea::make('syarat'),
               
                  
                            
                     
            ]);
        }

        // Default (kalau role lain), cuma lihat aja
        return $form->schema([
            FileUpload::make('logo')->disabled(),
            Textarea::make('deskripsi')->disabled(),
            Textarea::make('visi')->disabled(),
            Textarea::make('misi')->disabled(),
            Textarea::make('syarat')->disabled(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama')->label('Nama Organisasi')->searchable(),
                Tables\Columns\TextColumn::make('jurusan.nama')->label('Jurusan')->sortable(),
                Tables\Columns\TextColumn::make('adminUser.name')->label('Admin'),
                Tables\Columns\TextColumn::make('users_count')->counts('users')->label('Jumlah Anggota'),
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
            UsersRelationManager::class,
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
public static function canCreate(): bool
{
    return auth()->user()?->role === 'super_admin';
}


}