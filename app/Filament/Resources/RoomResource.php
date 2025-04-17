<?php

namespace App\Filament\Resources;

use Carbon\Carbon;
use Filament\Forms;
use App\Models\Room;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\RoomResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\RoomResource\RelationManagers;

class RoomResource extends Resource
{
    protected static ?string $model = Room::class;

    protected static ?string $navigationIcon = 'heroicon-s-building-office';

    protected static ?string $pluralLabel = 'Ruang';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->label('Nama Ruang'),
                Forms\Components\TextInput::make('capacity')
                    ->numeric()
                    ->required()
                    ->label('Capacity'),
                Forms\Components\FileUpload::make('image')
                    ->label('Gambar Ruangan')->columnSpan('full')
                    ->image(),
                Forms\Components\RichEditor::make('desc')
                    ->label('Description')->columnSpan('full')
                    ->required(),
                // Forms\Components\TextInput::make('yt_video_link')
                //     ->url()
                //     ->label('YouTube Video Link'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Split::make([
                    Tables\Columns\ImageColumn::make('image')
                        ->label('Gambar Ruangan')
                        ->grow(false), // Prevent image from stretching
                    Stack::make([
                        Tables\Columns\TextColumn::make('name')
                            ->label('Nama Ruang')
                            ->weight(FontWeight::Bold)
                            ->alignLeft(),
                        Tables\Columns\TextColumn::make('capacity')
                            ->label('Kapasitas')
                            ->alignLeft()
                            ->suffix(' orang'),

                        Tables\Columns\ToggleColumn::make('status')
                            ->label('Status Ketersediaan')
                            ->visible(fn() => auth()->user()->hasRole(['super_admin', 'admin']))
                            ->alignLeft(),
                    ])->space(1), // Optional: add spacing between items
                ])
            ])
            ->contentGrid([
                'md' => 2,
                'xl' => 3,
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make('Lihat Jadwal'),
                Tables\Actions\EditAction::make()
                    ->visible(fn() => auth()->user()->hasRole(['super_admin', 'admin'])),
                Tables\Actions\Action::make('Export Events')
                    ->label('Export Jadwal')
                    ->icon('heroicon-o-printer')
                    ->visible(fn() => auth()->user()?->hasRole(['super_admin', 'admin']))
                    ->color('danger')
                    ->form([
                        Select::make('month')
                            ->label('Pilih Bulan')
                            ->options(
                                collect(range(0, 11))->mapWithKeys(function ($i) {
                                    $date = Carbon::now()->startOfYear()->addMonths($i);
                                    return [$date->format('Y-m') => $date->translatedFormat('F Y')]; // e.g. Januari 2025
                                })->toArray()
                            )
                            ->required()
                            ->searchable()
                            ->preload(),
                    ])
                    ->action(function (array $data, Room $record) {
                        try {
                            $month = Carbon::parse($data['month']);

                            // Filter events by month + year
                            $events = $record->events()
                                ->whereMonth('date', $month->month)
                                ->whereYear('date', $month->year)
                                ->orderBy('date')
                                ->get();

                            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.room-events', [
                                'room' => $record,
                                'events' => $events,
                                'selectedMonth' => $month, // ✅ pass to view
                            ]);

                            $filename = 'jadwal_' . str($record->name)->slug() . '_' . $month->format('Ym') . '_' . now()->format('His') . '.pdf';
                            $path = storage_path("app/{$filename}");

                            $pdf->save($path);

                            return response()->download($path)->deleteFileAfterSend(true);
                        } catch (\Throwable $e) {
                            \Log::error('[EXPORT PDF ERROR]', [
                                'error' => $e->getMessage(),
                                'trace' => $e->getTraceAsString(),
                            ]);

                            \Filament\Notifications\Notification::make()
                                ->title('Gagal export PDF')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    })

            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ])
            ->paginated(false);
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
            'index' => Pages\ListRooms::route('/'),
            // 'create' => Pages\CreateRoom::route('/create'),
            // 'edit' => Pages\EditRoom::route('/{record}/edit'),
        ];
    }
}
