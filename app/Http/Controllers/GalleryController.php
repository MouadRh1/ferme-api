<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GalleryController extends Controller
{
    // ─────────────────────────────────────────────────
    // GET /api/gallery  (public)
    // ─────────────────────────────────────────────────
    public function index()
    {
        $photos = Gallery::where('is_visible', true)
            ->orderBy('order')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($p) => $this->format($p));

        return response()->json($photos);
    }

    // ─────────────────────────────────────────────────
    // GET /api/admin/gallery  (admin : toutes les photos)
    // ─────────────────────────────────────────────────
    public function adminIndex()
    {
        $photos = Gallery::orderBy('order')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($p) => $this->format($p));

        return response()->json($photos);
    }

    // ─────────────────────────────────────────────────
    // POST /api/admin/gallery  (admin)
    // ─────────────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'title'      => 'required|string|max:255',
            'image'      => 'required|image|mimes:jpg,jpeg,png,webp|max:8192',
            'category'   => 'required|in:extérieur,intérieur,piscine,jardin,paysage',
            'order'      => 'nullable|integer',
            'is_visible' => 'nullable|boolean',
        ]);

        // Upload image
        $path = $request->file('image')->store('gallery', 'public');

        $photo = Gallery::create([
            'title'      => $request->title,
            'image_path' => $path,
            'category'   => $request->category,
            'order'      => $request->order ?? 0,
            'is_visible' => $request->is_visible ?? true,
        ]);

        return response()->json([
            'message' => 'Photo ajoutée avec succès.',
            'photo'   => $this->format($photo),
        ], 201);
    }

    // ─────────────────────────────────────────────────
    // PUT /api/admin/gallery/{id}  (admin)
    // ─────────────────────────────────────────────────
    public function update(Request $request, Gallery $gallery)
    {
        $request->validate([
            'title'      => 'sometimes|string|max:255',
            'image'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:8192',
            'category'   => 'sometimes|in:extérieur,intérieur,piscine,jardin,paysage',
            'order'      => 'nullable|integer',
            'is_visible' => 'nullable|boolean',
        ]);

        // Remplacer l'image si nouvelle fournie
        if ($request->hasFile('image')) {
            Storage::disk('public')->delete($gallery->image_path);
            $gallery->image_path = $request->file('image')->store('gallery', 'public');
        }

        $gallery->title      = $request->title      ?? $gallery->title;
        $gallery->category   = $request->category   ?? $gallery->category;
        $gallery->order      = $request->order      ?? $gallery->order;
        $gallery->is_visible = $request->has('is_visible')
            ? (bool)$request->is_visible
            : $gallery->is_visible;

        $gallery->save();

        return response()->json([
            'message' => 'Photo mise à jour.',
            'photo'   => $this->format($gallery),
        ]);
    }

    // ─────────────────────────────────────────────────
    // DELETE /api/admin/gallery/{id}  (admin)
    // ─────────────────────────────────────────────────
    public function destroy(Gallery $gallery)
    {
        Storage::disk('public')->delete($gallery->image_path);
        $gallery->delete();

        return response()->json(['message' => 'Photo supprimée.']);
    }

    // ─────────────────────────────────────────────────
    // POST /api/gallery/{id}/like  (public)
    // ─────────────────────────────────────────────────
    public function like(Gallery $gallery)
    {
        $gallery->increment('likes');
        return response()->json(['likes' => $gallery->likes]);
    }

    // ─────────────────────────────────────────────────
    // Helper format
    // ─────────────────────────────────────────────────
    private function format(Gallery $p): array
    {
        return [
            'id'         => $p->id,
            'title'      => $p->title,
            'url'        => $p->image_url,
            'image_path' => $p->image_path,
            'category'   => $p->category,
            'likes'      => $p->likes,
            'order'      => $p->order,
            'is_visible' => $p->is_visible,
            'created_at' => $p->created_at?->format('Y-m-d'),
        ];
    }
}