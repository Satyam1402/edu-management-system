<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Certificate - {{ $certificate->number }}</title>
    <style>
        @page {
            margin: 0;
            size: A4 landscape;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background: #ffffff;
            width: 297mm;
            height: 210mm;
        }
        
        .certificate-container {
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 15mm;
            position: relative;
            overflow: hidden;
        }
        
        /* Decorative corner elements */
        .corner {
            position: absolute;
            width: 60px;
            height: 60px;
            border-style: solid;
            border-color: #28a745;
        }
        
        .corner-tl {
            top: 10mm;
            left: 10mm;
            border-width: 3px 0 0 3px;
        }
        
        .corner-tr {
            top: 10mm;
            right: 10mm;
            border-width: 3px 3px 0 0;
        }
        
        .corner-bl {
            bottom: 10mm;
            left: 10mm;
            border-width: 0 0 3px 3px;
        }
        
        .corner-br {
            bottom: 10mm;
            right: 10mm;
            border-width: 0 3px 3px 0;
        }
        
        .certificate-inner {
            background: #ffffff;
            border: 6px solid #28a745;
            border-radius: 20px;
            padding: 30px;
            text-align: center;
            position: relative;
            height: 100%;
            box-shadow: 0 0 30px rgba(40, 167, 69, 0.3);
        }
        
        /* Header Section */
        .certificate-header {
            position: relative;
            margin-bottom: 10px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e9ecef;
        }
        
        .certificate-badge {
            display: inline-block;
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 8px 25px;
            border-radius: 25px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 10px;
        }
        
        .certificate-number {
            position: absolute;
            top: 5px;
            left: 0;
            font-family: 'Courier New', monospace;
            font-size: 10px;
            color: #6c757d;
            background: #f8f9fa;
            padding: 5px 12px;
            border-radius: 15px;
            border: 1px solid #dee2e6;
        }
        
        .organization-name {
            font-size: 18px;
            font-weight: bold;
            color: #343a40;
            margin: 8px 0;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        
        /* Main Content */
        .certificate-icon {
            font-size: 48px;
            margin: 15px 0;
            color: #28a745;
        }
        
        .certificate-title {
            font-size: 32px;
            font-weight: bold;
            color: #28a745;
            margin: 15px 0;
            letter-spacing: 3px;
            text-transform: uppercase;
        }
        
        .certificate-subtitle {
            font-size: 13px;
            color: #6c757d;
            margin: 10px 0;
            font-style: italic;
        }
        
        .certificate-text {
            font-size: 14px;
            color: #495057;
            margin: 10px 0;
            line-height: 1.6;
        }
        
        .student-name {
            font-size: 36px;
            font-weight: bold;
            color: #007bff;
            margin: 20px 0;
            padding: 10px 0;
            border-bottom: 3px solid #007bff;
            display: inline-block;
            min-width: 400px;
        }
        
        .course-name {
            font-size: 22px;
            font-weight: bold;
            color: #28a745;
            margin: 15px 0;
            padding: 8px 20px;
            background: #e8f5e9;
            border-radius: 10px;
            display: inline-block;
        }
        
        /* Footer Section */
        .certificate-footer {
            position: absolute;
            bottom: 20px;
            left: 30px;
            right: 30px;
            border-top: 2px solid #e9ecef;
            padding-top: 15px;
        }
        
        .footer-grid {
            display: table;
            width: 100%;
            margin-top: 10px;
        }
        
        .footer-col {
            display: table-cell;
            width: 33.33%;
            text-align: center;
            vertical-align: top;
            padding: 0 10px;
        }
        
        .footer-label {
            font-size: 10px;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }
        
        .footer-value {
            font-size: 12px;
            color: #343a40;
            font-weight: bold;
        }
        
        .signature-section {
            margin-top: 15px;
            padding-top: 10px;
            border-top: 1px dashed #dee2e6;
        }
        
        .signature-line {
            border-top: 2px solid #343a40;
            width: 180px;
            margin: 5px auto;
        }
        
        .signature-label {
            font-size: 10px;
            color: #6c757d;
            margin-top: 3px;
            font-weight: bold;
        }
        
        /* Watermark */
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 120px;
            color: rgba(40, 167, 69, 0.05);
            font-weight: bold;
            z-index: 0;
            letter-spacing: 5px;
        }
        
        .content {
            position: relative;
            z-index: 1;
        }
        
        /* Verification Section */
        .verification-box {
            position: absolute;
            top: 5px;
            right: 0;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 10px;
            padding: 8px 12px;
            text-align: right;
            font-size: 9px;
            color: #6c757d;
        }
        
        .verification-box .qr-placeholder {
            width: 50px;
            height: 50px;
            background: #e9ecef;
            border: 1px dashed #adb5bd;
            border-radius: 5px;
            margin: 5px auto;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 8px;
            color: #6c757d;
        }
        
        /* Franchise Badge */
        .franchise-badge {
            display: inline-block;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 5px 15px;
            border-radius: 15px;
            font-size: 11px;
            color: #495057;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="certificate-container">
        <!-- Decorative Corners -->
        <div class="corner corner-tl"></div>
        <div class="corner corner-tr"></div>
        <div class="corner corner-bl"></div>
        <div class="corner corner-br"></div>
        
        <div class="certificate-inner">
            <!-- Watermark -->
            <div class="watermark">CERTIFIED</div>
            
            <div class="content">
                <!-- Header -->
                <div class="certificate-header">
                    <div class="certificate-badge">VERIFIED CERTIFICATE</div>
                    <div class="certificate-number">{{ $certificate->number }}</div>
                    
                    @if($certificate->franchise)
                    <div class="verification-box">
                        <strong>Issued By:</strong><br>
                        {{ $certificate->franchise->name }}
                        <div class="qr-placeholder">QR</div>
                    </div>
                    @endif
                    
                    <div class="organization-name">
                        Education Management System
                    </div>
                    <div class="certificate-subtitle">
                        Professional Course Completion Certificate
                    </div>
                </div>
                
                <!-- Icon -->
                <div class="certificate-icon">🎓</div>
                
                <!-- Title -->
                <h1 class="certificate-title">Certificate of Completion</h1>
                
                <!-- Content -->
                <p class="certificate-text">This is to certify that</p>
                
                <div class="student-name">
                    {{ $certificate->student->name ?? 'N/A' }}
                </div>
                
                <p class="certificate-text">
                    has successfully completed the course requirements for
                </p>
                
                <div class="course-name">
                    {{ $certificate->course->name ?? 'N/A' }}
                </div>
                
                @if($certificate->franchise)
                <div class="franchise-badge">
                    <strong>Training Partner:</strong> {{ $certificate->franchise->name }}
                </div>
                @endif
                
                <!-- Footer -->
                <div class="certificate-footer">
                    <div class="footer-grid">
                        <div class="footer-col">
                            <div class="footer-label">Certificate ID</div>
                            <div class="footer-value">CERT-{{ $certificate->number }}</div>
                        </div>
                        
                        <div class="footer-col">
                            <div class="footer-label">Issue Date</div>
                            <div class="footer-value">
                                {{ $certificate->issued_at ? $certificate->issued_at->format('F d, Y') : now()->format('F d, Y') }}
                            </div>
                        </div>
                        
                        <div class="footer-col">
                            <div class="footer-label">Status</div>
                            <div class="footer-value" style="color: #28a745;">
                                {{ strtoupper($certificate->status) }}
                            </div>
                        </div>
                    </div>
                    
                    @if($certificate->status === 'issued' && $certificate->issuedBy)
                    <div class="signature-section">
                        <div class="signature-line"></div>
                        <div class="signature-label">
                            {{ $certificate->issuedBy->name ?? 'Authorized Officer' }}
                        </div>
                        <div class="signature-label" style="color: #adb5bd; font-weight: normal;">
                            Authorized Signature
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</body>
</html>
