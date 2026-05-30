<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\InsufficientBalanceException;
use App\Http\Controllers\Controller;
use App\Models\Withdrawal;
use App\Services\WithdrawalService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WithdrawalController extends Controller
{
    public function __construct(private readonly WithdrawalService $withdrawalService) {}

    public function index(Request $request): View
    {
        $withdrawals = Withdrawal::with('user')
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $pendingTotal = Withdrawal::where('status', 'pending')->sum('amount');

        return view('admin.withdrawals.index', compact('withdrawals', 'pendingTotal'));
    }

    public function create(): View
    {
        $user = auth()->user();
        return view('admin.withdrawals.create', compact('user'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'amount' => ['required', 'numeric', 'min:' . WithdrawalService::MINIMUM_WITHDRAWAL],
        ]);

        try {
            $this->withdrawalService->request(auth()->user(), $request->amount);
        } catch (InsufficientBalanceException $e) {
            return back()->withErrors(['amount' => $e->getMessage()]);
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['amount' => $e->getMessage()]);
        }

        return redirect()
            ->route('admin.withdrawals.index')
            ->with('success', 'Permintaan penarikan berhasil diajukan.');
    }

    public function approve(Request $request, Withdrawal $withdrawal): RedirectResponse
    {
        $request->validate(['note' => ['nullable', 'string', 'max:300']]);

        $this->withdrawalService->approve($withdrawal, $request->note ?? '');

        return back()->with('success', 'Penarikan Rp ' . number_format($withdrawal->amount, 0, ',', '.') . ' diproses.');
    }

    public function reject(Request $request, Withdrawal $withdrawal): RedirectResponse
    {
        $request->validate(['reason' => ['required', 'string', 'max:300']]);

        $this->withdrawalService->reject($withdrawal, $request->reason);

        return back()->with('success', 'Penarikan ditolak dan saldo dikembalikan.');
    }
}