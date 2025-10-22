<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id', 'student_id', 'course_id', 'amount', 'currency',
        'status', 'gateway', 'gateway_order_id', 'gateway_payment_id',
        'gateway_response', 'paid_at'
    ];

    protected $casts = [
        'gateway_response' => 'array',
        'paid_at' => 'datetime'
    ];

    // Boot method to auto-generate order ID
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($payment) {
            if (!$payment->order_id) {
                $payment->order_id = 'ORD' . strtoupper(uniqid());
            }
        });
    }

    // Relationships
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    // Accessors
    public function getFormattedAmountAttribute()
    {
        return '₹' . number_format($this->amount, 2);
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => 'warning',
            'completed' => 'success',
            'failed' => 'danger',
            'refunded' => 'info'
        ];

        return $badges[$this->status] ?? 'secondary';
    }

    // Methods
    public function markAsCompleted($gatewayPaymentId, $gatewayResponse = null)
    {
        $this->update([
            'status' => 'completed',
            'gateway_payment_id' => $gatewayPaymentId,
            'gateway_response' => $gatewayResponse,
            'paid_at' => now()
        ]);
    }

    public function markAsFailed($gatewayResponse = null)
    {
        $this->update([
            'status' => 'failed',
            'gateway_response' => $gatewayResponse
        ]);
    }

    // Static Methods
    public static function getStatusOptions()
    {
        return [
            'pending' => 'Pending',
            'completed' => 'Completed',
            'failed' => 'Failed',
            'refunded' => 'Refunded'
        ];
    }
}
