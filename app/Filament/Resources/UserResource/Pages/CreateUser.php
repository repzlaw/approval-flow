<?php

namespace App\Filament\Resources\UserResource\Pages;

use Filament\Actions;
use Illuminate\Database\Eloquent\Model;
use App\Filament\Resources\UserResource;
use App\Traits\ApprovalFlow;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    use ApprovalFlow;

    protected static string $resource = UserResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

}
