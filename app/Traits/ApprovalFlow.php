<?php

namespace App\Traits;

use ReflectionClass;
use App\Models\Approval;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

trait ApprovalFlow
{
    protected $noApprovalRequired = false;

    protected function handleRecordCreation(array $data): Model
    {
        if (auth()->user()->can('approve_approval')) {
            return static::getModel()::create($data);
        }

        $array = [
            'new' => $data,
        ];

        return Approval::create([
            'approvable_type' => static::getModel(),
            'user_id'         => Auth::id(),
            'status'          => 'Submitted',
            'operation'       => 'Create',
            'data'            => $array,
        ]);
        
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        if (auth()->user()->can('approve_approval')) {
            $record->update($data);
 
            return $record;
        }

        $approvable = [];
        $original = $record->getOriginal();

        if (property_exists($record, 'approvable')) {
            $reflector = new ReflectionClass($record);
            $property  = $reflector->getProperty('approvable');
            $property->setAccessible(true);
            $approvable = $property->getValue($record);
        }

        $oldData = [];
        $newData = [];
        $approvableData = [];

        foreach ($data as $key => $value) {
            if (isset($original[$key]) && $original[$key] != $value) {
                if (in_array($key, $approvable)) {
                    $approvableData[$key] = $value;
                } else {
                    $oldData[$key] = $original[$key];
                    $newData[$key] = $value;
                }
            }
        }

        if (!empty($approvableData)) {
            $record->update($approvableData);
        }

        if (!empty($newData)) {
            $array = [
                'old' => $oldData,
                'new' => $newData,
            ];

            return Approval::updateOrCreate(
                [
                    'approvable_type' => static::getModel(),
                    'approvable_id'   => $record->id,
                    'status'          => 'Submitted',
                    'operation'       => 'Edit',
                    'user_id'         => Auth::id(),
                ],
                [
                    'data' => $array,
                ]
            );
        } else {
            $this->noApprovalRequired = true;
        }

        return $record;
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        if (auth()->user()->can('approve_approval') || $this->noApprovalRequired) {
            return 'Successful';
        }
        return 'Awaiting Admin Approval';
    }

    protected function getSavedNotificationTitle(): ?string
    {
        if (auth()->user()->can('approve_approval') || $this->noApprovalRequired) {
            return 'Successful';
        }
        return 'Awaiting Admin Approval';
    }

}