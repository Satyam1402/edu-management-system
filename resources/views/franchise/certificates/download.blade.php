@extends('layouts.custom-franchise')

@section('page-title', 'Download Certificate')

@section('css')
<style>
    .certificate-preview {
        background: white;
        border: 2px solid #ddd;
        padding: 40px;
        margin: 20px auto;
        max-width: 800px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    .certificate-header {
        text-align: center;
        margin-bottom: 30px;
    }
    .certificate-title {
        font-size: 2.5rem;
        font-weight: bold;
        color: #2c3e50;
        margin-bottom: 10px;
    }
    .certificate-number {
        font-size: 1.2rem;
        color: #7f8c8d;
        margin-bottom: 20px;
    }
    .certificate-body {
        margin: 40px 0;
        line-height: 2;
    }
    .student-name {
        font-size: 2rem;
        font-weight: bold;
        color: #3498db;
        border-bottom: 2px solid #3498db;
        display: inline-block;
        padding: 5px 20px;
    }
    .certificate-footer {
        margin-top: 60px;
        display: flex;
        justify-content: space-between;
    }
    .signature-section {
        text-align: center;
    }
    .signature-line {
        border-top: 2px solid #000;
        width: 200px;
        margin: 10px auto;
    }
    @media print {
        .no-print {
            display: none;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid">

    <!-- Header Actions -->
    <div class="row mb-4 no-print">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-0">Certificate Download</h4>
                    <p class="text-muted">Certificate Number: <strong>{{ $certificate->number }}</strong></p>
                </div>
                <div>
                    <a href="{{ route('franchise.certificate-requests.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Requests
                    </a>
                    <button onclick="window.print()" class="btn btn-primary">
                        <i class="fas fa-print"></i> Print Certificate
                    </button>
                    <button onclick="downloadPDF()" class="btn btn-success">
                        <i class="fas fa-download"></i> Download PDF
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Certificate Preview -->
    <div class="certificate-preview" id="certificate">

        <!-- Header -->
        <div class="certificate-header">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" style="height: 80px; margin-bottom: 20px;">
            <div class="certificate-title">CERTIFICATE OF COMPLETION</div>
            <div class="certificate-number">Certificate No: {{ $certificate->number }}</div>
        </div>

        <!-- Body -->
        <div class="certificate-body text-center">
            <p style="font-size: 1.2rem; margin-bottom: 30px;">This is to certify that</p>

            <div class="student-name">
                {{ $certificateRequest->student->full_name ?? 'N/A' }}
            </div>

            <p style="font-size: 1.2rem; margin-top: 30px; margin-bottom: 30px;">
                has successfully completed the course
            </p>

            <h3 style="color: #2c3e50; font-weight: bold;">
                {{ $certificateRequest->course->name ?? 'N/A' }}
            </h3>

            <p style="margin-top: 30px; font-size: 1rem; color: #7f8c8d;">
                Date of Completion: {{ $certificate->issued_at ? $certificate->issued_at->format('d F Y') : 'N/A' }}
            </p>
        </div>

        <!-- Footer -->
        <div class="certificate-footer">
            <div class="signature-section">
                <div class="signature-line"></div>
                <p><strong>Authorized Signature</strong></p>
                <p class="text-muted">Director</p>
            </div>

            <div class="signature-section">
                <div class="signature-line"></div>
                <p><strong>Date Issued</strong></p>
                <p class="text-muted">{{ $certificate->issued_at ? $certificate->issued_at->format('d M Y') : 'N/A' }}</p>
            </div>
        </div>

        <!-- Verification QR Code (Optional) -->
        <div class="text-center mt-5">
            <p class="text-muted small">
                Verify this certificate at: {{ url('/verify/' . $certificate->number) }}
            </p>
        </div>
    </div>

    <!-- Certificate Details -->
    <div class="row mt-4 no-print">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="fas fa-info-circle"></i> Certificate Details</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <th>Certificate Number:</th>
                            <td>{{ $certificate->number }}</td>
                        </tr>
                        <tr>
                            <th>Student Name:</th>
                            <td>{{ $certificateRequest->student->full_name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Course:</th>
                            <td>{{ $certificateRequest->course->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Issue Date:</th>
                            <td>{{ $certificate->issued_at ? $certificate->issued_at->format('d M Y, h:i A') : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Status:</th>
                            <td><span class="badge badge-success">{{ ucfirst($certificate->status) }}</span></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="fas fa-receipt"></i> Payment Details</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <th>Amount Paid:</th>
                            <td class="text-success font-weight-bold">₹{{ number_format($certificateRequest->amount, 2) }}</td>
                        </tr>
                        <tr>
                            <th>Payment Status:</th>
                            <td><span class="badge badge-success">Paid</span></td>
                        </tr>
                        <tr>
                            <th>Payment Date:</th>
                            <td>{{ $certificateRequest->paid_at ? $certificateRequest->paid_at->format('d M Y, h:i A') : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Request Date:</th>
                            <td>{{ $certificateRequest->created_at->format('d M Y, h:i A') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@section('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<script>
function downloadPDF() {
    const element = document.getElementById('certificate');
    const filename = 'Certificate_{{ $certificate->number }}.pdf';

    const opt = {
        margin: 10,
        filename: filename,
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2 },
        jsPDF: { unit: 'mm', format: 'a4', orientation: 'landscape' }
    };

    // Show loading
    Swal.fire({
        title: 'Generating PDF...',
        text: 'Please wait while we prepare your certificate',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    html2pdf().set(opt).from(element).save().then(() => {
        Swal.fire({
            icon: 'success',
            title: 'Download Complete!',
            text: 'Your certificate has been downloaded successfully.',
            timer: 2000,
            showConfirmButton: false
        });
    });
}
</script>
@endsection
