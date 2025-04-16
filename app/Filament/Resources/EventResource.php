<?php

namespace App\Filament\Resources;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use App\Models\Event;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Helpers\NotificationHelper;
use Illuminate\Support\Facades\Log;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TimePicker;
use Filament\Tables\Columns\ToggleColumn;
use Illuminate\Database\Eloquent\Builder;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use Illuminate\Validation\ValidationException;
use App\Filament\Resources\EventResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use App\Filament\Resources\EventResource\RelationManagers;

class EventResource extends Resource
{
    protected static ?string $model = Event::class;

    protected static ?string $navigationIcon = 'heroicon-m-calendar-date-range';

    protected static ?string $pluralLabel = 'Kegiatan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),

                TextInput::make('phone_number')
                    ->label('Nomor WhatsApp')
                    ->tel()
                    ->required()
                    ->helperText('Masukkan nomor tanpa awalan 0 atau +62. Contoh: 81234567890')
                    ->rules([
                        'required',
                        'regex:/^[1-9][0-9]{7,14}$/', // angka, minimal 8 digit, maksimal 15
                    ])
                    ->maxLength(15),


                Select::make('room_id')
                    ->relationship('room', 'name', fn(Builder $query) => $query->where('status', true)) // pastikan relasi `room()` ada di model Event
                    ->searchable()
                    ->required(),

                Select::make('asal_bidang')
                    ->required()
                    ->options([
                        'SEKRETARIAT' => 'SEKRETARIAT',
                        'RENDALEV' => 'RENDALEV',
                        'IKA' => 'IKA',
                        'PPM' => 'PPM',
                        'PSDA' => 'PSDA',
                        'LITBANG' => 'LITBANG',
                        'LAINNYA' => 'LAINNYA',
                    ]),

                DatePicker::make('date')
                    ->required(),

                TimePicker::make('start_time')
                    ->required(),

                TimePicker::make('finish_time')
                    ->required(),

                TextInput::make('guest_count')
                    ->numeric()
                    ->minValue(0)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->sortable(),
                TextColumn::make('room.name')->label('Room')->sortable(),
                TextColumn::make('asal_bidang')->sortable(),
                TextColumn::make('date')->date()->sortable(),
                TextColumn::make('start_time')->time()->sortable(),
                TextColumn::make('finish_time')->time()->sortable(),
                TextColumn::make('guest_count')->numeric()->sortable(),
                TextColumn::make('is_approved_badge')
                    ->label('Status')
                    ->state(fn($record) => $record->is_approve)
                    ->badge()
                    ->formatStateUsing(fn($state) => $state ? 'Disetujui' : 'Belum Disetujui')
                    ->color(fn($state) => $state ? 'success' : 'danger'),

                ToggleColumn::make('is_approve')
                    ->label('Ubah Status')
                    ->visible(fn() => auth()->user()?->hasRole(['super_admin', 'admin']))
                    ->afterStateUpdated(function ($record, $state) {
                        // Format nomor WhatsApp
                        $phone = '62' . ltrim($record->phone_number, '0');

                        // Isi pesan berbeda tergantung status
                        $message = $state
                            ? "Halo! Permintaan peminjaman ruangan Anda telah DISETUJUI. Silakan cek akun Anda untuk detail lebih lanjut."
                            : "Halo! Status persetujuan peminjaman ruangan Anda telah DIBATALKAN. Mohon cek kembali akun Anda.";

                        // Kirim pesan WA via Fonnte
                        NotificationHelper::sendWhatsApp($phone, $message);

                        // Optional log (built-in Laravel)
                        Log::info('[WA STATUS TOGGLED]', [
                            'phone' => $phone,
                            'new_status' => $state ? 'approved' : 'unapproved',
                            'event_id' => $record->id,
                        ]);
                    }),

            ])
            ->filters([
                Tables\Filters\Filter::make('month')
                    ->form([
                        Select::make('month')
                            ->label('Bulan')
                            ->options(collect(range(0, 11))->mapWithKeys(function ($i) {
                                $date = Carbon::now()->startOfYear()->addMonths($i);
                                return [$date->format('Y-m') => $date->translatedFormat('F Y')]; // contoh: "Januari 2025"
                            })->toArray())
                            ->searchable()
                            ->preload(),
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (!empty($data['month'])) {
                            $query
                                ->whereMonth('date', Carbon::parse($data['month'])->month)
                                ->whereYear('date', Carbon::parse($data['month'])->year);
                        }
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                ExportBulkAction::make()->exports([
                    ExcelExport::make()->fromTable()->except([
                        'is_approve',
                    ]),
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
            'index' => Pages\ListEvents::route('/'),
            'create' => Pages\CreateEvent::route('/create'),
            'edit' => Pages\EditEvent::route('/{record}/edit'),
        ];
    }
}
