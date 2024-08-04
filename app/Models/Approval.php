<?php

namespace App\Models;

use App\Enums\ApprovalStatus;
use App\Services\ApprovalService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Approval extends Model
{
    use HasFactory;

    protected $casts = [
        'status' => ApprovalStatus::class,
        'data' => 'array',
    ];

    public function approvable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approve(array $data): void
    {
        $approvalService = new ApprovalService($this);
        $result = $approvalService->index();

        $this->status           = ApprovalStatus::APPROVED;
        $this->approver_id      = auth()->id();
        $this->approve_date     = now();
        $this->approver_comment = $data['approver_comment'];

        $this->save();
    }

    public function reject(array $data): void
    {
        $this->status           = ApprovalStatus::REJECTED;
        $this->approver_id      = auth()->id();
        $this->approve_date     = now();
        $this->approver_comment = $data['approver_comment'];

        $this->save();
    }

    protected function editRecord(array $data)
    {
        $model = $this->approvable_type;
        $model->update($this->data);

        return true;
    }

}
