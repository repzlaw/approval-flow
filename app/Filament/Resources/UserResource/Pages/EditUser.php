<?php

namespace App\Filament\Resources\UserResource\Pages;

use Filament\Actions;
use App\Traits\ApprovalFlow;
use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    use ApprovalFlow;
    
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function beforeDelete(): void
    {
        dd(21);
    }

    public function delete(): void
    {
        dd(32);
    }
}
