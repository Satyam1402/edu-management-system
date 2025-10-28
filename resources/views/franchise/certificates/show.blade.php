<div class="certificate-preview-container">
    <div class="row">
        <div class="col-12">
            <div class="certificate-info mb-4">
                <h6><strong>Certificate Details</strong></h6>
                <table class="table table-sm">
                    <tr>
                        <td width="150"><strong>Certificate Number:</strong></td>
                        <td>{{ $certificate->number }}</td>
                    </tr>
                    <tr>
                        <td><strong>Student Name:</strong></td>
                        <td>{{ $certificate->student->name }}</td>
                    </tr>
                    <tr>
                        <td><strong>Course:</strong></td>
                        <td>{{ $certificate->course->name ?? 'General Certificate' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Issued Date:</strong></td>
                        <td>{{ $certificate->issued_at->format('F j, Y') }}</td>
                    </tr>
                    <tr>
                        <td><strong>Status:</strong></td>
                        <td>
                            <span class="badge badge-success">
                                <i class="fas fa-certificate"></i> {{ ucfirst($certificate->status) }}
                            </span>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="certificate-preview">
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle"></i>
                    <strong>Certificate Preview</strong><br>
                    This is a preview of the certificate. Click "Download PDF" to get the full certificate.
                </div>

                <div class="preview-container p-4" style="border: 2px solid #ddd; background: #f9f9f9;">
                    <div class="text-center">
                        <h2 style="color: #2c3e50; margin-bottom: 20px;">🎓 Certificate of Completion</h2>
                        <p style="font-size: 18px;">This is to certify that</p>
                        <h3 style="color: #667eea; margin: 20px 0; text-decoration: underline;">
                            {{ $certificate->student->name }}
                        </h3>
                        <p style="font-size: 16px;">has successfully completed the course</p>
                        <h4 style="color: #2c3e50; margin: 15px 0;">
                            {{ $certificate->course->name ?? 'General Course' }}
                        </h4>
                        <p style="font-size: 14px; color: #7f8c8d; margin-top: 30px;">
                            Certificate No: {{ $certificate->number }}<br>
                            Date: {{ $certificate->issued_at->format('F j, Y') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
