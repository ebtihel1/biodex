<?php

namespace Tests\Feature;

use App\Models\Waste;
use App\Services\WasteImageClassifier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class WasteClassificationTest extends TestCase
{
    use RefreshDatabase;

    private const JPG_1PX = '/9j/4AAQSkZJRgABAQEAYABgAAD/2wBDAAgGBgcGBQgHBwcJCQgKDBQNDAsLDBkSEw8UHRofHh0aHBwgJC4nICIsIxwcKDcpLDAxNDQ0Hyc5PTgyPC4zNDL/2wBDAQkJCQwLDBgNDRgyIRwhMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjL/wAARCAABAAEDASIAAhEBAxEB/8QAHwAAAQUBAQEBAQEAAAAAAAAAAAECAwQFBgcICQoL/8QAtRAAAgEDAwIEAwUFBAQAAAF9AQIDAAQRBRIhMUEGE1FhByJxFDKBkaEII0KxwRVS0fAkM2JyggkKFhcYGRolJicoKSo0NTY3ODk6Q0RFRkdISUpTVFVWV1hZWmNkZWZnaGlqc3R1dnd4eXqDhIWGh4iJipKTlJWWl5iZmqKjpKWmp6ipqrKztLW2t7i5usLDxMXGx8jJytLT1NXW19jZ2uHi4+Tl5ufo6erx8vP09fb3+Pn6/9oADAMBAAIRAxEAPwD3+iiigD/2Q==';

    private int $pointId;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pointId = DB::table('collection_points')->insertGetId([
            'name' => 'Test Point',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $categories = ['Plastique', 'Verre', 'Papier-Carton', 'Métal', 'Déchet organique', 'Déchets électroniques', 'Textile', 'Bois'];
        foreach ($categories as $index => $name) {
            DB::table('waste_categories')->insert([
                'id' => $index + 1,
                'name' => $name,
                'description' => null,
                'recycling_instructions' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Une vraie image JPEG (pas de GD requis pour générer un fichier factice).
        $this->image = UploadedFile::fake()->createWithContent('dechet.jpg', base64_decode(self::JPG_1PX));
    }

    private function mockClassifier(array $result, ?int $idForCode): void
    {
        $mock = $this->createMock(WasteImageClassifier::class);
        $mock->method('classify')->willReturn($result);
        $mock->method('categoryIdForCode')->willReturn($idForCode);

        $this->app->instance(WasteImageClassifier::class, $mock);
    }

    private function postWaste(array $overrides = [])
    {
        return $this->actingAs(\App\Models\User::factory()->create())->post('/wastess', $overrides);
    }

    public function test_classify_page_is_displayed(): void
    {
        $this->get('/ai/classify')->assertOk()->assertSee('Classification automatique des déchets');
    }

    public function test_classify_endpoint_returns_ai_prediction(): void
    {
        $this->mockClassifier([
            'category_code' => 'organic',
            'category_label' => 'Déchet organique',
            'confidence' => 0.45,
            'model' => 'heuristic-pil',
            'source' => 'python_cnn',
            'top_categories' => [],
        ], null);

        $this->post('/waste/classify', ['image' => $this->image])
            ->assertOk()
            ->assertJson([
                'success' => true,
                'category' => 'organic',
                'source' => 'python_cnn',
            ]);
    }

    public function test_classify_endpoint_rejects_missing_image(): void
    {
        $this->post('/waste/classify', [])->assertSessionHasErrors('image');
    }

    public function test_waste_store_persists_ai_classification(): void
    {
        $this->mockClassifier([
            'category_code' => 'organic',
            'category_label' => 'Déchet organique',
            'confidence' => 0.45,
            'model' => 'heuristic-pil',
            'source' => 'python_cnn',
            'top_categories' => [],
        ], 5);

        $this->postWaste([
            'type' => 'Restes alimentaires',
            'weight' => 1.5,
            'status' => 'recyclable',
            'waste_category_id' => 5,
            'collection_point_id' => $this->pointId,
            'image' => $this->image,
        ])->assertRedirect('/wastess/1');

        $this->assertDatabaseHas('wastes', [
            'id' => 1,
            'waste_category_id' => 5,
            'ai_classification' => 'Déchet organique',
            'ai_confidence' => 0.45,
        ]);
    }

    public function test_waste_store_overrides_category_when_ai_is_confident(): void
    {
        $this->mockClassifier([
            'category_code' => 'organic',
            'category_label' => 'Déchet organique',
            'confidence' => 0.85,
            'model' => 'mobilenetv2-imagenet',
            'source' => 'python_cnn',
            'top_categories' => [],
        ], 5);

        $this->postWaste([
            'type' => 'Épluchures',
            'weight' => 0.8,
            'status' => 'recyclable',
            'waste_category_id' => 3,
            'collection_point_id' => $this->pointId,
            'image' => $this->image,
        ])->assertRedirect('/wastess/1');

        $this->assertSame(5, Waste::find(1)->waste_category_id);
    }

    public function test_waste_store_without_image_skips_classification(): void
    {
        $this->postWaste([
            'type' => 'Bouteille',
            'weight' => 0.5,
            'status' => 'recyclable',
            'waste_category_id' => 1,
            'collection_point_id' => $this->pointId,
        ])->assertRedirect('/wastess/1');

        $this->assertNull(Waste::find(1)->ai_classification);
        $this->assertNull(Waste::find(1)->ai_confidence);
    }
}
