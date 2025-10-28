<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Print Certificate - {{ $certificate->number }}</title>
    <style>
        @media print {
            body { margin: 0; }
            @page { margin: 0; size: A4 landscape; }
        }
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #333;
        }
        .certificate-container {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 0 30px rgba(0,0,0,0.2);
            text-align: center;
            min-height: 600px;
            position: relative;
        }
        .header { margin-bottom: 30px; }
        .logo { font-size: 36px; color: #667eea; margin-bottom: 10px; }
        .company-name { font-size: 20px; font-weight: bold; color: #2c3e50; }
        .title { font-size: 48px; font-weight: bold; color: #2c3e50; margin: 20px 0; text-transform: uppercase; letter-spacing: 3px; }
        .subtitle { font-size: 18px; color: #7f8c8d; margin-bottom: 30px; }
        .student-name { font-size: 32px; font-weight: bold; color: #667eea; margin: 20px 0; text-decoration: underline; }
        .course-info { font-size: 22px; color: #2c3e50; margin: 15px 0; }
        .completion-text { font-size: 16px; color: #7f8c8d; margin: 20px 0; line-height: 1.6; }
        .certificate-number { position: absolute; bottom: 20px; left: 40px; font-size: 12px; color: #95a5a6; }
        .issue-date { position: absolute; bottom: 20px; right: 40px; font-size: 12px; color: #95a5a6; }
        .decorative-border { border: 4px solid #667eea; border-radius: 15px; position: absolute; top: 10px; left: 10px; right: 10px; bottom: 10px; pointer-events: none; }
    </style>
    <script>
        window.onload = function() {
            window.print();
        }
    </script>
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
        <p class="completion-text">has successfully completed the course</p>
        <h4 class="course-info">{{ $certificate->course->name ?? 'General Course' }}</h4>
        <p class="completion-text">and has demonstrated proficiency in all required competencies.</p>

        <div class="certificate-number">Certificate No: {{ $certificate->number }}</div>
        <div class="issue-date">Date: {{ $certificate->issued_at->format('F j, Y') }}</div>
    </div>
</body>
</html>
