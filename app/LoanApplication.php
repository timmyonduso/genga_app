<?php

namespace App;

use App\Observers\LoanApplicationObserver;
use App\Traits\Auditable;
use App\Traits\MultiTenantModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use \DateTimeInterface;

class LoanApplication extends Model
{
    use SoftDeletes, MultiTenantModelTrait, Auditable;

    public $table = 'loan_applications';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'repayment_date',
    ];

    protected $fillable = [
        'loan_amount',
        'interest_rate',
        'repayment_date',
        'description',
        'analyst_id',
        'cfo_id',
        'created_at',
        'updated_at',
        'deleted_at',
        'created_by_id',
        'status_id',
        'overdue',
        'penalty_amount',
    ];

    protected static function booted()
    {
        self::observe(LoanApplicationObserver::class);
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id');
    }

    public function analyst()
    {
        return $this->belongsTo(User::class, 'analyst_id');
    }

    public function cfo()
    {
        return $this->belongsTo(User::class, 'cfo_id');
    }

    public function created_by()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function logs()
    {
        return $this->morphMany(AuditLog::class, 'subject');
    }

    function calculateInterest()
    {
        $daysDifference = $this->repayment_date->diffInDays(now());
        $interest = ($this->loan_amount + 20 * $daysDifference);
        $this->update(['interest_rate' => $interest]);
    }

    public function checkOverdue()
    {
        $this->overdue = (now() > $this->repayment_date);
    }

    public function applyPenalty()
    {
        $overdueDays = now()->diffInDays($this->repayment_date);
        $penaltyAmount = $overdueDays * 50 + $this->loan_amount; // Assuming a 10% penalty per overdue day

        $this->update(['penalty_amount' => $penaltyAmount]);
    }

    public function getOverdueLoans()
    {
        return self::where('repayment_date', '<', now())->get();
    }

    public function getIsOverdueAttribute()
    {
        return $this->overdue ? 'Yes' : 'No';
    }

    public function isOverdue()
    {
        // Check if the repayment date has passed
        return $this->repayment_date < now();
    }


}
