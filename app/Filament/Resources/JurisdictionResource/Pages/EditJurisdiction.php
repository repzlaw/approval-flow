<?php

namespace App\Filament\Resources\JurisdictionResource\Pages;

use Filament\Actions;
use App\Traits\ApprovalFlow;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\JurisdictionResource;

class EditJurisdiction extends EditRecord
{
    use ApprovalFlow;
    
    protected static string $resource = JurisdictionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
