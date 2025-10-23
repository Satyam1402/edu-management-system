<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Certificate - {{ $certificate->number }}</title>
    <style>
        @page {
            margin: 1cm;
            size: A4 landscape;
        }
        
        body {
            font-family: 'DejaVu Sans', sans-serif;
            margin: 0;
            padding: 20px;
            background: #f8f9fa;
        }
        
        .certificate-container {
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 30px;
            box-sizing: border-box;
            position: relative;
        }
        
        .certificate-inner {
            background: white;
            border: 4px solid #28a745;
            border-radius: 15px;
            padding: 40px;
            text-align: center;
            position: relative;
            height: calc(100% - 60px);
            box-sizing: border-box;
        }
        
        .certificate-badge {
            position: absolute;
            top: -15px;
            right: 30px;
            background: #28a745;
            color: white;
            padding: 8px 20px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .certificate-number {
            position: absolute;
            top: 20px;
            left: 30px;
            font-family: 'Courier New', monospace;
            font-size: 12px;
            color: #666;
            background: rgba(40, 167, 69, 0.1);
            padding: 5px 10px;
            border-radius: 5px;
        }
        
        .certificate-icon {
            font-size: 60px;
            color: #28a745;
            margin: 20px 0;
        }
        
        .certificate-title {
            font-size: 28px;
            font-weight: bold;
            color: #28a745;
            margin: 30px 0;
            letter-spacing: 2px;
        }
        
        .certificate-text {
            font-size: 16px;
            color: #333;
            margin: 20px 0;
        }
        
        .student-name {
            font-size: 32px;
            font-weight: bold;
            color: #007bff;
            margin: 30px 0;
            text-decoration: underline;
        }
        
        .course-name {
            font-size: 24px;
            font-weight: bold;
            color: #28a745;
            margin: 20px 0;
        }
        
        .certificate-footer {
            margin-top: 40px;
            border-top: 2px solid #28a745;
            padding-top: 20px;
        }
        
        .footer-row {
            display: table;
            width: 100%;
            margin-top: 20px;
        }
        
        .footer-col {
            display: table-cell;
            width: 50%;
            text-align: left;
            vertical-align: top;
        }
        
        .footer-col:last-child {
            text-align: right;
        }
        
        .signature-line {
            border-top: 2px solid #28a745;
            width: 200px;
            margin: 30px auto 10px;
        }
        
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 100px;
            color: rgba(40, 167, 69, 0.1);
            font-weight: bold;
            z-index: 0;
        }
        
        .content {
            position: relative;
            z-index: 1;
        }
    </style>
</head>
<body>
    <div class="certificate-container">
        <div class="certificate-inner">
            <div class="watermark">CERTIFIED</div>
            
            <div class="content">
                <div class="certificate-badge">{{ ucfirst($certificate->status) }}</div>
                <div class="certificate-number">{{ $certificate->number }}</div>
                
                <div class="certificate-icon">🏆</div>
                
                <h1 class="certificate-title">CERTIFICATE OF COMPLETION</h1>
                
                <p class="certificate-text">This is to certify that</p>
                
                <h2 class="student-name">{{ $certificate->student->name ?? 'N/A' }}</h2>
                
                <p class="certificate-text">has successfully completed the course</p>
                
                <h3 class="course-name">{{ $certificate->course->name ?? 'N/A' }}</h3>
                
                <div class="certificate-footer">
                    <div class="footer-row">
                        <div class="footer-col">
                            <strong>Issue Date:</strong><br>
                            {{ $certificate->issued_at ? $certificate->issued_at->format('F d, Y') : now()->format('F d, Y') }}
                        </div>
                        <div class="footer-col">
                            <strong>Certificate ID:</strong><br>
                            CERT-{{ $certificate->number }}
                        </div>
                    </div>
                    
                    @if($certificate->status === 'issued')
                        <div class="signature-line"></div>
                        <strong>Authorized Signature</strong>
                    @endif
                </div>
            </div>
        </div>
    </div>
</body>
</html>
