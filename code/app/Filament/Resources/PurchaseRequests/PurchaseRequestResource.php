<?php

namespace App\Filament\Resources\PurchaseRequests;

use App\Filament\Resources\PurchaseRequests\Pages\ManagePurchaseRequests;
use App\Models\PurchaseRequest;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Placeholder;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema as DbSchema;
// No Wizard/Steps/Tabs/Section/View to avoid class-not-found at runtime
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Hidden;
use Filament\Schemas\Components\Grid;
use App\Models\Product;
use App\Models\Department;

class PurchaseRequestResource extends Resource
{
    protected static ?string $model = PurchaseRequest::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocument;

    protected static ?string $recordTitleAttribute = 'request_number';

    // 🇪🇸 TEXTOS
    protected static ?string $navigationLabel = 'Solicitudes';
    protected static ?string $modelLabel = 'Solicitud';
    protected static ?string $pluralModelLabel = 'Solicitudes';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make('pr_tabs')
                ->columnSpanFull()
                ->tabs([
                    Tab::make('Información General')
                        ->icon('heroicon-o-clipboard-document')
                        ->badge(function ($get) {
                            $required = [
                                $get('request_number'),
                                $get('user_id'),
                                $get('department_id'),
                                $get('priority'),
                                $get('request_date'),
                                $get('required_date'),
                            ];
                            $filled = 0;
                            foreach ($required as $v) {
                                if (!empty($v)) $filled++;
                            }
                            return $filled . '/6';
                        })
                        ->schema([
                            Grid::make(12)
                                ->schema([
                                    TextInput::make('request_number')
                                        ->label('Número')
                                        ->readOnly()
                                        ->helperText('Se genera automáticamente al crear la solicitud')
                                        ->unique(ignoreRecord: true)
                                        ->required()
                                        ->columnSpan(['default' => 12, 'lg' => 6]),
                                    DatePicker::make('request_date')
                                        ->label('Fecha solicitud')
                                        ->required()
                                        ->default(fn () => now())
                                        ->columnSpan(['default' => 12, 'lg' => 6]),
                                    DatePicker::make('required_date')
                                        ->label('Fecha requerida')
                                        ->required()
                                        ->default(fn () => now()->addDays(7))
                                        ->columnSpan(['default' => 12, 'lg' => 6]),
                                    Select::make('priority')
                                        ->label('Prioridad')
                                        ->options(['low' => 'Baja', 'medium' => 'Normal', 'high' => 'Alta'])
                                        ->required()
                                        ->default('medium')
                                        ->columnSpan(['default' => 12, 'lg' => 6]),
                                    Select::make('user_id')
                                        ->label('Solicitante')
                                        ->relationship('user', 'name')
                                        ->searchable()
                                        ->preload()
                                        ->required()
                                        ->default(fn () => auth()->id())
                                        ->columnSpan(['default' => 12, 'lg' => 6]),
                                    Select::make('department_id')
                                        ->label('Centro de costo')
                                        ->relationship('department', 'name')
                                        ->searchable()
                                        ->preload()
                                        ->required()
                                        ->default(fn () => Department::query()->where('is_active', true)->orderBy('name')->value('id'))
                                        ->columnSpan(['default' => 12, 'lg' => 6]),
                                    Textarea::make('justification')
                                        ->label('Justificación')
                                        ->columnSpanFull(),
                                    Textarea::make('notes')
                                        ->label('Notas')
                                        ->columnSpanFull(),
                                ])
                                ->columns(12),
                        ]),
                    Tab::make('Productos y Servicios')
                        ->icon('heroicon-o-shopping-bag')
                        ->badge(fn ($get) => (string) count((array) ($get('items') ?? [])))
                        ->schema([
                            Repeater::make('items')
                                ->relationship('items')
                                ->label('Ítems')
                                ->addActionLabel('Agregar ítem')
                                ->reorderable()
                                ->defaultItems(1)
                                ->minItems(1)
                                ->required()
                                ->columnSpanFull()
                                ->columns(12)
                                ->schema([
                                    Toggle::make('is_custom')->label('Nuevo producto/servicio (no figura en catálogo)')->default(false)->inline(false)->columnSpan(4),
                                    Select::make('product_id')
                                        ->label('Producto')
                                        ->relationship('product', 'name', fn ($query) => $query->orderBy('name'))
                                        ->searchable()
                                        ->placeholder('Seleccione un producto...')
                                        ->helperText('Si no lo encuentras, activa "Nuevo producto/servicio" para cargarlo manualmente.')
                                        ->visible(fn ($get) => DbSchema::hasTable('products') && ! (bool) $get('is_custom'))
                                        ->required(fn ($get) => DbSchema::hasTable('products') && ! (bool) $get('is_custom'))
                                        ->columnSpan(8),
                                    Select::make('supplier_id')
                                        ->label('Proveedor')
                                        ->relationship('supplier', 'name', fn ($query) => $query->orderBy('name'))
                                        ->searchable()
                                        ->preload()
                                        ->placeholder('Seleccione un proveedor...')
                                        ->columnSpan(8),
                                    TextInput::make('custom_name')
                                        ->label('Nombre personalizado')
                                        ->placeholder('Ingrese el nombre del producto/servicio')
                                        ->visible(fn ($get) => (bool) $get('is_custom') || ! DbSchema::hasTable('products'))
                                        ->required(fn ($get) => (bool) $get('is_custom') || ! DbSchema::hasTable('products'))
                                        ->columnSpan(8),
                                    TextInput::make('description')->label('Descripción')->placeholder('Descripción breve del producto/servicio')->columnSpan(12),
                                    Textarea::make('specifications')
                                        ->label('Especificaciones')
                                        ->placeholder('Especificaciones o detalles adicionales')
                                        ->dehydrated(false)
                                        ->columnSpan(12),
                                    TextInput::make('quantity')
                                        ->label('Cantidad')
                                        ->numeric()
                                        ->required()
                                        ->minValue(1)
                                        ->default(1)
                                        ->live(debounce: 500)
                                        ->afterStateHydrated(function ($state, callable $set, callable $get) {
                                            $set('total_price', (float) ($get('unit_price') ?? 0) * (int) ($get('quantity') ?? 0));
                                        })
                                        ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                            $set('total_price', (float) ($get('unit_price') ?? 0) * (int) ($get('quantity') ?? 0));
                                        })
                                        ->columnSpan(6),
                                    TextInput::make('unit_price')
                                        ->label('Precio unitario')
                                        ->numeric()
                                        ->step(0.01)
                                        ->required()
                                        ->minValue(0)
                                        ->prefix('$')
                                        ->default(0)
                                        ->live(debounce: 500)
                                        ->afterStateHydrated(function ($state, callable $set, callable $get) {
                                            $set('total_price', (float) ($get('unit_price') ?? 0) * (int) ($get('quantity') ?? 0));
                                        })
                                        ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                            $set('total_price', (float) ($get('unit_price') ?? 0) * (int) ($get('quantity') ?? 0));
                                        })
                                        ->columnSpan(6),
                                    TextInput::make('total_price')->label('Total')->numeric()->step(0.01)->prefix('$')->readOnly()->default(0)->required()->dehydrated(true)->columnSpan(6),
                                    DatePicker::make('required_date')->label('Fecha requerida')->native(false)->columnSpan(6),
                                    Textarea::make('comments')->label('Comentarios')->placeholder('Notas u observaciones para compras (opcional)')->columnSpan(12),
                                    Select::make('status')->label('Estado')->options(['pending' => 'Pendiente', 'approved' => 'Aprobado', 'rejected' => 'Rechazado'])->default('pending')->columnSpan(6),
                                ]),
                        ]),
                    Tab::make('Revisión y Envío')
                        ->icon('heroicon-o-eye')
                        ->badge(function ($get) {
                            $required = [
                                $get('request_number'),
                                $get('user_id'),
                                $get('department_id'),
                                $get('priority'),
                                $get('request_date'),
                                $get('required_date'),
                            ];
                            foreach ($required as $v) {
                                if (empty($v)) {
                                    return 'Pendiente';
                                }
                            }
                            $items = (array) ($get('items') ?? []);
                            if (count($items) === 0) {
                                return 'Pendiente';
                            }
                            foreach ($items as $it) {
                                $isCustom = !empty($it['is_custom']);
                                $hasProduct = $isCustom
                                    ? !empty($it['custom_name'])
                                    : (!DbSchema::hasTable('products') || !empty($it['product_id']));
                                $qtyOk = (int) ($it['quantity'] ?? 0) >= 1;
                                $priceOk = (float) ($it['unit_price'] ?? 0) >= 0;
                                if (!($hasProduct && $qtyOk && $priceOk)) {
                                    return 'Pendiente';
                                }
                            }
                            return 'Listo';
                        })
                        ->schema([
                            Select::make('status')
                                ->label('Estado inicial')
                                ->options([
                                    'pending' => 'Pendiente',
                                    'approved' => 'Aprobada',
                                    'rejected' => 'Rechazada',
                                    'completed' => 'Completada',
                                ])
                                ->default('pending')
                                ->required()
                                ->disabled(function ($get) {
                                    $required = [
                                        $get('request_number'),
                                        $get('user_id'),
                                        $get('department_id'),
                                        $get('priority'),
                                        $get('request_date'),
                                        $get('required_date'),
                                    ];
                                    foreach ($required as $v) {
                                        if (empty($v)) {
                                            return true;
                                        }
                                    }
                                    $items = (array) ($get('items') ?? []);
                                    return count($items) < 1;
                                }),
                            Placeholder::make('summary_header')->label('Resumen de la Solicitud')->content('Verifica todos los datos antes de enviar la solicitud.'),
                            Placeholder::make('summary_total_items')->label('Total de productos')->content(fn ($get) => (string) count((array) ($get('items') ?? []))),
                            Placeholder::make('summary_new_items')->label('Productos nuevos')->content(function ($get) { $items = (array) ($get('items') ?? []); $count = 0; foreach ($items as $it) { if (!empty($it['is_custom'])) { $count++; } } return (string) $count; }),
                            Placeholder::make('summary_amount')->label('Monto total estimado')->content(function ($get) { $items = (array) ($get('items') ?? []); $sum = 0; foreach ($items as $it) { $sum += (float) ($it['total_price'] ?? 0); } return '$' . number_format($sum, 2, ',', '.') . ' ARS'; }),
                        ]),
                ]),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            TextEntry::make('request_number')->label('Número'),
            TextEntry::make('user.name')->label('Solicitante'),
            TextEntry::make('department.name')->label('Departamento'),
            TextEntry::make('request_date')->label('Fecha solicitud')->date(),
            TextEntry::make('required_date')->label('Fecha requerida')->date(),
            TextEntry::make('priority')->label('Prioridad'),
            TextEntry::make('status')->label('Estado'),
            TextEntry::make('total_amount')->label('Monto total')->money('ARS')->formatStateUsing(fn ($s) => '$' . number_format($s, 2, ',', '.') . ' ARS'),
            TextEntry::make('currency')->label('Moneda'),
            TextEntry::make('justification')->label('Justificación')->columnSpanFull(),
            TextEntry::make('notes')->label('Notas')->columnSpanFull(),
            TextEntry::make('approvedBy.name')->label('Aprobado por')->placeholder('-'),
            TextEntry::make('approved_at')->label('Aprobado el')->dateTime()->placeholder('-'),
            TextEntry::make('rejectedBy.name')->label('Rechazado por')->placeholder('-'),
            TextEntry::make('rejected_at')->label('Rechazado el')->dateTime()->placeholder('-'),
            TextEntry::make('rejection_reason')->label('Motivo rechazo')->placeholder('-'),
            TextEntry::make('erp_request_id')->label('ID ERP')->placeholder('-'),
            TextEntry::make('created_at')->label('Creado')->dateTime(),
            TextEntry::make('updated_at')->label('Actualizado')->dateTime(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('request_number')
            ->modifyQueryUsing(fn (Builder $query) =>
                $query->withSum('items as items_sum_total_price', 'total_price')
            )
            ->columns([
                TextColumn::make('request_number')
                    ->label('Número')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Número copiado')
                    ->copyMessageDuration(1500)
                    ->toggleable(),
                TextColumn::make('user.name')
                    ->label('Solicitante')
                    ->searchable()
                    ->sortable()
                    ->limit(24)
                    ->tooltip(fn ($state) => $state)
                    ->toggleable(),
                TextColumn::make('department.name')
                    ->label('Departamento')
                    ->searchable()
                    ->sortable()
                    ->limit(24)
                    ->tooltip(fn ($state) => $state)
                    ->toggleable(),
                TextColumn::make('request_date')->label('Fecha solicitud')->date()->sortable()->toggleable(),
                TextColumn::make('required_date')->label('Fecha requerida')->date()->sortable()->toggleable(),
                TextColumn::make('priority')
                    ->label('Prioridad')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'low' => 'gray',
                        'medium' => 'primary',
                        'high' => 'danger',
                        default => 'gray',
                    })
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'primary',
                        'rejected' => 'danger',
                        'completed' => 'success',
                        default => 'gray',
                    })
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('items_sum_total_price')
                    ->label('Monto total')
                    ->formatStateUsing(fn ($state, $record) => '$' . number_format((float) $state, 2, ',', '.') . ' ' . ($record->currency ?? 'ARS'))
                    ->alignRight()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('currency')->label('Moneda')->sortable()->toggleable(),
                TextColumn::make('erp_request_id')->label('ID ERP')->numeric()->sortable()->placeholder('-')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')->label('Creado')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')->label('Actualizado')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'pending' => 'Pendiente',
                        'approved' => 'Aprobada',
                        'rejected' => 'Rechazada',
                        'completed' => 'Completada',
                    ]),
                Filter::make('request_date')
                    ->label('Rango de Fecha (Solicitud)')
                    ->form([
                        DatePicker::make('from')->label('Desde'),
                        DatePicker::make('until')->label('Hasta'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['from'] ?? null, fn ($q, $date) => $q->whereDate('request_date', '>=', $date))
                            ->when($data['until'] ?? null, fn ($q, $date) => $q->whereDate('request_date', '<=', $date));
                    }),
            ])
            ->recordActions([
                Action::make('approve')
                    ->label('')
                    ->icon('heroicon-o-check')
                    ->iconSize('sm')
                    ->color('success')
                    ->tooltip('Aprobar solicitud')
                    ->visible(fn (PurchaseRequest $record) => $record->status === 'pending' && auth()->user()?->can('purchase-requests.approve'))
                    ->authorize(fn (PurchaseRequest $record) => auth()->user()?->can('purchase-requests.approve'))
                    ->requiresConfirmation()
                    ->action(function (PurchaseRequest $record) {
                        $record->update([
                            'status' => 'approved',
                            'approved_by' => auth()->id(),
                            'approved_at' => now(),
                        ]);
                    }),
                Action::make('reject')
                    ->label('')
                    ->icon('heroicon-o-x-mark')
                    ->iconSize('sm')
                    ->color('danger')
                    ->tooltip('Rechazar solicitud')
                    ->visible(fn (PurchaseRequest $record) => in_array($record->status, ['pending','approved']) && auth()->user()?->can('purchase-requests.reject'))
                    ->authorize(fn (PurchaseRequest $record) => auth()->user()?->can('purchase-requests.reject'))
                    ->form([
                        Textarea::make('rejection_reason')->label('Motivo de rechazo')->required(),
                    ])
                    ->action(function (PurchaseRequest $record, array $data) {
                        $record->update([
                            'status' => 'rejected',
                            'rejected_by' => auth()->id(),
                            'rejected_at' => now(),
                            'rejection_reason' => $data['rejection_reason'] ?? null,
                        ]);
                    }),
                ViewAction::make()->label('')->icon('heroicon-o-eye')->iconSize('sm')->tooltip('Ver solicitud'),
                EditAction::make()->label('')->icon('heroicon-o-pencil')->iconSize('sm')->tooltip('Editar solicitud')->authorize(fn ($record) => auth()->user()?->can('purchase-requests.update')),
                DeleteAction::make()->label('')->icon('heroicon-o-trash')->iconSize('sm')->tooltip('Eliminar solicitud')->authorize(fn ($record) => auth()->user()?->can('purchase-requests.delete')),
            ])
            ->toolbarActions([
                // Exportar TODOS (CSV)
                Action::make('export_all_csv')
                    ->label('Exportar CSV')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('gray')
                    ->visible(fn () => auth()->user()?->can('purchase-requests.export'))
                    ->authorize(fn () => auth()->user()?->can('purchase-requests.export'))
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
                        $columns = $data['columns'];
                        $records = PurchaseRequest::query()->with(['user','department','approvedBy','rejectedBy'])->get();
                        $filename = 'purchase_requests_'.date('Ymd_His').'.csv';
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
                        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
                    }),
                // Exportar TODOS (PDF)
                Action::make('export_all_pdf')
                    ->label('Exportar PDF')
                    ->icon('heroicon-o-document')
                    ->color('gray')
                    ->visible(fn () => class_exists('Barryvdh\\DomPDF\\Facade\\Pdf'))
                    ->visible(fn () => class_exists('Barryvdh\\DomPDF\\Facade\\Pdf') && auth()->user()?->can('purchase-requests.export'))
                    ->authorize(fn () => auth()->user()?->can('purchase-requests.export'))
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
                        $columns = $data['columns'];
                        $records = PurchaseRequest::query()->with(['user','department','approvedBy','rejectedBy'])->get();
                        $filename = 'purchase_requests_'.date('Ymd_His').'.pdf';
                        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.purchase-requests.pdf', [
                            'columns' => $columns,
                            'records' => $records,
                            'title' => 'Exportación de Solicitudes',
                            'generatedAt' => date('Y-m-d H:i:s'),
                        ])->setPaper('a4', 'landscape');
                        return response()->streamDownload(fn () => print($pdf->output()), $filename, ['Content-Type' => 'application/pdf']);
                    }),
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label('')->icon('heroicon-o-trash')->iconSize('sm')->tooltip('Eliminar seleccionados')->authorize(fn () => auth()->user()?->can('purchase-requests.delete')),
                ]),
            ])
            ->bulkActions([
                // Exportar SELECCIONADOS (CSV)
                BulkAction::make('export_selected_csv')
                    ->label('Exportar CSV')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('gray')
                    ->visible(fn () => auth()->user()?->can('purchase-requests.export'))
                    ->authorize(fn () => auth()->user()?->can('purchase-requests.export'))
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
                        $columns = $data['columns'];
                        $records->load(['user','department','approvedBy','rejectedBy']);
                        $filename = 'purchase_requests_selected_'.date('Ymd_His').'.csv';
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
                        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
                    })
                    ->tooltip('Exportar solo los registros seleccionados a CSV'),
                // Exportar SELECCIONADOS (PDF)
                BulkAction::make('export_selected_pdf')
                    ->label('Exportar PDF')
                    ->icon('heroicon-o-document')
                    ->color('gray')
                    ->visible(fn () => class_exists('Barryvdh\\DomPDF\\Facade\\Pdf'))
                    ->visible(fn () => class_exists('Barryvdh\\DomPDF\\Facade\\Pdf') && auth()->user()?->can('purchase-requests.export'))
                    ->authorize(fn () => auth()->user()?->can('purchase-requests.export'))
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
                        $columns = $data['columns'];
                        $records->load(['user','department','approvedBy','rejectedBy']);
                        $filename = 'purchase_requests_selected_'.date('Ymd_His').'.pdf';
                        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.purchase-requests.pdf', [
                            'columns' => $columns,
                            'records' => $records,
                            'title' => 'Exportación de Solicitudes',
                            'generatedAt' => date('Y-m-d H:i:s'),
                        ])->setPaper('a4', 'landscape');
                        return response()->streamDownload(fn () => print($pdf->output()), $filename, ['Content-Type' => 'application/pdf']);
                    }),
                // Aprobar seleccionados
                BulkAction::make('approve_selected')
                    ->label('Aprobar')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->authorize(fn () => auth()->user()?->can('purchase-requests.approve'))
                    ->requiresConfirmation()
                    ->action(function (\Illuminate\Support\Collection $records) {
                        \DB::transaction(function () use ($records) {
                            foreach ($records as $record) {
                                if ($record->status === 'pending') {
                                    $record->update([
                                        'status' => 'approved',
                                        'approved_by' => auth()->id(),
                                        'approved_at' => now(),
                                    ]);
                                }
                            }
                        });
                    })
                    ->deselectRecordsAfterCompletion(),
                // Rechazar seleccionados
                BulkAction::make('reject_selected')
                    ->label('Rechazar')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->authorize(fn () => auth()->user()?->can('purchase-requests.reject'))
                    ->form([
                        \Filament\Forms\Components\Textarea::make('rejection_reason')->label('Motivo de rechazo')->required(),
                    ])
                    ->action(function (\Illuminate\Support\Collection $records, array $data) {
                        \DB::transaction(function () use ($records, $data) {
                            foreach ($records as $record) {
                                if (in_array($record->status, ['pending','approved'])) {
                                    $record->update([
                                        'status' => 'rejected',
                                        'rejected_by' => auth()->id(),
                                        'rejected_at' => now(),
                                        'rejection_reason' => $data['rejection_reason'] ?? null,
                                    ]);
                                }
                            }
                        });
                    })
                    ->deselectRecordsAfterCompletion(),
                // Completar seleccionados
                BulkAction::make('complete_selected')
                    ->label('Marcar completadas')
                    ->icon('heroicon-o-check-badge')
                    ->color('primary')
                    ->authorize(fn () => auth()->user()?->can('purchase-requests.update'))
                    ->requiresConfirmation()
                    ->action(function (\Illuminate\Support\Collection $records) {
                        \DB::transaction(function () use ($records) {
                            foreach ($records as $record) {
                                if ($record->status === 'approved') {
                                    $record->update([
                                        'status' => 'completed',
                                    ]);
                                }
                            }
                        });
                    })
                    ->deselectRecordsAfterCompletion(),
                DeleteBulkAction::make()
                    ->label('Eliminar')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManagePurchaseRequests::route('/'),
        ];
    }

    public static function getExportableColumns(): array
    {
        return [
            'request_number' => 'Número',
            'user.name' => 'Solicitante',
            'department.name' => 'Departamento',
            'request_date' => 'Fecha solicitud',
            'required_date' => 'Fecha requerida',
            'priority' => 'Prioridad',
            'status' => 'Estado',
            'total_amount' => 'Monto total',
            'currency' => 'Moneda',
            'justification' => 'Justificación',
            'notes' => 'Notas',
            'approvedBy.name' => 'Aprobado por',
            'approved_at' => 'Aprobado el',
            'rejectedBy.name' => 'Rechazado por',
            'rejected_at' => 'Rechazado el',
            'rejection_reason' => 'Motivo rechazo',
            'erp_request_id' => 'ID ERP',
            'created_at' => 'Creado',
            'updated_at' => 'Actualizado',
        ];
    }

}
