<?php

namespace App\Traits;

use App\Models\Approval;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

trait ApprovalFlow
{
    protected function handleRecordCreation(array $data): Model
    {
        return Approval::create([
            'approvable_type' => static::getModel(),
            'user_id'         => Auth::id(),
            'status'          => 'Submitted',
            'operation'       => 'Create',
            'data'            => $data,
        ]);
        
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        return Approval::updateOrCreate(
            [
                'approvable_type' => static::getModel(),
                'approvable_id'   => $record['id'],
                'status'          => 'Submitted',
                'operation'       => 'Edit',
                'user_id'         => Auth::id(),
            ],
            [
                'data' => $data,
            ]
        );
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Awaiting Admin Approval';
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Awaiting Admin Approval';
    }

}