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
        'gateway_response', 'paid_at', 'qr_data' // ✅ Added QR data support
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

    // ✅ New QR Code Scopes
    public function scopeQrPayments($query)
    {
        return $query->where('gateway', 'qr_code');
    }

    public function scopeManualPayments($query)
    {
        return $query->where('gateway', 'manual');
    }

    // Existing Accessors
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

    // ✅ New QR Code Accessors
    public function getIsQrPaymentAttribute()
    {
        return $this->gateway === 'qr_code' && !empty($this->qr_data);
    }

    public function getIsManualPaymentAttribute()
    {
        return $this->gateway === 'manual';
    }

    public function getPaymentMethodBadgeAttribute()
    {
        return $this->is_qr_payment ? 'success' : 'primary';
    }

    public function getPaymentMethodTextAttribute()
    {
        return $this->is_qr_payment ? 'QR Code' : 'Manual';
    }

    public function getParsedQrDataAttribute()
    {
        if (empty($this->qr_data)) {
            return null;
        }

        try {
            // Try to parse as JSON first
            $decoded = json_decode($this->qr_data, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $decoded;
            }

            // If not JSON, try UPI format
            if (str_contains($this->qr_data, 'upi://pay')) {
                return $this->parseUpiData($this->qr_data);
            }

            // Return as raw text
            return ['raw_data' => $this->qr_data];
        } catch (\Exception $e) {
            return ['error' => 'Could not parse QR data', 'raw_data' => $this->qr_data];
        }
    }

    // Existing Methods
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

    // ✅ New QR Code Methods
    public function markAsQrCompleted($qrData, $gatewayPaymentId = null)
    {
        $this->update([
            'status' => 'completed',
            'gateway' => 'qr_code',
            'qr_data' => $qrData,
            'gateway_payment_id' => $gatewayPaymentId ?: 'QR_' . time(),
            'paid_at' => now()
        ]);
    }

    private function parseUpiData($upiString)
    {
        try {
            $url = parse_url($upiString);
            if (!isset($url['query'])) {
                return ['upi_string' => $upiString];
            }

            parse_str($url['query'], $params);

            return [
                'type' => 'UPI Payment',
                'payee_address' => $params['pa'] ?? 'Not specified',
                'payee_name' => $params['pn'] ?? 'Not specified',
                'amount' => $params['am'] ?? 'Not specified',
                'transaction_note' => $params['tn'] ?? 'Payment',
                'currency' => $params['cu'] ?? 'INR',
                'merchant_code' => $params['mc'] ?? null
            ];
        } catch (\Exception $e) {
            return ['upi_string' => $upiString, 'parse_error' => $e->getMessage()];
        }
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

    // ✅ New Gateway Options
    public static function getGatewayOptions()
    {
        return [
            'manual' => 'Manual Payment',
            'qr_code' => 'QR Code Payment',
            'razorpay' => 'Razorpay',
            'paytm' => 'Paytm',
            'phonepe' => 'PhonePe'
        ];
    }

    // ✅ Payment Statistics
    public static function getPaymentStats($franchiseId = null)
    {
        $query = self::query();

        if ($franchiseId) {
            $query->whereHas('student', function($q) use ($franchiseId) {
                $q->where('franchise_id', $franchiseId);
            });
        }

        return [
            'total' => $query->count(),
            'completed' => $query->where('status', 'completed')->count(),
            'pending' => $query->where('status', 'pending')->count(),
            'failed' => $query->where('status', 'failed')->count(),
            'qr_payments' => $query->where('gateway', 'qr_code')->count(),
            'manual_payments' => $query->where('gateway', 'manual')->count(),
            'total_amount' => $query->where('status', 'completed')->sum('amount')
        ];
    }
}
