<div class="row">
    <div class="col-md-4 text-center">
        <div class="student-avatar-large mb-3">
            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto" style="width: 80px; height: 80px;">
                <i class="fas fa-user fa-2x"></i>
            </div>
        </div>
        <h5 class="font-weight-bold">{{ $student->name }}</h5>
        <p class="text-muted">{{ $student->student_id }}</p>
        <span class="badge badge-{{ $student->status_badge }} px-3 py-2">{{ ucfirst($student->status) }}</span>
    </div>
    <div class="col-md-8">
        <h6 class="font-weight-bold mb-3">Student Information</h6>
        
        <div class="row">
            <div class="col-sm-6">
                <p><strong>Email:</strong><br>{{ $student->email }}</p>
                <p><strong>Phone:</strong><br>{{ $student->phone }}</p>
                <p><strong>Gender:</strong><br>{{ ucfirst($student->gender) }}</p>
                <p><strong>Age:</strong><br>{{ $student->age ?? 'N/A' }} years</p>
            </div>
            <div class="col-sm-6">
                <p><strong>Franchise:</strong><br>{{ $student->franchise->name ?? 'Not assigned' }}</p>
                <p><strong>Course:</strong><br>{{ $student->course->name ?? 'No course' }}</p>
                <p><strong>Batch:</strong><br>{{ $student->batch ?? 'N/A' }}</p>
                <p><strong>Enrollment:</strong><br>{{ $student->enrollment_date ? $student->enrollment_date->format('M d, Y') : 'N/A' }}</p>
            </div>
        </div>

        <h6 class="font-weight-bold mb-2 mt-3">Address</h6>
        <p class="text-muted">
            {{ $student->address }}<br>
            {{ $student->city }}, {{ $student->state }} - {{ $student->pincode }}
        </p>

        @if($student->guardian_name)
        <h6 class="font-weight-bold mb-2 mt-3">Guardian Information</h6>
        <p class="text-muted">
            <strong>{{ $student->guardian_name }}</strong><br>
            {{ $student->guardian_phone }}
        </p>
        @endif

        <div class="mt-4">
            <a href="{{ route('admin.students.show', $student) }}" class="btn btn-primary btn-sm">
                <i class="fas fa-user mr-1"></i>Full Profile
            </a>
            <a href="{{ route('admin.students.edit', $student) }}" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-edit mr-1"></i>Edit
            </a>
        </div>
    </div>
</div>
