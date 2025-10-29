<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Certificate - {{ $certificate->number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Times New Roman', serif;
            background: white;
            color: #000;
            padding: 20px;
        }

        .certificate-container {
            max-width: 900px;
            margin: 0 auto;
        }

        .print-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .print-buttons {
            margin: 20px 0;
        }

        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            margin: 0 10px;
            display: inline-block;
        }

        .btn-print {
            background: #3498db;
            color: white;
        }

        .btn-print:hover {
            background: #2980b9;
        }

        .btn-back {
            background: #95a5a6;
            color: white;
        }

        .btn-back:hover {
            background: #7f8c8d;
            text-decoration: none;
            color: white;
        }

        .certificate-frame {
            border: 8px solid #2c3e50;
            padding: 60px;
            text-align: center;
            position: relative;
            min-height: 500px;
            margin: 20px 0;
        }

        .certificate-number {
            position: absolute;
            top: 20px;
            right: 30px;
            background: #3498db;
            color: white;
            padding: 10px 18px;
            border-radius: 12px;
            font-weight: bold;
            font-size: 16px;
        }

        .certificate-title {
            font-size: 48px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 30px;
            text-transform: uppercase;
            letter-spacing: 4px;
        }

        .decorative-line {
            width: 200px;
            height: 4px;
            background: #3498db;
            margin: 25px auto;
        }

        .certificate-subtitle {
            font-size: 22px;
            color: #7f8c8d;
            margin-bottom: 30px;
            font-style: italic;
        }

        .student-name {
            font-size: 40px;
            font-weight: bold;
            color: #e74c3c;
            margin: 30px 0;
            padding: 18px;
            border-bottom: 4px solid #3498db;
            display: inline-block;
        }

        .certificate-text {
            font-size: 20px;
            color: #2c3e50;
            margin: 25px 0;
            line-height: 1.7;
        }

        .course-name {
            font-size: 32px;
            color: #27ae60;
            font-weight: 600;
            margin: 30px 0;
        }

        .certificate-date {
            position: absolute;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 18px;
            color: #7f8c8d;
            font-style: italic;
        }

        .seal-area {
            position: absolute;
            bottom: 40px;
            left: 50px;
            width: 80px;
            height: 80px;
            border: 3px solid #3498db;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            color: #3498db;
            font-weight: bold;
            text-align: center;
        }

        .signature-area {
            position: absolute;
            bottom: 50px;
            right: 60px;
            width: 180px;
            border-bottom: 2px solid #2c3e50;
            text-align: center;
            padding-top: 5px;
            font-size: 14px;
            color: #7f8c8d;
        }

        @media print {
            .print-header,
            .print-buttons {
                display: none !important;
            }

            body {
                padding: 0;
            }

            .certificate-frame {
                border: 5px solid #000;
                margin: 0;
                page-break-inside: avoid;
            }

            .certificate-container {
                max-width: none;
            }
        }

        @media (max-width: 768px) {
            .certificate-frame {
                padding: 30px 20px;
            }

            .certificate-title {
                font-size: 32px;
            }

            .student-name {
                font-size: 28px;
            }

            .course-name {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="certificate-container">

        {{-- PRINT HEADER --}}
        <div class="print-header">
            <h2>Certificate Print Preview</h2>
            <p>Certificate Number: <strong>{{ $certificate->number }}</strong></p>
        </div>

        {{-- PRINT BUTTONS --}}
        <div class="print-buttons">
            <button onclick="window.print()" class="btn btn-print">
                🖨️ Print Certificate
            </button>
            <a href="javascript:history.back()" class="btn btn-back">
                ← Back to Certificate
            </a>
        </div>

        {{-- CERTIFICATE FRAME --}}
        <div class="certificate-frame">

            <div class="certificate-number">{{ $certificate->number }}</div>

            <div class="certificate-title">Certificate of Completion</div>
            <div class="decorative-line"></div>
            <div class="certificate-subtitle">This is to certify that</div>

            <div class="student-name">
                {{ $certificate->student->name ?? 'Unknown Student' }}
            </div>

            <div class="certificate-text">has successfully completed the course</div>

            <div class="course-name">
                {{ $certificate->course->name ?? 'General Certificate' }}
            </div>

            <div class="decorative-line"></div>

            <div class="certificate-text">
                and is hereby awarded this certificate in recognition<br>
                of their dedication and outstanding achievement.
            </div>

            <div class="certificate-date">
                Issued on {{ $certificate->issued_at ? $certificate->issued_at->format('F d, Y') : 'Not specified' }}
            </div>

            <div class="seal-area">
                OFFICIAL<br>SEAL
            </div>

            <div class="signature-area">
                Authorized Signature
            </div>

        </div>
    </div>

    <script>
        // Auto-focus print dialog when page loads
        window.onload = function() {
            document.querySelector('.btn-print').focus();
        }

        // Keyboard shortcut for printing
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.key === 'p') {
                e.preventDefault();
                window.print();
            }
        });
    </script>
</body>
</html>
