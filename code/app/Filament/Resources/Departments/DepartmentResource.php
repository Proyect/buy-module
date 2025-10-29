<?php

namespace App\Filament\Resources\Departments;

use App\Filament\Resources\Departments\Pages\ManageDepartments;
use App\Models\Department;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Tables\Table;

class DepartmentResource extends Resource
{
    protected static ?string $model = Department::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    // 🇪🇸 TEXTO EN ESPAÑOL
    protected static ?string $navigationLabel = 'Departamentos';
    protected static ?string $modelLabel = 'Departamento';
    protected static ?string $pluralModelLabel = 'Departamentos';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Ej: Recursos Humanos'),
                TextInput::make('code')
                    ->label('Código')
                    ->required()
                    ->maxLength(50)
                    ->unique(ignoreRecord: true)
                    ->placeholder('Ej: RH01'),
                Textarea::make('description')
                    ->label('Descripción')
                    ->columnSpanFull()
                    ->placeholder('Descripción del departamento...'),
                Select::make('manager_id')
                    ->label('Gerente')
                    ->relationship('manager', 'name')
                    ->searchable()
                    ->preload()
                    ->placeholder('Seleccionar gerente...'),
                TextInput::make('budget_limit')
                    ->label('Presupuesto')
                    ->required()
                    ->numeric()
                    ->prefix('$')
                    ->step(0.01)
                    ->placeholder('0.00')
                    ->helperText('Monto en Pesos Argentinos (ARS)'),
                Toggle::make('is_active')
                    ->label('Activo')
                    ->default(true),
                TextInput::make('erp_department_id')
                    ->label('ID del Departamento en ERP')
                    ->numeric()
                    ->helperText('ID correspondiente en el sistema ERP externo')
                    ->placeholder('Ej: 1001'),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')
                    ->label('Nombre'),
                TextEntry::make('code')
                    ->label('Código'),
                TextEntry::make('description')
                    ->label('Descripción')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('manager.name')
                    ->label('Gerente')
                    ->placeholder('-'),
                TextEntry::make('budget_limit')
                    ->label('Presupuesto')
                    ->money('ARS')
                    ->formatStateUsing(fn ($state) => '$' . number_format($state, 2, ',', '.') . ' ARS'),
                IconEntry::make('is_active')
                    ->label('Activo')
                    ->boolean(),
                TextEntry::make('erp_department_id')
                    ->label('ID ERP')
                    ->numeric()
                    ->placeholder('-'),
              /*  TextEntry::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime()
                    ->placeholder('-'),*/
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('code')
                    ->label('Código')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('manager.name')
                    ->label('Gerente')
                    ->searchable()
                    ->sortable()
                    ->placeholder('-'),
                TextColumn::make('budget_limit')
                    ->label('Presupuesto')
                    ->money('ARS')
                    ->formatStateUsing(fn ($state) => '$' . number_format($state, 2, ',', '.') . ' ARS')
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean(),
                TextColumn::make('erp_department_id')
                    ->label('ID ERP')
                    ->numeric()
                    ->sortable()
                    ->placeholder('-')
                    ->tooltip('ID del departamento en el sistema ERP'),
               /* TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),*/
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('')
                    ->icon('heroicon-o-eye')
                    ->iconSize('sm')
                    ->tooltip('Ver departamento'),
                EditAction::make()
                    ->label('')
                    ->icon('heroicon-o-pencil')
                    ->iconSize('sm')
                    ->tooltip('Editar departamento'),
                DeleteAction::make()
                    ->label('')
                    ->icon('heroicon-o-trash')
                    ->iconSize('sm')
                    ->tooltip('Eliminar departamento'),
            ])
            ->toolbarActions([
                // Exportar TODOS los registros visibles a CSV (selección de columnas)
                Action::make('export_all_csv')
                    ->label('Exportar CSV')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('gray')
                    ->form([
                        \Filament\Forms\Components\CheckboxList::make('columns')
                            ->label('Columnas a exportar')
                            ->options(self::getExportableColumns())
                            ->columns(2)
                            ->default(array_keys(self::getExportableColumns()))
                            ->bulkToggleable()
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        $columns = $data['columns'] ?? [];
                        if (empty($columns)) {
                            $columns = array_keys(self::getExportableColumns());
                        }

                        // Exporta TODOS los registros (sin depender de selección)
                        // Eager-load relaciones necesarias para columnas anidadas
                        $query = Department::query()->with(['manager']);
                        $records = $query->get();

                        $filename = 'departments_export_'.date('Ymd_His').'.csv';

                        return response()->streamDownload(function () use ($records, $columns) {
                            $out = fopen('php://output', 'w');
                            // Encabezados
                            fputcsv($out, $columns, ';');
                            foreach ($records as $row) {
                                $line = [];
                                foreach ($columns as $col) {
                                    $line[] = data_get($row, $col);
                                }
                                fputcsv($out, $line, ';');
                            }
                            fclose($out);
                        }, $filename, [
                            'Content-Type' => 'text/csv; charset=UTF-8',
                        ]);
                    })
                    ->tooltip('Exportar todos los registros a CSV (Excel compatible)'),
                // Exportar TODOS los registros a PDF (selección de columnas)
                Action::make('export_all_pdf')
                    ->label('Exportar PDF')
                    ->icon('heroicon-o-document')
                    ->color('gray')
                    ->visible(fn () => class_exists('Barryvdh\\DomPDF\\Facade\\Pdf'))
                    ->form([
                        \Filament\Forms\Components\CheckboxList::make('columns')
                            ->label('Columnas a exportar')
                            ->options(self::getExportableColumns())
                            ->columns(2)
                            ->default(array_keys(self::getExportableColumns()))
                            ->bulkToggleable()
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        $columns = $data['columns'] ?? array_keys(self::getExportableColumns());
                        $query = Department::query()->with(['manager']);
                        $records = $query->get();

                        $filename = 'departments_export_'.date('Ymd_His').'.pdf';

                        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.departments.pdf', [
                            'columns' => $columns,
                            'records' => $records,
                            'title' => 'Exportación de Departamentos',
                            'generatedAt' => date('Y-m-d H:i:s'),
                        ])->setPaper('a4', 'landscape');

                        return response()->streamDownload(fn () => print($pdf->output()), $filename, [
                            'Content-Type' => 'application/pdf',
                        ]);
                    })
                    ->tooltip('Exportar todos los registros a PDF'),
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('')
                        ->icon('heroicon-o-trash')
                        ->iconSize('sm')
                        ->tooltip('Eliminar seleccionados'),
                ]),
            ])
            ->bulkActions([
                // Eliminar seleccionados
                DeleteBulkAction::make()
                    ->label('')
                    ->icon('heroicon-o-trash')
                    ->iconSize('sm')
                    ->tooltip('Eliminar seleccionados'),
                // Exportar SOLO seleccionados a CSV (selección de columnas)
                BulkAction::make('export_selected_csv')
                    ->label('Exportar CSV')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('gray')
                    ->form([
                        \Filament\Forms\Components\CheckboxList::make('columns')
                            ->label('Columnas a exportar')
                            ->options(self::getExportableColumns())
                            ->columns(2)
                            ->default(array_keys(self::getExportableColumns()))
                            ->bulkToggleable()
                            ->required(),
                    ])
                    ->action(function (\Illuminate\Support\Collection $records, array $data) {
                        $columns = $data['columns'] ?? [];
                        if (empty($columns)) {
                            $columns = array_keys(self::getExportableColumns());
                        }

                        $filename = 'departments_selected_'.date('Ymd_His').'.csv';

                        // Eager-load relaciones
                        $records->load(['manager']);

                        return response()->streamDownload(function () use ($records, $columns) {
                            $out = fopen('php://output', 'w');
                            fputcsv($out, $columns, ';');
                            foreach ($records as $row) {
                                $line = [];
                                foreach ($columns as $col) {
                                    $line[] = data_get($row, $col);
                                }
                                fputcsv($out, $line, ';');
                            }
                            fclose($out);
                        }, $filename, [
                            'Content-Type' => 'text/csv; charset=UTF-8',
                        ]);
                    })
                    ->tooltip('Exportar solo los registros seleccionados a CSV (Excel compatible)'),
                // Exportar SOLO seleccionados a PDF (selección de columnas)
                BulkAction::make('export_selected_pdf')
                    ->label('Exportar PDF')
                    ->icon('heroicon-o-document')
                    ->color('gray')
                    ->visible(fn () => class_exists('Barryvdh\\DomPDF\\Facade\\Pdf'))
                    ->form([
                        \Filament\Forms\Components\CheckboxList::make('columns')
                            ->label('Columnas a exportar')
                            ->options(self::getExportableColumns())
                            ->columns(2)
                            ->default(array_keys(self::getExportableColumns()))
                            ->bulkToggleable()
                            ->required(),
                    ])
                    ->action(function (\Illuminate\Support\Collection $records, array $data) {
                        $columns = $data['columns'] ?? array_keys(self::getExportableColumns());
                        $records->load(['manager']);

                        $filename = 'departments_selected_'.date('Ymd_His').'.pdf';

                        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.departments.pdf', [
                            'columns' => $columns,
                            'records' => $records,
                            'title' => 'Exportación de Departamentos',
                            'generatedAt' => date('Y-m-d H:i:s'),
                        ])->setPaper('a4', 'landscape');

                        return response()->streamDownload(fn () => print($pdf->output()), $filename, [
                            'Content-Type' => 'application/pdf',
                        ]);
                    })
                    ->tooltip('Exportar solo los registros seleccionados a PDF'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageDepartments::route('/'),
        ];
    }

    /**
     * Devuelve las columnas exportables (clave = atributo/relación, valor = etiqueta visible)
     * Debe ser pública para que pueda ser invocada desde la vista Blade del PDF.
     */
    public static function getExportableColumns(): array
    {
        return [
            'name' => 'Nombre',
            'code' => 'Código',
            'description' => 'Descripción',
            'manager.name' => 'Gerente',
            'budget_limit' => 'Presupuesto',
            'is_active' => 'Activo',
            'erp_department_id' => 'ID ERP',
           /* 'created_at' => 'Creado',
            'updated_at' => 'Actualizado',*/
        ];
    }
}