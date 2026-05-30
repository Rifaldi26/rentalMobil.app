<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditController extends Controller
{
    public function index(Request $request): View
    {
        $logs = AuditLog::with('user')
            ->when($request->action, fn ($q) => $q->where('action', 'like', '%' . $request->action . '%'))
            ->when($request->user_id, fn ($q) => $q->where('user_id', $request->user_id))
            ->latest()
            ->paginate(30)
            ->withQueryString();

        return view('admin.audit.index', compact('logs'));
    }
}
