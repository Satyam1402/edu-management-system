<?php

namespace App\Http\Controllers\Franchise;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Certificate;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display franchise dashboard with real-time stats
     */
    public function index()
    {
        // Get current franchise ID from authenticated user
        $franchiseId = Auth::user()->franchise_id;
        
        // Calculate all statistics
        $stats = [
            // Student Statistics
            'total_students' => Student::where('franchise_id', $franchiseId)->count(),
            'active_students' => Student::where('franchise_id', $franchiseId)
                                       ->where('status', 'active')
                                       ->count(),
            'graduated_students' => Student::where('franchise_id', $franchiseId)
                                          ->where('status', 'graduated')
                                          ->count(),
            'dropped_students' => Student::where('franchise_id', $franchiseId)
                                        ->where('status', 'dropped')
                                        ->count(),
            
            // Certificate Statistics
            'total_certificates' => Certificate::whereHas('student', function($query) use ($franchiseId) {
                                                  $query->where('franchise_id', $franchiseId);
                                              })->count(),
            'pending_certificates' => Certificate::whereHas('student', function($query) use ($franchiseId) {
                                                    $query->where('franchise_id', $franchiseId);
                                                })->where('status', 'pending')->count(),
            'approved_certificates' => Certificate::whereHas('student', function($query) use ($franchiseId) {
                                                     $query->where('franchise_id', $franchiseId);
                                                 })->where('status', 'approved')->count(),
            'rejected_certificates' => Certificate::whereHas('student', function($query) use ($franchiseId) {
                                                     $query->where('franchise_id', $franchiseId);
                                                 })->where('status', 'rejected')->count(),
            
            // Payment Statistics
            'total_payments' => Payment::whereHas('student', function($query) use ($franchiseId) {
                                       $query->where('franchise_id', $franchiseId);
                                   })->sum('amount'),
            'completed_payments' => Payment::whereHas('student', function($query) use ($franchiseId) {
                                           $query->where('franchise_id', $franchiseId);
                                       })->where('status', 'completed')->sum('amount'),
            'pending_payments' => Payment::whereHas('student', function($query) use ($franchiseId) {
                                         $query->where('franchise_id', $franchiseId);
                                     })->where('status', 'pending')->sum('amount'),
            'pending_payments_count' => Payment::whereHas('student', function($query) use ($franchiseId) {
                                               $query->where('franchise_id', $franchiseId);
                                           })->where('status', 'pending')->count(),
        ];
        
        // Recent Activities (Last 7 days)
        $recentStudents = Student::where('franchise_id', $franchiseId)
                                ->latest()
                                ->take(5)
                                ->get();
        
        $recentCertificates = Certificate::whereHas('student', function($query) use ($franchiseId) {
                                            $query->where('franchise_id', $franchiseId);
                                        })
                                        ->with(['student', 'course'])
                                        ->latest()
                                        ->take(5)
                                        ->get();
        
        $recentPayments = Payment::whereHas('student', function($query) use ($franchiseId) {
                                     $query->where('franchise_id', $franchiseId);
                                 })
                                 ->with(['student'])
                                 ->latest()
                                 ->take(5)
                                 ->get();
        
        // Chart Data - Monthly trend (last 6 months)
        $chartData = $this->getChartData($franchiseId);
        
        return view('franchise.dashboard', compact(
            'stats',
            'recentStudents',
            'recentCertificates',
            'recentPayments',
            'chartData'
        ));
    }
    
    /**
     * Get chart data for dashboard graphs
     */
    private function getChartData($franchiseId)
    {
        $months = [];
        $studentData = [];
        $certificateData = [];
        $paymentData = [];
        
        // Get last 6 months data
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $months[] = $date->format('M Y');
            
            // Students enrolled in this month
            $studentData[] = Student::where('franchise_id', $franchiseId)
                                   ->whereYear('created_at', $date->year)
                                   ->whereMonth('created_at', $date->month)
                                   ->count();
            
            // Certificates issued in this month
            $certificateData[] = Certificate::whereHas('student', function($query) use ($franchiseId) {
                                              $query->where('franchise_id', $franchiseId);
                                          })
                                          ->where('status', 'approved')
                                          ->whereYear('created_at', $date->year)
                                          ->whereMonth('created_at', $date->month)
                                          ->count();
            
            // Payments completed in this month
            $paymentData[] = Payment::whereHas('student', function($query) use ($franchiseId) {
                                    $query->where('franchise_id', $franchiseId);
                                })
                                ->where('status', 'completed')
                                ->whereYear('created_at', $date->year)
                                ->whereMonth('created_at', $date->month)
                                ->sum('amount');
        }
        
        return [
            'months' => $months,
            'students' => $studentData,
            'certificates' => $certificateData,
            'payments' => $paymentData
        ];
    }
}
