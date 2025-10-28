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
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 40px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #333;
        }
        .certificate-container {
            background: white;
            padding: 60px;
            border-radius: 20px;
            box-shadow: 0 0 50px rgba(0,0,0,0.2);
            text-align: center;
            height: calc(100vh - 80px);
            position: relative;
        }
        .header {
            margin-bottom: 40px;
        }
        .logo {
            font-size: 48px;
            color: #667eea;
            margin-bottom: 10px;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 10px;
        }
        .title {
            font-size: 64px;
            font-weight: bold;
            color: #2c3e50;
            margin: 30px 0;
            text-transform: uppercase;
            letter-spacing: 4px;
        }
        .subtitle {
            font-size: 24px;
            color: #7f8c8d;
            margin-bottom: 40px;
        }
        .student-name {
            font-size: 42px;
            font-weight: bold;
            color: #667eea;
            margin: 30px 0;
            text-decoration: underline;
        }
        .course-info {
            font-size: 28px;
            color: #2c3e50;
            margin: 20px 0;
        }
        .completion-text {
            font-size: 20px;
            color: #7f8c8d;
            margin: 30px 0;
            line-height: 1.6;
        }
        .certificate-number {
            position: absolute;
            bottom: 40px;
            left: 60px;
            font-size: 14px;
            color: #95a5a6;
        }
        .issue-date {
            position: absolute;
            bottom: 40px;
            right: 60px;
            font-size: 14px;
            color: #95a5a6;
        }
        .decorative-border {
            border: 8px solid #667eea;
            border-radius: 20px;
            position: absolute;
            top: 20px;
            left: 20px;
            right: 20px;
            bottom: 20px;
            pointer-events: none;
        }
        .signature-section {
            position: absolute;
            bottom: 100px;
            left: 50%;
            transform: translateX(-50%);
            text-align: center;
        }
        .signature-line {
            width: 200px;
            border-bottom: 2px solid #333;
            margin: 20px auto 10px;
        }
    </style>
</head>
<body>
    <div class="certificate-container">
        <div class="decorative-border"></div>

        <div class="header">
            <div class="logo">🎓</div>
            <div class="company-name">EduManagement System</div>
        </div>

        <h2 class="title">Certificate of Completion</h2>

        <p class="subtitle">This is to certify that</p>

        <h3 class="student-name">{{ $certificate->student->name }}</h3>

        <p class="completion-text">
            has successfully completed the course
        </p>

        <h4 class="course-info">{{ $certificate->course->name ?? 'General Course' }}</h4>

        <p class="completion-text">
            and has demonstrated proficiency in all required competencies.<br>
            This certificate is awarded in recognition of their dedication and achievement.
        </p>

        <div class="signature-section">
            <div class="signature-line"></div>
            <p style="margin: 0; font-size: 14px; color: #7f8c8d;">Authorized Signature</p>
        </div>

        <div class="certificate-number">
            Certificate No: {{ $certificate->number }}
        </div>

        <div class="issue-date">
            Date: {{ $certificate->issued_at->format('F j, Y') }}
        </div>
    </div>
</body>
</html>
