namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class FruitImgService
{
    protected $apiUrl;

    public function __construct()
    {
        $this->apiUrl = config('services.fruit_api.url');
    }

    public function searchImage(string $fruitName): ?string
    {
        try {
            $response = Http::get("{$this->apiUrl}/search", [
                'query' => $fruitName
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['image_url'] ?? null;
            }
        } catch (\Exception $e) {
            \Log::error('Fruit API error: ' . $e->getMessage());
        }

        return null;
    }

    public function downloadAndStore(string $imageUrl, string $productName): ?string
    {
        try {
            $imageContent = Http::get($imageUrl)->body();
            $filename = 'products/' . \Str::slug($productName) . '-' . time() . '.jpg';
            
            Storage::disk('public')->put($filename, $imageContent);
            
            return $filename;
        } catch (\Exception $e) {
            \Log::error('Image download error: ' . $e->getMessage());
            return null;
        }
    }
}
