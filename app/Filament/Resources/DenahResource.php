<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Denah;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Columns\Layout\Panel;
use Filament\Tables\Columns\Layout\Stack;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\DenahResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\DenahResource\RelationManagers;

class DenahResource extends Resource
{
    protected static ?string $model = Denah::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $pluralLabel = 'Denah Ruangan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\FileUpload::make('picture')
                    ->label('Denah')
                    ->image()
                    ->directory('denah-pictures')
                    ->required()->columnSpan('full'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Stack::make([
                    Tables\Columns\ImageColumn::make('picture')
                        ->label('Gambar Denah')
                        ->alignCenter()
                        ->width(700)
                        ->height(500)
                        ->columnSpan('full')
                        ->extraAttributes(['class' => 'rounded-lg shadow-lg']),
                ]),
            ])
            ->contentGrid([
                'md' => 1,
                'xl' => 1,
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ])->paginated(false);
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
            'index' => Pages\ListDenahs::route('/'),
            // 'create' => Pages\CreateDenah::route('/create'),
            // 'view' => Pages\ViewDenah::route('/{record}'),
            // 'edit' => Pages\EditDenah::route('/{record}/edit'),
        ];
    }
}
