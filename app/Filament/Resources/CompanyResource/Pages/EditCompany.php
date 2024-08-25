<?php

namespace App\Filament\Resources\CompanyResource\Pages;

use Filament\Actions;
use App\Traits\ApprovalFlow;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\CompanyResource;

class EditCompany extends EditRecord
{
    use ApprovalFlow;

    protected static string $resource = CompanyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
