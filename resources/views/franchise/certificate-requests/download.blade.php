<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate - {{ $certificateRequest->certificate_number }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 50px 0;
        }
        .certificate-container {
            background: white;
            border: 15px solid #667eea;
            border-radius: 10px;
            padding: 60px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
            position: relative;
        }
        .certificate-container::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 20px;
            right: 20px;
            bottom: 20px;
            border: 2px solid #764ba2;
            pointer-events: none;
        }
        .certificate-header {
            text-align: center;
            margin-bottom: 40px;
        }
        .certificate-title {
            font-size: 48px;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 3px;
        }
        .certificate-subtitle {
            font-size: 20px;
            color: #666;
            margin-bottom: 30px;
        }
        .student-name {
            font-size: 42px;
            font-weight: bold;
            color: #333;
            font-family: 'Brush Script MT', cursive;
            margin: 30px 0;
            border-bottom: 3px solid #667eea;
            display: inline-block;
            padding-bottom: 10px;
        }
        .course-name {
            font-size: 32px;
            color: #28a745;
            font-weight: bold;
            margin: 20px 0;
        }
        .certificate-body {
            text-align: center;
            margin: 40px 0;
        }
        .certificate-info {
            margin: 40px 0;
            display: flex;
            justify-content: space-around;
        }
        .info-box {
            text-align: center;
        }
        .info-label {
            font-size: 14px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .info-value {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            margin-top: 5px;
        }
        .signature-section {
            margin-top: 60px;
            display: flex;
            justify-content: space-around;
        }
        .signature-box {
            text-align: center;
        }
        .signature-line {
            border-top: 2px solid #333;
            width: 200px;
            margin: 0 auto 10px;
            padding-top: 10px;
        }
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 120px;
            color: rgba(102, 126, 234, 0.05);
            pointer-events: none;
            font-weight: bold;
        }
        .action-buttons {
            text-align: center;
            margin-top: 30px;
        }
        @media print {
            body {
                background: white;
                padding: 0;
            }
            .action-buttons {
                display: none;
            }
            .certificate-container {
                border: 15px solid #667eea;
                box-shadow: none;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="certificate-container">
            <div class="watermark">VERIFIED</div>

            <!-- Header -->
            <div class="certificate-header">
                <div class="mb-3">
                    <i class="fas fa-award fa-5x text-warning"></i>
                </div>
                <h1 class="certificate-title">Certificate</h1>
                <p class="certificate-subtitle">of Course Completion</p>
            </div>

            <!-- Body -->
            <div class="certificate-body">
                <p style="font-size: 18px; color: #666;">This is to certify that</p>

                <div class="student-name">
                    {{ $certificateRequest->student->full_name }}
                </div>

                <p style="font-size: 18px; color: #666; margin-top: 30px;">
                    has successfully completed the course
                </p>

                <div class="course-name">
                    {{ $certificateRequest->course->name }}
                </div>

                <p style="font-size: 16px; color: #666; margin-top: 20px;">
                    {{ $certificateRequest->course->description ?? 'Course completion certificate' }}
                </p>
            </div>

            <!-- Certificate Info -->
            <div class="certificate-info">
                <div class="info-box">
                    <div class="info-label">Certificate Number</div>
                    <div class="info-value">{{ $certificateRequest->certificate_number }}</div>
                </div>
                <div class="info-box">
                    <div class="info-label">Issue Date</div>
                    <div class="info-value">
                        {{ $certificateRequest->completed_at
                            ? $certificateRequest->completed_at->format('d F Y')
                            : '—' }}
                    </div>
                </div>
                <div class="info-box">
                    <div class="info-label">Valid Until</div>
                    <div class="info-value">Lifetime</div>
                </div>
            </div>

            <!-- Signatures -->
            <div class="signature-section">
                <div class="signature-box">
                    <div class="signature-line">
                        <i class="fas fa-signature fa-2x text-primary"></i>
                    </div>
                    <strong>Authorized Signature</strong><br>
                    <small class="text-muted">Program Director</small>
                </div>
                <div class="signature-box">
                    <div class="signature-line">
                        <i class="fas fa-stamp fa-2x text-success"></i>
                    </div>
                    <strong>{{ $certificateRequest->franchise->name ?? 'Institute' }}</strong><br>
                    <small class="text-muted">Issuing Authority</small>
                </div>
            </div>

            <!-- QR Code or Seal (Optional) -->
            <div style="text-align: center; margin-top: 40px;">
                <small class="text-muted">
                    <i class="fas fa-shield-alt mr-1"></i>
                    This certificate is digitally verified and authenticated
                </small>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons">
            <a href="{{ route('franchise.certificate-requests.index') }}" class="btn btn-secondary btn-lg">
                <i class="fas fa-arrow-left mr-2"></i>Back to Requests
            </a>
            <button onclick="window.print()" class="btn btn-primary btn-lg ml-2">
                <i class="fas fa-print mr-2"></i>Print Certificate
            </button>
            <button onclick="downloadAsPDF()" class="btn btn-success btn-lg ml-2">
                <i class="fas fa-download mr-2"></i>Download PDF
            </button>
        </div>
    </div>

    <script>
        function downloadAsPDF() {
            // For now, just print (user can choose "Save as PDF")
            window.print();
            alert('Use your browser\'s "Save as PDF" option after Print.');
        }
    </script>
</body>
</html>
