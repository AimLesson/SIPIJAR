<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Room;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
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
                            ->visible(fn () => auth()->user()->hasRole(['super_admin', 'admin']))
                            ->alignLeft(),
                    ])->space(1), // Optional: add spacing between items
                ])
            ])
            ->contentGrid([
                'md' => 2,
                'xl' => 4,
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make('Lihat Jadwal'),
                Tables\Actions\EditAction::make()
                    ->visible(fn() => auth()->user()->hasRole(['super_admin', 'admin'])),
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
