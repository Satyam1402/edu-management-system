<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Franchise;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables; 

class FranchiseController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->getDatatablesData();
        }
        
        return view('admin.franchises.index');
    }

    public function getDatatablesData()
    {
        $franchises = Franchise::with(['users'])->select([
            'id',
            'name',
            'code', 
            'email',
            'phone',
            'address',
            'city',
            'state',
            'pincode',
            'contact_person',
            'status',
            'established_date',
            'created_at'
        ]);

        return DataTables::of($franchises)
            ->addIndexColumn()
            ->addColumn('franchise_details', function ($franchise) {
                return '<div class="franchise-details">' .
                    '<div class="font-weight-bold text-dark mb-1">' . $franchise->name . '</div>' .
                    '<small class="text-muted"><i class="fas fa-hashtag mr-1"></i>' . $franchise->code . '</small>' .
                    '</div>';
            })
            ->addColumn('contact_info', function ($franchise) {
                $html = '<div class="contact-info">';
                $html .= '<div class="mb-1"><i class="fas fa-envelope text-primary mr-1"></i> ' . $franchise->email . '</div>';
                $html .= '<div><i class="fas fa-phone text-success mr-1"></i> ' . $franchise->phone . '</div>';
                if ($franchise->contact_person) {
                    $html .= '<div class="mt-1"><i class="fas fa-user text-info mr-1"></i> ' . $franchise->contact_person . '</div>';
                }
                $html .= '</div>';
                return $html;
            })
            ->addColumn('location_info', function ($franchise) {
                $location = [];
                if ($franchise->address) $location[] = $franchise->address;
                if ($franchise->city) $location[] = $franchise->city;
                if ($franchise->state) $location[] = $franchise->state;
                if ($franchise->pincode) $location[] = $franchise->pincode;
                
                if (!empty($location)) {
                    return '<i class="fas fa-map-marker-alt text-danger mr-1"></i>' . implode(', ', $location);
                }
                return '<span class="text-muted">Not specified</span>';
            })
            ->addColumn('users_count', function ($franchise) {
                return $franchise->users->count();
            })
            ->addColumn('status_badge', function ($franchise) {
                $badges = [
                    'active' => 'success',
                    'inactive' => 'secondary', 
                    'suspended' => 'danger'
                ];
                $badgeClass = $badges[$franchise->status] ?? 'secondary';
                return '<span class="badge badge-' . $badgeClass . ' px-3 py-1">' . ucfirst($franchise->status) . '</span>';
            })
            ->addColumn('date_info', function ($franchise) {
                $html = '<div class="text-center">';
                
                if ($franchise->established_date) {
                    $html .= '<div class="small text-muted">Est: ' . $franchise->established_date->format('M Y') . '</div>';
                }
                
                $html .= '<div class="font-weight-bold">' . $franchise->created_at->format('M d, Y') . '</div>';
                $html .= '<small class="text-muted">Added: ' . $franchise->created_at->format('H:i A') . '</small>';
                $html .= '</div>';
                
                return $html;
            })
          ->addColumn('actions', function ($franchise) {
    $buttons = '<div class="btn-group" role="group">';
    
    // View button
    $buttons .= '<a href="' . route('admin.franchises.show', $franchise) . '" class="btn btn-sm btn-info mr-1" title="View Details" data-toggle="tooltip">';
    $buttons .= '<i class="fas fa-eye"></i></a>';
    
    // Edit button  
    $buttons .= '<a href="' . route('admin.franchises.edit', $franchise) . '" class="btn btn-sm btn-primary mr-1" title="Edit" data-toggle="tooltip">';
    $buttons .= '<i class="fas fa-edit"></i></a>';
    
    // Delete button with data attributes for AJAX
    $buttons .= '<button class="btn btn-sm btn-danger delete-franchise" data-id="' . $franchise->id . '" title="Delete" data-toggle="tooltip">';
    $buttons .= '<i class="fas fa-trash"></i></button>';
    
    $buttons .= '</div>';
    return $buttons;
})

            ->rawColumns(['franchise_details', 'contact_info', 'location_info', 'status_badge', 'date_info', 'actions'])
            ->make(true);
    }

    public function create()
    {
        return view('admin.franchises.create');
    }

    public function store(Request $request)
    {
        // Validation rules
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:20|unique:franchises,code',
            'email' => 'required|email|unique:franchises,email',
            'phone' => 'required|string|max:15',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100', 
            'pincode' => 'nullable|string|max:10',
            'contact_person' => 'nullable|string|max:100',
            'established_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'status' => 'required|in:active,inactive,suspended',
            'create_user' => 'nullable|boolean',
            'user_name' => 'required_if:create_user,1|string|max:255',
            'user_email' => 'required_if:create_user,1|email|unique:users,email',
        ]);

        try {
            // Safe field extraction with defaults
            $franchiseData = [
                'name' => $request->input('name'),
                'code' => $request->input('code'),
                'email' => $request->input('email'),
                'phone' => $request->input('phone'),
                'address' => $request->input('address'),
                'city' => $request->input('city'),
                'state' => $request->input('state'),
                'pincode' => $request->input('pincode'),
                'contact_person' => $request->input('contact_person'),
                'established_date' => $request->input('established_date'),
                'notes' => $request->input('notes'),
                'status' => $request->input('status', 'active'),
            ];

            // Create franchise
            $franchise = Franchise::create($franchiseData);

            // Handle user creation
            $userData = null;
            if ($request->boolean('create_user') && $request->filled(['user_name', 'user_email'])) {
                $password = Str::random(12);

                $user = User::create([
                    'name' => $request->input('user_name'),
                    'email' => $request->input('user_email'),
                    'password' => Hash::make($password),
                    'franchise_id' => $franchise->id,
                ]);

                $franchiseRole = Role::firstOrCreate(['name' => 'franchise']);
                $user->assignRole($franchiseRole);

                $userData = [
                    'franchise' => $franchise->name,
                    'name' => $user->name,
                    'email' => $user->email,
                    'password' => $password
                ];
            }

            // Redirect with success message
            $message = $userData ? 'Franchise and user created successfully!' : 'Franchise created successfully!';
            $redirect = redirect()->route('admin.franchises.index')->with('success', $message);
            
            if ($userData) {
                $redirect->with('user_created', $userData);
            }

            return $redirect;

        } catch (\Exception $e) {
            \Log::error('Franchise creation error: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Error creating franchise: ' . $e->getMessage());
        }
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
    try {
        // Check if franchise has students
        if ($franchise->students()->count() > 0) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete franchise with existing students.'
                ], 400);
            }
            return redirect()->back()
                ->with('error', 'Cannot delete franchise with existing students.');
        }

        // Delete associated users first
        $franchise->users()->delete();

        // Delete franchise
        $franchise->delete();

        // Return appropriate response based on request type
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Franchise deleted successfully!'
            ]);
        }

        return redirect()->route('admin.franchises.index')
            ->with('success', 'Franchise deleted successfully!');
            
    } catch (\Exception $e) {
        \Log::error('Error deleting franchise: ' . $e->getMessage());
        
        if (request()->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting franchise: ' . $e->getMessage()
            ], 500);
        }
        
        return redirect()->back()
            ->with('error', 'Error deleting franchise.');
    }
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
