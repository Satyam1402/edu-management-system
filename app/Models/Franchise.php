<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Franchise extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'pincode',
        'status',
        'contact_person',
        'established_date',
        'license_number',
        'notes'
    ];

    protected $casts = [
        'established_date' => 'date'
    ];

    // All your relationships stay the same...
    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }

      public function payments()
    {
        return $this->hasManyThrough(Payment::class, Student::class);
    }

    public function certificates()
    {
        return $this->hasManyThrough(Certificate::class, Student::class);
    }

    // Accessors
    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'active' => 'success',
            'inactive' => 'secondary',
            'suspended' => 'danger',
            default => 'secondary'
        };
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }
}
