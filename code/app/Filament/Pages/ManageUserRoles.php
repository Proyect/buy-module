<?php

namespace App\Filament\Pages;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Columns\TagsColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Table;
use BackedEnum;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class ManageUserRoles extends Page implements HasTable
{
    use InteractsWithTable;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected string $view = 'filament.pages.manage-user-roles';

    protected static ?string $navigationLabel = 'Usuarios y Roles';

    protected static ?string $title = 'Usuarios y Roles';

    protected static ?int $navigationSort = 90;

    public static function shouldRegisterNavigation(): bool
    {
        $user = Auth::user();
        return (bool) ($user?->hasRole('admin'));
    }

    public function mount(): void
    {
        abort_unless(Auth::user()?->hasRole('admin'), 403);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nombre')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('email')->label('Email')->searchable()->sortable(),
                TagsColumn::make('roles_list')->label('Roles')->getStateUsing(fn (User $user) => $user->getRoleNames()->toArray()),
            ])
            ->actions([
                Action::make('assign_roles')
                    ->label('Asignar roles')
                    ->icon('heroicon-o-key')
                    ->form([
                        \Filament\Forms\Components\CheckboxList::make('roles')
                            ->label('Roles')
                            ->options(fn () => Role::query()->orderBy('name')->pluck('name', 'name')->all())
                            ->bulkToggleable()
                            ->columns(2)
                            ->required(),
                    ])
                    ->action(function (User $record, array $data) {
                        $record->syncRoles($data['roles'] ?? []);
                    })
                    ->visible(fn () => Auth::user()?->hasRole('admin')),
            ])
            ->paginated([25, 50, 100])
            ->defaultSort('name');
    }

    protected function getTableQuery(): Builder
    {
        return User::query();
    }
}
