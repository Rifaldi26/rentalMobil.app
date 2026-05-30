<?php
// app/Http/Controllers/Admin/UserController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $users = User::query()
            ->when($request->role,   fn ($q) => $q->where('role', $request->role))
            ->when($request->search, fn ($q) => $q->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
            }))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function suspend(User $user): RedirectResponse
    {
        abort_if($user->isAdmin(), 403, 'Admin tidak bisa disuspend.');

        $user->update(['is_suspended' => true]);
        AuditLogService::log('user.suspended', $user);

        return back()->with('success', "Akun {$user->name} disuspend.");
    }

    public function unsuspend(User $user): RedirectResponse
    {
        $user->update(['is_suspended' => false]);
        AuditLogService::log('user.unsuspended', $user);

        return back()->with('success', "Akun {$user->name} diaktifkan kembali.");
    }
}
