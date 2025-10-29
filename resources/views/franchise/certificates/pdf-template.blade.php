<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate - {{ $certificate->number }}</title>
    <style>
        @page {
            margin: 15mm;
            size: A4 landscape;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Times New Roman', serif;
            background: white;
            color: #000;
        }

        .certificate-container {
            width: 100%;
            height: 100vh;
            display: table;
        }

        .certificate-frame {
            display: table-cell;
            vertical-align: middle;
            text-align: center;
            border: 10px solid #2c3e50;
            padding: 50px;
            position: relative;
            min-height: 600px;
        }

        .certificate-number {
            position: absolute;
            top: 20px;
            right: 30px;
            background: #3498db;
            color: white;
            padding: 10px 20px;
            border-radius: 15px;
            font-weight: bold;
            font-size: 16px;
        }

        .certificate-title {
            font-size: 48px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 30px;
            text-transform: uppercase;
            letter-spacing: 5px;
        }

        .decorative-line {
            width: 250px;
            height: 5px;
            background: #3498db;
            margin: 25px auto;
        }

        .certificate-subtitle {
            font-size: 24px;
            color: #7f8c8d;
            margin-bottom: 30px;
            font-style: italic;
        }

        .student-name {
            font-size: 42px;
            font-weight: bold;
            color: #e74c3c;
            margin: 30px 0;
            padding: 20px;
            border-bottom: 5px solid #3498db;
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
            left: 60px;
            width: 100px;
            height: 100px;
            border: 3px solid #3498db;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            color: #3498db;
            font-weight: bold;
        }

        .signature-area {
            position: absolute;
            bottom: 60px;
            right: 80px;
            width: 200px;
            border-bottom: 2px solid #2c3e50;
            text-align: center;
            padding-top: 5px;
            font-size: 14px;
            color: #7f8c8d;
        }
    </style>
</head>
<body>
    <div class="certificate-container">
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
</body>
</html>
