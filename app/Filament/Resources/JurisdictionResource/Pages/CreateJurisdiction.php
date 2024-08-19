<?php

namespace App\Filament\Resources\JurisdictionResource\Pages;

use Filament\Actions;
use App\Traits\ApprovalFlow;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\JurisdictionResource;

class CreateJurisdiction extends CreateRecord
{
    use ApprovalFlow;
    
    protected static string $resource = JurisdictionResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
