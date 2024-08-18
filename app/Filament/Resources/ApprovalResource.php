<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Approval;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Enums\ApprovalStatus;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Model;
use Filament\Infolists\Components\Group;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\KeyValueEntry;
use App\Filament\Resources\ApprovalResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ApprovalResource\RelationManagers;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;

class ApprovalResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Approval::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'delete',
            'delete_any',
            'approve'
        ];
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Tables\Columns\TextColumn::make('data'),
                Tables\Columns\TextColumn::make('approvable_type')
                    ->label('Resource')
                    ->formatStateUsing(function ($state) {
                        return class_basename($state);
                    }),
                Tables\Columns\TextColumn::make('operation'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->sortable()
                    ->color(function ($state) {
                        return $state->getColor();
                    }),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Requested By')
                    ->numeric()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->slideOver(),
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('approve')
                        ->form([
                            Forms\Components\Textarea::make('approver_comment')
                                ->label('Approver Comment')
                        ])
                        ->visible(function ($record) {
                            return ($record->status === ApprovalStatus::SUBMITTED || $record->status === ApprovalStatus::REJECTED);
                        })
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalSubmitActionLabel('Approve')
                        ->action(function (Approval $record, array $data) {
                            $record->approve($data);
                        })->after(function () {
                            Notification::make()->success()->title('Request Approved')
                                ->duration(1000)
                                ->send();
                        }),
                    Tables\Actions\Action::make('reject')
                        ->form([
                            Forms\Components\Textarea::make('approver_comment')
                                ->label('Approver Comment')
                        ])
                        ->icon('heroicon-o-no-symbol')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalSubmitActionLabel('Reject')
                        ->visible(function ($record) {
                            return ($record->status === ApprovalStatus::SUBMITTED);
                        })
                        ->action(function (Approval $record, array $data) {
                            $record->reject($data);
                        })->after(function () {
                            Notification::make()->danger()->title('Request Rejected')
                                ->duration(1000)
                                ->send();
                        }),

                ])
                ->visible(function () {
                    return auth()->user()->can('approve_approval');
                }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->recordAction(Tables\Actions\ViewAction::class) 
            ->recordUrl(null)
            ->modifyQueryUsing(function (Builder $query) {
                if (auth()->user()->can('approve_approval')) {
                    return $query;
                } else {
                    return $query->where('user_id', auth()->user()->id);
                }
            });
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Related Record')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('approvable_id')
                            ->label('Record ID'),
                        TextEntry::make('approvable_type')
                            ->label(function (Model $record) {
                                $relatedModelClass = $record->approvable_type;
                                $relatedModel = $relatedModelClass::find($record->approvable_id);

                                if ($relatedModel) {
                                    $approvableRelatedColumn = getApprovableRelatedColumnValue($relatedModel);
                                    if ($approvableRelatedColumn){
                                        return ucfirst($approvableRelatedColumn ?? 'Related Record');
                                    }
                                    $relatedModelKeys = array_keys($relatedModel->getAttributes());
                                    $secondKey = $relatedModelKeys[1] ?? 'Related Record';
                                    return ucfirst($secondKey);
                                }

                                return 'Related Record';
                            })
                            ->getStateUsing(function (Model $record) {
                                $relatedModelClass = $record->approvable_type;
                                $relatedModel = $relatedModelClass::find($record->approvable_id);
                        
                                if ($relatedModel) {
                                    $approvableRelatedColumn = getApprovableRelatedColumnValue($relatedModel);
                                    if ($approvableRelatedColumn){
                                        return $relatedModel->{$approvableRelatedColumn} ?? 'Not Found';
                                    }
                                    $relatedModelKeys = array_keys($relatedModel->getAttributes());
                                    $secondKey = $relatedModelKeys[1] ?? null;
                                    return $secondKey ? $relatedModel->{$secondKey} : 'Not Found';
                                }
                        
                                return 'Not Found';
                            })
                        ])
                        ->visible(function ($record) {
                            return $record->operation === 'Edit';
                        }),
                Section::make('Request Information')
                    ->columns(3)
                    ->schema([
                        Group::make()
                            ->columnSpan(3)
                            ->columns(2)
                            ->schema([
                                KeyValueEntry::make('data.new')
                                    ->label('New Data')
                                    ->columnSpan(2),
                                KeyValueEntry::make('data.old')
                                    ->label('Old Data')
                                    ->visible(function ($record) {
                                        return $record->operation === 'Edit';
                                    })
                                    ->columnSpan(2),
                                TextEntry::make('approvable_type')
                                    ->label('Resource')
                                    ->formatStateUsing(function ($state) {
                                        return class_basename($state);
                                    }),
                                TextEntry::make('user.name')
                                    ->label('Requested By'),
                                TextEntry::make('operation'),
                                TextEntry::make('status')
                                    ->badge()
                                    ->color(function ($state) {
                                        return $state->getColor();
                                    }),
                            ]),
                        ]),

                        Section::make('Approval Information')
                            ->columns(2)
                            ->schema([
                                TextEntry::make('approver.name'),
                                TextEntry::make('approve_date'),
                                TextEntry::make('approver_comment')
                                    ->columnSpan(2),
                        ])
            ]);
    }
    
    public static function canCreate(): bool
   {
      return false;
   }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListApprovals::route('/'),
            // 'create' => Pages\CreateApproval::route('/create'),
            // 'edit' => Pages\EditApproval::route('/{record}/edit'),
        ];
    }
}
