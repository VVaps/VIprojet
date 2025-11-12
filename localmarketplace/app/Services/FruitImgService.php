<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Exception;

class FruitImgService
{
    private $apiKey;
    private $baseUrl = 'https://api.pexels.com/v1';

    public function __construct()
    {
        $this->apiKey = config('services.pexels.api_key');
    }


    public function searchPhotos(string $query, int $perPage = 9, string $orientation = 'square'): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => $this->apiKey
            ])->get("{$this->baseUrl}/search", [
                'query' => $query,
                'per_page' => $perPage,
                'orientation' => $orientation
            ]);

            if ($response->successful()) {
                return $response->json()['photos'] ?? [];
            }

            return [];
        } catch (Exception $e) {
            Log::error('Erreur lors de la recherche d\'images: ' . $e->getMessage());
            return [];
        }
    }

    public function getCuratedPhotos(int $perPage = 9): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => $this->apiKey
            ])->get("{$this->baseUrl}/curated", [
                'per_page' => $perPage
            ]);

            if ($response->successful()) {
                return $response->json()['photos'] ?? [];
            }

            return [];
        } catch (Exception $e) {
            Log::error('Erreur lors de la récupération des photos: ' . $e->getMessage());
            return [];
        }
    }


    public function downloadAndStore(string $imageUrl, string $productName): ?string
    {
        try {
            $imageContent = Http::get($imageUrl)->body();
            $filename = 'products/' . Str::slug($productName) . '-' . time() . '.jpg';
            
            Storage::disk('public')->put($filename, $imageContent);
            
            return $filename;
        } catch (\Exception $e) {
            Log::error('Image download error: ' . $e->getMessage());
            return null;
        }
    }

    public function getPhoto(int $photoId): ?array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => $this->apiKey
            ])->get("{$this->baseUrl}/photos/{$photoId}");

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        } catch (Exception $e) {
            Log::error('Erreur lors de la récupération de la photo : ' . $e->getMessage());
            return null;
        }
    }
}
