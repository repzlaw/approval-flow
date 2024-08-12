<?php

namespace App\Services;

class ApprovalService
{
    protected $approvable_type;
    protected $approvable_id; // Assumes you have this field as the identifier
    protected $operation;
    protected $data;

    public function __construct($approval)
    {
        $this->approvable_type = $approval->approvable_type;
        $this->approvable_id   = $approval->approvable_id;
        $this->operation       = $approval->operation;
        $this->data            = $approval->data;
    }

    public function index()
    {
        if ($this->operation === 'Create'){
            $this->createRecord();
        } elseif ($this->operation === 'Edit') {
            $this->editRecord();
        }  elseif ($this->operation === 'Delete') {
            $this->deleteRecord();
        }
    }

    protected function createRecord()
    {
        // Retrieve the model class from the approvable_type field
        $modelClass = $this->approvable_type;

        if (!class_exists($modelClass)) {
            throw new \Exception("Model class $modelClass does not exist.");
        }

        // Create a new record with the provided data
        $model = new $modelClass;
        $model->fill($this->data['new']);
        $model->save();

        return $model;
    }

    protected function editRecord()
    {
        // Retrieve the model class from the approvable_type field
        $modelClass = $this->approvable_type;

        if (!class_exists($modelClass)) {
            throw new \Exception("Model class $modelClass does not exist.");
        }

        // Find the record by ID
        $model = $modelClass::find($this->approvable_id);

        if (!$model) {
            throw new \Exception("Record not found.");
        }

        // Update the record with the provided data
        $model->update($this->data['new']);

        return true;
    }

    protected function deleteRecord()
    {
        // Retrieve the model class from the approvable_type field
        $modelClass = $this->approvable_type;

        if (!class_exists($modelClass)) {
            throw new \Exception("Model class $modelClass does not exist.");
        }

        // Find the record by ID
        $model = $modelClass::find($this->approvable_id);

        if (!$model) {
            throw new \Exception("Record not found.");
        }

        // Delete the record
        $model->delete();

        return true;
    }
}

