<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Franchise;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class FranchiseController extends Controller
{
    public function index()
    {
        $franchises = Franchise::with('users')->latest()->paginate(10);

        return view('admin.franchises.index', compact('franchises'));
    }

    public function create()
    {
        return view('admin.franchises.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:franchises,code',
            'email' => 'required|email|unique:franchises,email',
            'phone' => 'required|string|max:15',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'pincode' => 'nullable|string|max:10',
            'status' => 'required|in:active,inactive',

            // User creation fields
            'create_user' => 'boolean',
            'user_name' => 'required_if:create_user,1|string|max:255',
            'user_email' => 'required_if:create_user,1|email|unique:users,email',
        ]);

        // Create franchise
        $franchise = Franchise::create($validated);

        // Create franchise user if requested
        if ($request->create_user) {
            $password = Str::random(10); // Generate random password

            $user = User::create([
                'name' => $validated['user_name'],
                'email' => $validated['user_email'],
                'password' => Hash::make($password),
                'franchise_id' => $franchise->id,
            ]);

            // Assign franchise role
            $user->assignRole('franchise');

            // Return success with login credentials
            return redirect()->route('admin.franchises.index')
                ->with('success', 'Franchise created successfully!')
                ->with('user_created', [
                    'name' => $user->name,
                    'email' => $user->email,
                    'password' => $password,
                    'franchise' => $franchise->name
                ]);
        }

        return redirect()->route('admin.franchises.index')
            ->with('success', 'Franchise created successfully!');
    }

    public function show(Franchise $franchise)
    {
        $franchise->load(['users', 'students']);

        $stats = [
            'total_students' => $franchise->students()->count(),
            'active_students' => $franchise->students()->where('status', 'active')->count(),
            'total_users' => $franchise->users()->count(),
        ];

        return view('admin.franchises.show', compact('franchise', 'stats'));
    }

    public function edit(Franchise $franchise)
    {
        return view('admin.franchises.edit', compact('franchise'));
    }

    public function update(Request $request, Franchise $franchise)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:franchises,code,' . $franchise->id,
            'email' => 'required|email|unique:franchises,email,' . $franchise->id,
            'phone' => 'required|string|max:15',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'pincode' => 'nullable|string|max:10',
            'status' => 'required|in:active,inactive',
            'contact_person' => 'nullable|string|max:255',
            'established_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $franchise->update($validated);

        return redirect()->route('admin.franchises.index')
            ->with('success', 'Franchise updated successfully!');
    }

    public function destroy(Franchise $franchise)
    {
        // Check if franchise has students
        if ($franchise->students()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete franchise with existing students.');
        }

        // Delete associated users
        $franchise->users()->delete();

        // Delete franchise
        $franchise->delete();

        return redirect()->route('admin.franchises.index')
            ->with('success', 'Franchise deleted successfully!');
    }

    // ADD THIS MISSING METHOD ← THIS IS WHAT YOU NEED!
    public function createUser(Request $request, Franchise $franchise)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
        ]);

        $password = Str::random(12); // Generate secure random password

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($password),
            'franchise_id' => $franchise->id,
        ]);

        // Assign franchise role
        $user->assignRole('franchise');

        return response()->json([
            'success' => true,
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'password' => $password,
            ]
        ]);
    }
}
