<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'franchise_id', // Add this
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Relationship with Franchise
    public function franchise()
    {
        return $this->belongsTo(Franchise::class);
    }

    // Check if user is super admin
    public function isSuperAdmin()
    {
        return $this->hasRole('super_admin');
    }

    // Check if user is franchise owner
    public function isFranchiseOwner()
    {
        return $this->hasRole('franchise');
    }

    // Get accessible students based on role
    public function accessibleStudents()
    {
        if ($this->isSuperAdmin()) {
            return Student::query(); // All students
        }

        if ($this->isFranchiseOwner() && $this->franchise_id) {
            return Student::where('franchise_id', $this->franchise_id);
        }

        return Student::whereRaw('1 = 0'); // No students
    }
}
