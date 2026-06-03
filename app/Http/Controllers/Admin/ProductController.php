<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of products. (INI YANG KURANG)
     */
    public function index(Request $request)
    {
        $query = Product::with('category');
        
        // Search
        if ($request->has('search') && $request->search != '') {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        
        // Filter by category
        if ($request->has('category') && $request->category != '') {
            $query->where('category_id', $request->category);
        }
        
        // Filter by availability
        if ($request->has('availability') && $request->availability != '') {
            $query->where('is_available', $request->availability === 'available');
        }
        
        $products = $query->latest()->paginate(10);
        $categories = Category::all();
        
        return view('admin.products.index', compact('products', 'categories'));
    }

    /**
     * Show form for creating new product.
     */
    public function create()
    {
        $categories = Category::all();
        return view('admin.products.create', compact('categories'));
    }

    /**
     * Store a newly created product.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'is_available' => 'nullable|boolean'
        ]);

        // Generate slug unik
        $slug = Str::slug($validated['name']);
        $originalSlug = $slug;
        $count = 1;

        while (Product::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }

        $validated['slug'] = $slug;

        // Upload gambar
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
            $validated['image'] = $imagePath;
        }

        Product::create($validated);

        return redirect()->route('admin.products.index')
            ->with('success', 'Menu berhasil ditambahkan');
    }

    /**
     * Display the specified product. (Opsional)
     */
    public function show(Product $product)
    {
        return view('admin.products.show', compact('product'));
    }

    /**
     * Show form for editing product.
     */
    public function edit(Product $product)
    {
        $categories = Category::all();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified product.
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'is_available' => 'nullable|boolean'
        ]);

        // Generate slug unik (exclude id sekarang)
        $slug = Str::slug($validated['name']);
        $originalSlug = $slug;
        $count = 1;

        while (
            Product::where('slug', $slug)
                ->where('id', '!=', $product->id)
                ->exists()
        ) {
            $slug = $originalSlug . '-' . $count++;
        }

        $validated['slug'] = $slug;

        // Fitur Hapus Gambar (Menyamakan dengan Category)
        if ($request->has('remove_image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = null;
        }

        // Jika upload gambar baru
        if ($request->hasFile('image')) {
            // Hapus gambar lama
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }

            $imagePath = $request->file('image')->store('products', 'public');
            $validated['image'] = $imagePath;
        }

        $product->update($validated);

        return redirect()->route('admin.products.index')
            ->with('success', 'Menu berhasil diupdate');
    }

    /**
     * Remove the specified product. (INI JUGA KURANG)
     */
    public function destroy(Product $product)
    {
        try {
            // Cek apakah produk memiliki relasi dengan pesanan
            if ($product->orderItems()->exists()) {
                return redirect()->route('admin.products.index')
                    ->with('error', 'Tidak dapat menghapus produk ini karena sedang digunakan dalam pesanan. Silakan nonaktifkan status ketersediaannya saja.');
            }

            // Hapus gambar jika ada
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            
            // Hapus produk
            $product->delete();
            
            return redirect()->route('admin.products.index')
                ->with('success', 'Produk berhasil dihapus');
                
        } catch (\Illuminate\Database\QueryException $e) {
            // Menangkap error constraint violation
            if ($e->getCode() == 23000) {
                return redirect()->route('admin.products.index')
                    ->with('error', 'Tidak dapat menghapus produk ini karena terkait dengan data lain. Silakan nonaktifkan statusnya saja.');
            }
            
            return redirect()->route('admin.products.index')
                ->with('error', 'Gagal menghapus produk: ' . $e->getMessage());
        } catch (\Exception $e) {
            return redirect()->route('admin.products.index')
                ->with('error', 'Gagal menghapus produk: ' . $e->getMessage());
        }
    }

    /**
     * Toggle product availability.
     */
    public function toggleAvailability(Product $product)
    {
        $product->update([
            'is_available' => !$product->is_available
        ]);

        return response()->json([
            'success' => true,
            'is_available' => $product->is_available,
            'message' => 'Status produk berhasil diupdate'
        ]);
    }
}