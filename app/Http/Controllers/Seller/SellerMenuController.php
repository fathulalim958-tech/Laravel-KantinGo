<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Http\Requests\Seller\StoreMenuRequest;
use App\Http\Requests\Seller\UpdateMenuRequest;
use App\Models\ActivityLog;
use App\Models\Menu;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SellerMenuController extends Controller
{
    public function index(Request $request): View
    {
        $canteen = Auth::user()->canteen;
        if (!$canteen) abort(403);

        $query = Menu::where('canteen_id', $canteen->id)->latest();

        if ($search = $request->input('q')) {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($cat = $request->input('kategori')) {
            $query->where('kategori', $cat);
        }

        $menus = $query->paginate(12)->withQueryString();

        return view('seller.menus.index', compact('canteen', 'menus'));
    }

    public function create(): View
    {
        $canteen = Auth::user()->canteen;
        if (!$canteen) abort(403);

        return view('seller.menus.create', compact('canteen'));
    }

    public function store(StoreMenuRequest $request): RedirectResponse
    {
        $canteen = Auth::user()->canteen;
        $validated = $request->validated();

        $imageName = 'default.svg';
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = 'menu_' . bin2hex(random_bytes(10)) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('assets/uploads'), $filename);
            $imageName = 'upload:' . $filename;
        }

        $menu = Menu::create([
            'canteen_id' => $canteen->id,
            'name' => $validated['name'],
            'kategori' => $validated['kategori'],
            'price' => $validated['price'],
            'stock' => $validated['stock'],
            'description' => $validated['description'] ?? null,
            'image' => $imageName,
            'is_active' => true,
        ]);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'canteen_id' => $canteen->id,
            'action' => 'MENU_CREATED',
            'description' => "Penjual menambahkan menu baru: {$menu->name} (Rp" . number_format($menu->price) . ", Stok: {$menu->stock})",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('seller.menus.index')
            ->with('success', "Menu \"{$menu->name}\" berhasil ditambahkan ke katalog!");
    }

    public function edit(Menu $menu): View
    {
        $this->authorize('update', $menu);
        $canteen = Auth::user()->canteen;

        return view('seller.menus.edit', compact('canteen', 'menu'));
    }

    public function update(UpdateMenuRequest $request, Menu $menu): RedirectResponse
    {
        $this->authorize('update', $menu);
        $validated = $request->validated();

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = 'menu_' . bin2hex(random_bytes(10)) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('assets/uploads'), $filename);
            $validated['image'] = 'upload:' . $filename;
        }

        $validated['is_active'] = $request->boolean('is_active');

        $menu->update($validated);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'canteen_id' => $menu->canteen_id,
            'action' => 'MENU_UPDATED',
            'description' => "Penjual memperbarui menu: {$menu->name}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('seller.menus.index')
            ->with('success', "Menu \"{$menu->name}\" berhasil diperbarui!");
    }

    public function destroy(Request $request, Menu $menu): RedirectResponse
    {
        $this->authorize('delete', $menu);

        $name = $menu->name;
        $menu->delete(); // Soft delete: riwayat pesanan masa lalu tetap utuh!

        ActivityLog::create([
            'user_id' => Auth::id(),
            'canteen_id' => $menu->canteen_id,
            'action' => 'MENU_DELETED',
            'description' => "Penjual menghapus menu: {$name} (Soft Delete)",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('seller.menus.index')
            ->with('success', "Menu \"{$name}\" telah dihapus dari katalog aktif. Riwayat pesanan tetap tersimpan.");
    }
}
