<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate - {{ $certificate->number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Times New Roman', serif;
            background: #f5f5f5;
            padding: 20px;
        }

        .certificate-container {
            max-width: 800px;
            margin: 0 auto;
        }

        .certificate-frame {
            background: white;
            border: 8px solid #2c3e50;
            border-radius: 15px;
            padding: 60px;
            text-align: center;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            min-height: 500px;
            position: relative;
        }

        .certificate-number {
            position: absolute;
            top: 20px;
            right: 30px;
            background: #3498db;
            color: white;
            padding: 8px 15px;
            border-radius: 15px;
            font-weight: bold;
            font-size: 1rem;
        }

        .certificate-title {
            font-size: 3rem;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 30px;
            text-transform: uppercase;
            letter-spacing: 3px;
        }

        .decorative-line {
            width: 150px;
            height: 4px;
            background: #3498db;
            margin: 25px auto;
        }

        .certificate-subtitle {
            font-size: 1.4rem;
            color: #7f8c8d;
            margin-bottom: 30px;
            font-style: italic;
        }

        .student-name {
            font-size: 2.5rem;
            font-weight: bold;
            color: #e74c3c;
            margin: 30px 0;
            padding: 15px;
            border-bottom: 4px solid #3498db;
            display: inline-block;
        }

        .certificate-text {
            font-size: 1.3rem;
            color: #2c3e50;
            margin: 25px 0;
            line-height: 1.7;
        }

        .course-name {
            font-size: 2rem;
            color: #27ae60;
            font-weight: 600;
            margin: 30px 0;
        }

        .certificate-date {
            position: absolute;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 1.1rem;
            color: #7f8c8d;
            font-style: italic;
        }

        /* ONLY ONE BUTTON SET - NO DUPLICATES */
        .action-buttons {
            text-align: center;
            margin-top: 30px;
        }

        .btn {
            padding: 15px 40px;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
            text-decoration: none;
            margin: 0 15px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-block;
        }

        .btn-close {
            background: #95a5a6;
            color: white;
        }

        .btn-close:hover {
            background: #7f8c8d;
            color: white;
            transform: translateY(-2px);
        }

        .btn-download {
            background: #27ae60;
            color: white;
        }

        .btn-download:hover {
            background: #2ecc71;
            color: white;
            transform: translateY(-2px);
        }

        @media print {
            .action-buttons {
                display: none !important;
            }
        }
    </style>
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="certificate-container">

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
                of their dedication and achievement.
            </div>

            <div class="certificate-date">
                Issued on {{ $certificate->issued_at ? $certificate->issued_at->format('F d, Y') : 'Not specified' }}
            </div>
        </div>

        {{-- SINGLE SET OF BUTTONS - NO DUPLICATES --}}
        <div class="action-buttons">
            <a href="javascript:history.back()" class="btn btn-close">
                Close
            </a>
            <a href="{{ route('franchise.certificates.download', $certificate->id) }}" class="btn btn-download">
                <i class="fas fa-download" style="margin-right: 8px;"></i>Download
            </a>
        </div>
    </div>
</body>
</html>
