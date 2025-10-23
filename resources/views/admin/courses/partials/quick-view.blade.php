<div class="row">
    <div class="col-md-8">
        <h4>{{ $course->name }}</h4>
        <p class="text-muted">{{ $course->code }}</p>
        <p>{{ $course->description }}</p>
        
        <div class="row">
            <div class="col-md-6">
                <p><strong>Fee:</strong> ₹{{ number_format($course->fee) }}</p>
                <p><strong>Duration:</strong> {{ $course->duration_months }} months</p>
                <p><strong>Level:</strong> {{ ucfirst($course->level ?? 'N/A') }}</p>
            </div>
            <div class="col-md-6">
                <p><strong>Category:</strong> {{ ucfirst($course->category ?? 'N/A') }}</p>
                <p><strong>Students:</strong> {{ $course->students()->count() }}</p>
                <p><strong>Status:</strong> 
                    <span class="badge badge-{{ $course->status == 'active' ? 'success' : 'secondary' }}">
                        {{ ucfirst($course->status) }}
                    </span>
                </p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="text-center">
            @if($course->is_featured)
                <span class="badge badge-warning badge-lg mb-3">Featured Course</span>
            @endif
            
            <div class="mb-3">
                <a href="{{ route('admin.courses.show', $course) }}" class="btn btn-primary btn-block">
                    View Details
                </a>
                <a href="{{ route('admin.courses.edit', $course) }}" class="btn btn-success btn-block">
                    Edit Course
                </a>
            </div>
        </div>
    </div>
</div>

@if($course->students()->count() > 0)
<hr>
<h6>Recent Students</h6>
<div class="row">
    @foreach($course->students()->take(4)->get() as $student)
    <div class="col-md-6 mb-2">
        <div class="d-flex align-items-center">
            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mr-2" 
                 style="width: 30px; height: 30px; font-size: 12px;">
                {{ strtoupper(substr($student->name, 0, 2)) }}
            </div>
            <div>
                <small><strong>{{ $student->name }}</strong></small><br>
                <small class="text-muted">{{ $student->email }}</small>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endif
