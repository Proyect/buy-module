<?php

namespace App\Filament\Resources\PurchaseRequests\RelationManagers;

use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Actions as FormActions;
use Filament\Forms\Components\Actions\Action as FormAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';
    protected static ?string $title = 'Ítems';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Toggle::make('is_custom')->label('Nuevo producto/servicio')->default(false),
            Select::make('product_id')
                ->label('Producto')
                ->searchable()
                ->preload()
                ->visible(fn ($get) => ! $get('is_custom'))
                ->required(fn ($get) => ! $get('is_custom')),
            TextInput::make('custom_name')
                ->label('Nombre (nuevo)')
                ->visible(fn ($get) => (bool) $get('is_custom'))
                ->required(fn ($get) => (bool) $get('is_custom')),
            TextInput::make('quantity')->label('Cantidad')->numeric()->minValue(1)->default(1)->required(),
            TextInput::make('unit_price')->label('Precio unitario')->numeric()->step('0.01')->default(0),
            TextInput::make('total_price')->label('Total')->numeric()->step('0.01')
                ->disabled()
                ->dehydrated()
                ->afterStateHydrated(fn ($set, $get) => $set('total_price', number_format((float) ($get('quantity') ?? 0) * (float) ($get('unit_price') ?? 0), 2, '.', '')))
                ->reactive()
                ->afterStateUpdated(function ($set, $get) {
                    $set('total_price', number_format((float) ($get('quantity') ?? 0) * (float) ($get('unit_price') ?? 0), 2, '.', ''));
                }),
            Textarea::make('comments')->label('Comentarios')->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product.name')->label('Producto')->toggleable(),
                TextColumn::make('custom_name')->label('Producto (nuevo)')->toggleable(),
                TextColumn::make('quantity')->label('Cant.'),
                TextColumn::make('unit_price')->label('P. Unit')->money('ARS'),
                TextColumn::make('total_price')->label('Total')->money('ARS'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}
