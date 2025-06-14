<?php

namespace App\Filament\Resources;

use App\Models\Pendaftaran;
use App\Models\Divisi;
use App\Models\Organisasi;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\PendaftaranResource\Pages;

class PendaftaranResource extends Resource
{
    protected static ?string $model = Pendaftaran::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-plus';
    protected static ?string $navigationLabel = 'Pendaftaran Mahasiswa';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('user_id')
                ->label('Mahasiswa')
                ->relationship('user', 'name')
                ->searchable()
                ->preload()
                ->required(),

            Forms\Components\Select::make('organisasi_id')
                ->label('Organisasi')
                ->relationship('organisasi', 'nama')
                ->searchable()
                ->preload()
                ->required(),

            Forms\Components\Select::make('divisi_id')
                ->label('Divisi')
                ->relationship('divisi', 'nama')
                ->searchable()
                ->preload()
                ->required(),

            Forms\Components\Textarea::make('alasan')
                ->required()
                ->label('Alasan Bergabung'),

            Forms\Components\FileUpload::make('cv')
                ->label('Upload CV')
                ->disk('public')
                ->directory('cv')
                ->required(),

            Forms\Components\Select::make('status')
                ->options([
                    'pending' => 'Pending',
                    'diterima' => 'Diterima',
                    'ditolak' => 'Ditolak',
                ])
                ->default('pending')
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')->label('Mahasiswa'),
                Tables\Columns\TextColumn::make('organisasi.nama')->label('Organisasi'),
                Tables\Columns\TextColumn::make('divisi.nama')->label('Divisi'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'gray',
                        'diterima' => 'success',
                        'ditolak' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('created_at')->label('Didaftar')->dateTime(),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
     public static function canCreate(): bool
    {
        
        return false;
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPendaftarans::route('/'),
            'create' => Pages\CreatePendaftaran::route('/create'),
            'edit' => Pages\EditPendaftaran::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        if ($user->role === 'admin_organisasi') {
            return $query->whereHas('organisasi', function ($q) use ($user) {
                $q->where('admin_user_id', $user->id);
            });
        }

        return $query; // super_admin bisa lihat semua
    }
}
