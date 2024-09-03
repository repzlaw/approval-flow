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

        // get approvable relationships
        $modelClass = static::getModel(); 
        $model = new $modelClass(); 

        $approvable_relationships = [];
        $relationships = [];

        if (property_exists($model, 'approvable_relationships')) {
            $reflector = new ReflectionClass($model);
            $property = $reflector->getProperty('approvable_relationships');
            $property->setAccessible(true);
            $approvable_relationships = $property->getValue($model);
        }

        foreach ($approvable_relationships as $foreignKey => $relatedModelClass) {
            if (isset($data[$foreignKey]) && $data[$foreignKey] != null) {
                $relatedRecord = $relatedModelClass::find($data[$foreignKey]);
                if ($relatedRecord) {
                    $recordData = $relatedRecord->toArray();
                    $firstThreeKeys = array_slice($recordData, 0, 2, true);
                    $relationships[$foreignKey] = $firstThreeKeys;
                } else {
                    $relationships[$foreignKey] = null;
                }
            }
        }

        $array = [
            'new' => $data,
            'new_relationships' => $relationships,
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

            // get approvable relationships
            $modelClass = static::getModel(); 
            $model = new $modelClass(); 

            $approvable_relationships = [];
            $oldRelationships = [];
            $newRelationships = [];

            if (property_exists($model, 'approvable_relationships')) {
                $reflector = new ReflectionClass($model);
                $property = $reflector->getProperty('approvable_relationships');
                $property->setAccessible(true);
                $approvable_relationships = $property->getValue($model);
            }

            foreach ($approvable_relationships as $foreignKey => $relatedModelClass) {

                //old relationships
                if (isset($oldData[$foreignKey]) && $oldData[$foreignKey] != null) {
                    $oldRelatedRecord = $relatedModelClass::find($oldData[$foreignKey]);
                    if ($oldRelatedRecord) {
                        $oldRecordData = $oldRelatedRecord->toArray();
                        $oldFirstThreeKeys = array_slice($oldRecordData, 0, 2, true);
                        $oldRelationships[$foreignKey] = $oldFirstThreeKeys;
                    } else {
                        $oldRelationships[$foreignKey] = null;
                    }
                }

                //new relationships
                if (isset($newData[$foreignKey]) && $newData[$foreignKey] != null) {
                    $newRelatedRecord = $relatedModelClass::find($newData[$foreignKey]);
                    if ($newRelatedRecord) {
                        $newRecordData = $newRelatedRecord->toArray();
                        $newFirstTwoKeys = array_slice($newRecordData, 0, 2, true);
                        $newRelationships[$foreignKey] = $newFirstTwoKeys;
                    } else {
                        $newRelationships[$foreignKey] = null;
                    }
                }

                
            }
            
            $array = [
                'old' => $oldData,
                'new' => $newData,
                'old_relationships' => $oldRelationships,
                'new_relationships' => $newRelationships,
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
