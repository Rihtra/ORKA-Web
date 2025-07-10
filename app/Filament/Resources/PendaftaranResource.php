<?php

namespace App\Filament\Resources;

use App\Models\Pendaftaran;
use App\Models\Divisi;
use App\Models\Organisasi;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\Filament\Resources\PendaftaranResource\Pages;

class PendaftaranResource extends Resource
{
    protected static ?string $model = Pendaftaran::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-plus';
    protected static ?string $navigationLabel = 'Pendaftaran Mahasiswa';

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('nama')->disabled(),
            TextInput::make('nim')->disabled(),
            TextInput::make('prodi')->disabled(),
            TextInput::make('semester')->disabled(),
            TextInput::make('nomor_wa')->disabled(),
            Textarea::make('alasan')->label('Alasan Bergabung')->disabled(),
            Select::make('divisi_id')
                ->label('Divisi yang Dipilih')
                ->relationship('divisi', 'nama')
                ->disabled(),

            FileUpload::make('cv')
                ->label('CV / Foto Mahasiswa')
                ->image()
                ->directory('cv')
                ->visibility('public')
                ->previewable(true)
                ->downloadable()
                ->disabled(),

            Select::make('status')
                ->options([
                    'pending' => 'Pending',
                    'diterima' => 'Diterima',
                    'ditolak' => 'Ditolak',
                ])
                ->disabled(),

            DateTimePicker::make('jadwal_wawancara')
                ->label('Jadwal Wawancara')
                ->visible(fn ($record) => $record?->status === 'diterima')
                ->required(fn ($record) => $record?->status === 'diterima')
                ->disabled(fn ($record) => $record?->status !== 'diterima'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama'),
                TextColumn::make('nim'),
                TextColumn::make('prodi'),
                TextColumn::make('semester'),
                TextColumn::make('status')->badge()->color(fn ($state) => match ($state) {
                    'diterima' => 'success',
                    'ditolak' => 'danger',
                    'pending' => 'gray',
                }),
                TextColumn::make('divisi.nama')
                    ->label('Divisi')
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options([
                    'pending' => 'Pending',
                    'diterima' => 'Diterima',
                    'ditolak' => 'Ditolak',
                ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),

                Tables\Actions\Action::make('Tolak')
                    ->color('danger')
                    ->icon('heroicon-m-x-circle')
                    ->requiresConfirmation()
                    ->action(fn (Model $record) => $record->update(['status' => 'ditolak']))
                    ->visible(fn (Model $record) => $record->status === 'menunggu' || $record->status === 'pending'),

                Tables\Actions\Action::make('Terima')
                    ->label('Terima & Atur Jadwal')
                    ->color('success')
                    ->icon('heroicon-m-check')
                    ->form([
                        DateTimePicker::make('jadwal_wawancara')
                            ->label('Jadwal Wawancara')
                            ->required(),
                    ])
                    ->action(function (Model $record, array $data) {
                        $record->update([
                            'status' => 'diterima',
                            'jadwal_wawancara' => $data['jadwal_wawancara'],
                        ]);

                        // ✅ Tambahkan ke organisasi sebagai anggota jika belum ada
                        if (! $record->user->organisasis->contains($record->organisasi_id)) {
                            $record->user->organisasis()->attach($record->organisasi_id, [
                                'role' => 'Anggota',
                            ]);
                        }
                    })
                    ->modalHeading('Konfirmasi Penerimaan & Atur Jadwal')
                    ->requiresConfirmation()
                    ->visible(fn (Model $record) => $record->status === 'pending' || $record->status === 'menunggu'),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
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
            'view' => Pages\ViewPendaftaran::route('/{record}'),
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

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->role === 'admin_organisasi';
    }
}
