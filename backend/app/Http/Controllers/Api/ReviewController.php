<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReviewRequest;
use App\Http\Resources\ReviewResource;
use App\Models\Product;
use App\Models\Review;

class ReviewController extends Controller
{
    // Daftar ulasan satu produk (id atau slug), terbaru di atas
    public function index(string $product)
    {
        $found = Product::findByIdOrSlug($product);

        if (!$found) {
            return $this->produkTidakAda();
        }

        return ReviewResource::collection(
            $found->reviews()->latest('id')->get()
        );
    }

    // Tambah ulasan (publik, dibatasi throttle di route)
    public function store(StoreReviewRequest $request, string $product)
    {
        $found = Product::findByIdOrSlug($product);

        if (!$found) {
            return $this->produkTidakAda();
        }

        $data = $request->validated();

        $review = $found->reviews()->create([
            'name'    => trim($data['name'] ?? '') ?: 'Pembeli',
            'rating'  => $data['rating'],
            'comment' => $data['comment'],
        ]);

        return (new ReviewResource($review))->response()->setStatusCode(201);
    }

    // Hapus ulasan (admin)
    public function destroy(string $id)
    {
        $review = Review::find($id);

        if (!$review) {
            return response()->json(['status' => 'error', 'message' => 'Ulasan tidak ditemukan.'], 404);
        }

        $review->delete();

        return response()->json(['status' => 'success', 'message' => 'Ulasan dihapus.']);
    }

    private function produkTidakAda()
    {
        return response()->json(['status' => 'error', 'message' => 'Produk tidak ditemukan.'], 404);
    }
}
