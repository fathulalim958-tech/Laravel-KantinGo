<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCanteenRequest;
use App\Http\Requests\Admin\UpdateCanteenRequest;
use App\Models\ActivityLog;
use App\Models\Canteen;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CanteenController extends Controller
{
    public function index(Request $request): View
    {
        $query = Canteen::with(['seller', 'menus', 'orders'])->latest();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('campus_location', 'like', "%{$search}%");
            });
        }

        if ($location = $request->input('location')) {
            $query->where('campus_location', 'like', "%{$location}%");
        }

        $canteens = $query->paginate(10)->withQueryString();

        return view('admin.canteens.index', compact('canteens'));
    }

    public function create(): View
    {
        // Hanya tampilkan seller yang belum memiliki kantin
        $availableSellers = User::where('role', 'seller')
            ->whereDoesntHave('canteen')
            ->orderBy('name')
            ->get();

        return view('admin.canteens.create', compact('availableSellers'));
    }

    public function store(StoreCanteenRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            $slug = Str::slug($validated['name']);
            $count = Canteen::where('slug', 'like', "{$slug}%")->count();
            if ($count > 0) {
                $slug .= '-' . ($count + 1);
            }

            $logoName = null;
            if ($request->hasFile('logo')) {
                $file = $request->file('logo');
                $logoName = 'canteen_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('assets/uploads'), $logoName);
            }

            $canteen = Canteen::create([
                'seller_id' => $validated['seller_id'],
                'name' => $validated['name'],
                'slug' => $slug,
                'campus_location' => $validated['campus_location'],
                'description' => $validated['description'] ?? null,
                'opening_hours' => $validated['opening_hours'] ?? '08:00 - 16:30 WIB',
                'is_open' => $request->boolean('is_open', true),
                'logo' => $logoName,
            ]);

            ActivityLog::create([
                'user_id' => auth()->id(),
                'canteen_id' => $canteen->id,
                'action' => 'CANTEEN_CREATED',
                'description' => "Admin menambahkan kantin baru: {$canteen->name} (Seller ID: {$canteen->seller_id})",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();

            return redirect()->route('admin.canteens.index')
                ->with('success', "Kantin \"{$canteen->name}\" berhasil didaftarkan.");
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal menyimpan kantin: ' . $e->getMessage());
        }
    }

    public function show(Canteen $canteen): View
    {
        $canteen->load(['seller', 'categories', 'menus' => function ($q) {
            $q->orderBy('name');
        }, 'orders' => function ($q) {
            $q->latest()->limit(10);
        }]);

        $totalRevenue = $canteen->orders()->where('payment_status', 'Lunas')->sum('total_price');
        $totalOrders = $canteen->orders()->count();

        return view('admin.canteens.show', compact('canteen', 'totalRevenue', 'totalOrders'));
    }

    public function edit(Canteen $canteen): View
    {
        // Seller saat ini + seller yang belum punya kantin
        $availableSellers = User::where('role', 'seller')
            ->where(function ($q) use ($canteen) {
                $q->whereDoesntHave('canteen')
                  ->orWhere('id', $canteen->seller_id);
            })
            ->orderBy('name')
            ->get();

        return view('admin.canteens.edit', compact('canteen', 'availableSellers'));
    }

    public function update(UpdateCanteenRequest $request, Canteen $canteen): RedirectResponse
    {
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            if ($request->hasFile('logo')) {
                $file = $request->file('logo');
                $logoName = 'canteen_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('assets/uploads'), $logoName);
                $validated['logo'] = $logoName;
            }

            $validated['is_open'] = $request->boolean('is_open');

            $canteen->update($validated);

            ActivityLog::create([
                'user_id' => auth()->id(),
                'canteen_id' => $canteen->id,
                'action' => 'CANTEEN_UPDATED',
                'description' => "Admin memperbarui informasi kantin: {$canteen->name}",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();

            return redirect()->route('admin.canteens.index')
                ->with('success', "Informasi kantin \"{$canteen->name}\" berhasil diperbarui.");
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal memperbarui kantin: ' . $e->getMessage());
        }
    }

    public function toggleStatus(Request $request, Canteen $canteen): RedirectResponse
    {
        $canteen->update(['is_open' => !$canteen->is_open]);

        $statusText = $canteen->is_open ? 'dibuka' : 'ditutup sementara';

        ActivityLog::create([
            'user_id' => auth()->id(),
            'canteen_id' => $canteen->id,
            'action' => 'CANTEEN_STATUS_TOGGLED',
            'description' => "Status operasional kantin {$canteen->name} diubah menjadi {$statusText}.",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->with('success', "Kantin \"{$canteen->name}\" sekarang {$statusText}.");
    }

    public function destroy(Request $request, Canteen $canteen): RedirectResponse
    {
        $name = $canteen->name;
        $canteen->delete(); // Soft delete

        ActivityLog::create([
            'user_id' => auth()->id(),
            'canteen_id' => $canteen->id,
            'action' => 'CANTEEN_DELETED',
            'description' => "Admin menonaktifkan kantin: {$name} (Soft Delete)",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('admin.canteens.index')
            ->with('success', "Kantin \"{$name}\" berhasil dinonaktifkan.");
    }
}
