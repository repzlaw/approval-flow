<?php

namespace App\Filament\Resources\CompanyResource\Pages;

use Filament\Actions;
use App\Traits\ApprovalFlow;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\CompanyResource;

class CreateCompany extends CreateRecord
{
    use ApprovalFlow;

    protected static string $resource = CompanyResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
