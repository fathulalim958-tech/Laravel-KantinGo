<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreSellerRequest;
use App\Http\Requests\Admin\UpdateSellerRequest;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class SellerController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::where('role', 'seller')->with('canteen')->latest();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $sellers = $query->paginate(10)->withQueryString();

        return view('admin.sellers.index', compact('sellers'));
    }

    public function create(): View
    {
        return view('admin.sellers.create');
    }

    public function store(StoreSellerRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $seller = User::create([
            'name' => $validated['name'],
            'username' => strtolower($validated['username']),
            'email' => strtolower($validated['email']),
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'] ?? null,
            'role' => 'seller',
            'is_active' => $request->boolean('is_active', true),
        ]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'SELLER_CREATED',
            'description' => "Admin membuat akun penjual baru: {$seller->name} ({$seller->username})",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('admin.sellers.index')
            ->with('success', "Akun penjual \"{$seller->name}\" berhasil dibuat. Sekarang Anda dapat menghubungkannya ke kantin.");
    }

    public function edit(User $seller): View
    {
        if (!$seller->isSeller()) {
            abort(404);
        }

        return view('admin.sellers.edit', compact('seller'));
    }

    public function update(UpdateSellerRequest $request, User $seller): RedirectResponse
    {
        if (!$seller->isSeller()) {
            abort(404);
        }

        $validated = $request->validated();

        $data = [
            'name' => $validated['name'],
            'username' => strtolower($validated['username']),
            'email' => strtolower($validated['email']),
            'phone' => $validated['phone'] ?? null,
            'is_active' => $request->boolean('is_active'),
        ];

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $seller->update($data);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'SELLER_UPDATED',
            'description' => "Admin memperbarui data penjual: {$seller->name}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('admin.sellers.index')
            ->with('success', "Data penjual \"{$seller->name}\" berhasil diperbarui.");
    }

    public function toggleStatus(Request $request, User $seller): RedirectResponse
    {
        if (!$seller->isSeller()) {
            abort(404);
        }

        $seller->update(['is_active' => !$seller->is_active]);

        $statusText = $seller->is_active ? 'diaktifkan' : 'dinonaktifkan';

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'SELLER_STATUS_TOGGLED',
            'description' => "Akun penjual {$seller->name} telah {$statusText}.",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->with('success', "Akun penjual \"{$seller->name}\" telah {$statusText}.");
    }

    public function destroy(Request $request, User $seller): RedirectResponse
    {
        if (!$seller->isSeller()) {
            abort(404);
        }

        // Jika seller mengelola kantin, berikan peringatan
        if ($seller->canteen) {
            return back()->with('error', "Penjual \"{$seller->name}\" sedang mengelola kantin \"{$seller->canteen->name}\". Pindahkan atau hapus kantin terlebih dahulu.");
        }

        $name = $seller->name;
        $seller->delete();

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'SELLER_DELETED',
            'description' => "Admin menghapus akun penjual: {$name}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('admin.sellers.index')
            ->with('success', "Akun penjual \"{$name}\" berhasil dihapus.");
    }
}
