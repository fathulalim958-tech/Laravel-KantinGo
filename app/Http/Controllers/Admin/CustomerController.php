<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::where('role', 'customer')->latest();

        if ($type = $request->input('user_type')) {
            $query->where('user_type', $type);
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('identifier_number', 'like', "%{$search}%");
            });
        }

        $customers = $query->paginate(12)->withQueryString();

        return view('admin.customers.index', compact('customers'));
    }

    public function show(User $customer): View
    {
        if (!$customer->isCustomer()) abort(404);

        $customer->load(['orders' => function ($q) {
            $q->with('canteen')->latest();
        }]);

        $totalSpent = $customer->orders()->where('payment_status', 'Lunas')->sum('total_price');
        $totalOrders = $customer->orders()->count();

        return view('admin.customers.show', compact('customer', 'totalSpent', 'totalOrders'));
    }

    public function edit(User $customer): View
    {
        if (!$customer->isCustomer()) abort(404);

        return view('admin.customers.edit', compact('customer'));
    }

    public function update(Request $request, User $customer): RedirectResponse
    {
        if (!$customer->isCustomer()) abort(404);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'max:50', 'alpha_dash', Rule::unique('users', 'username')->ignore($customer->id)],
            'email' => ['required', 'email', 'max:100', Rule::unique('users', 'email')->ignore($customer->id)],
            'user_type' => ['required', 'in:mahasiswa,dosen,staf'],
            'identifier_number' => ['nullable', 'string', 'max:30'],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['nullable', 'string', 'min:8'],
            'is_active' => ['boolean'],
        ]);

        $data = [
            'name' => $validated['name'],
            'username' => strtolower($validated['username']),
            'email' => strtolower($validated['email']),
            'user_type' => $validated['user_type'],
            'identifier_number' => $validated['identifier_number'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'is_active' => $request->boolean('is_active'),
        ];

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $customer->update($data);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'CUSTOMER_UPDATED',
            'description' => "Admin memperbarui data pelanggan: {$customer->name} ({$customer->username})",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('admin.customers.index')
            ->with('success', "Data pelanggan \"{$customer->name}\" berhasil diperbarui.");
    }

    public function toggleStatus(Request $request, User $customer): RedirectResponse
    {
        if (!$customer->isCustomer()) abort(404);

        $customer->update(['is_active' => !$customer->is_active]);

        $statusText = $customer->is_active ? 'diaktifkan' : 'dinonaktifkan';

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'CUSTOMER_STATUS_TOGGLED',
            'description' => "Akun pelanggan {$customer->name} telah {$statusText}.",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->with('success', "Akun pelanggan \"{$customer->name}\" telah {$statusText}.");
    }
}
