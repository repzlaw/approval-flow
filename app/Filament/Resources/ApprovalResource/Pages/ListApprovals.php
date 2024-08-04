<?php

namespace App\Filament\Resources\ApprovalResource\Pages;

use App\Enums\ApprovalStatus;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\ApprovalResource;

class ListApprovals extends ListRecords
{
    protected static string $resource = ApprovalResource::class;

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Activities'),
            'approved' => Tab::make('Approved')
                ->modifyQueryUsing(function ($query) {
                    return $query->where('status', ApprovalStatus::APPROVED);
                }),
            'submitted' => Tab::make('Submitted')
                ->modifyQueryUsing(function ($query) {
                    return $query->where('status', ApprovalStatus::SUBMITTED);
                }),
            'rejected' => Tab::make('Rejected')
                ->modifyQueryUsing(function ($query) {
                    return $query->where('status', ApprovalStatus::REJECTED);
                }),
        ];
    }

    public function getDefaultActiveTab(): string | int | null
    {
        return 'submitted';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
