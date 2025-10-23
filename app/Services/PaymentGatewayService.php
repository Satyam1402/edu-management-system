<?php

namespace App\Services;

use Razorpay\Api\Api;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\Payment;

class PaymentGatewayService
{
    private $razorpay;

    public function __construct()
    {
        $this->razorpay = new Api(
            config('services.razorpay.key'), 
            config('services.razorpay.secret')
        );
    }

    /**
     * Create Razorpay Order
     */
    public function createRazorpayOrder($amount, $currency = 'INR')
    {
        try {
            $orderData = [
                'receipt' => 'order_' . time(),
                'amount' => $amount * 100, // Amount in paise
                'currency' => $currency
            ];

            $razorpayOrder = $this->razorpay->order->create($orderData);
            
            return [
                'success' => true,
                'order_id' => $razorpayOrder['id'],
                'amount' => $razorpayOrder['amount'],
                'currency' => $razorpayOrder['currency']
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Verify Razorpay Payment
     */
    public function verifyRazorpayPayment($paymentId, $orderId, $signature)
    {
        try {
            $attributes = [
                'razorpay_order_id' => $orderId,
                'razorpay_payment_id' => $paymentId,
                'razorpay_signature' => $signature
            ];

            $this->razorpay->utility->verifyPaymentSignature($attributes);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Generate UPI QR Code
     */
    public function generateUpiQrCode($amount, $name, $note = 'Payment')
    {
    $upiId = config('services.upi.id');
            $businessName = config('services.upi.business_name');

            // UPI URL format
            $upiUrl = "upi://pay?pa={$upiId}&pn={$businessName}&am={$amount}&cu=INR&tn={$note}";

            // Generate QR Code with SVG format (no ImageMagick needed)
            $qrCode = QrCode::size(250)
                            ->format('svg')  // Changed from 'png' to 'svg'
                            ->generate($upiUrl);

            return [
                'qr_code' => base64_encode($qrCode),
                'upi_url' => $upiUrl,
                'upi_id' => $upiId,
                'format' => 'svg'  // Added format info
            ];
    }


    /**
     * Create Payment Link for UPI
     */
    public function createUpiPaymentLink($amount, $note = 'Payment')
    {
        $upiId = config('services.upi.id');
        $businessName = config('services.upi.business_name');
        
        return "upi://pay?pa={$upiId}&pn={$businessName}&am={$amount}&cu=INR&tn={$note}";
    }
}
