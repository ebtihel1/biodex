<?php

namespace Tests\Feature;

use App\Models\CollectionPoint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class CollectionPointManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_renders_with_stats_and_paginated_table(): void
    {
        CollectionPoint::factory()->count(15)->create();

        $response = $this->get(route('collectionpoints.index'));

        $response->assertStatus(200);
        $response->assertSee('Collection Points Management');
        $response->assertSee('15', false);
        $response->assertViewHas('collectionPoints');
        $response->assertViewHas('total', 15);
    }

    public function test_search_filters_points_by_name(): void
    {
        CollectionPoint::factory()->create(['name' => 'Zone Verte Paris']);
        CollectionPoint::factory()->create(['name' => 'Dépôt Lyon Est']);

        $response = $this->get(route('collectionpoints.index', ['search' => 'Verte']));

        $response->assertSee('Zone Verte Paris');
        $response->assertDontSee('Dépôt Lyon Est');
    }

    public function test_status_filter_only_returns_active_points(): void
    {
        CollectionPoint::factory()->active()->create(['name' => 'Point Actif']);
        CollectionPoint::factory()->inactive()->create(['name' => 'Point Inactif']);

        $response = $this->get(route('collectionpoints.index', ['status' => 'active']));

        $response->assertSee('Point Actif');
        $response->assertDontSee('Point Inactif');
    }

    public function test_city_filter_filters_points_by_city(): void
    {
        CollectionPoint::factory()->create(['name' => 'Paris Sud', 'city' => 'Paris']);
        CollectionPoint::factory()->create(['name' => 'Lyon Centre', 'city' => 'Lyon']);

        $response = $this->get(route('collectionpoints.index', ['city' => 'Paris']));

        $response->assertSee('Paris Sud');
        $response->assertDontSee('Lyon Centre');
    }

    public function test_sorting_by_status(): void
    {
        CollectionPoint::factory()->create(['name' => 'Beta Point', 'status' => 'inactive']);
        CollectionPoint::factory()->create(['name' => 'Alpha Point', 'status' => 'active']);

        $response = $this->get(route('collectionpoints.index', ['sort' => 'name', 'direction' => 'asc']));

        $response->assertSeeInOrder(['Alpha Point', 'Beta Point']);
    }

    public function test_export_csv_downloads_full_filtered_set(): void
    {
        CollectionPoint::factory()->active()->create(['name' => 'P1 Export']);
        CollectionPoint::factory()->inactive()->create(['name' => 'P2 Exclu']);

        $response = $this->get(route('collectionpoints.export.csv', ['status' => 'active']));

        $response->assertStatus(200);
        $this->assertStringContainsString('text/csv', $response->headers->get('content-type'));
        $this->assertStringContainsString('.csv', $response->headers->get('content-disposition'));

        ob_start();
        $response->baseResponse->sendContent();
        $content = ob_get_clean();

        $this->assertStringContainsString('P1 Export', $content);
        $this->assertStringNotContainsString('P2 Exclu', $content);
    }

    public function test_export_pdf_downloads_document(): void
    {
        CollectionPoint::factory()->count(2)->create(['name' => 'Point PDF']);

        $response = $this->get(route('collectionpoints.export.pdf'));

        $response->assertStatus(200);
        $this->assertStringContainsString('application/pdf', $response->headers->get('content-type'));
        $this->assertStringContainsString('.pdf', $response->headers->get('content-disposition'));
    }

    public function test_import_csv_creates_collection_points(): void
    {
        $csv = <<<'CSV'
name;address;city;postal_code;latitude;longitude;contact_phone;status;opening_hours;accepted_categories
Point Importé;12 rue Verte;Paris;75011;48.85;2.35;0123456789;active;["Lundi: 9h-18h"];["Plastic","Glass"]
CSV;

        $file = UploadedFile::fake()->createWithContent('points.csv', $csv);

        $response = $this->post(route('collectionpoints.import'), ['csv_file' => $file]);

        $response->assertRedirect();
        $response->assertSessionHas('success', '1 point(s) de collecte importé(s).');

        $this->assertDatabaseHas('collection_points', ['name' => 'Point Importé', 'city' => 'Paris', 'status' => 'active']);

        $point = CollectionPoint::where('name', 'Point Importé')->first();
        $this->assertEquals(['Plastic', 'Glass'], $point->accepted_categories);
    }

    public function test_import_csv_skips_invalid_rows(): void
    {
        $csv = <<<'CSV'
name;address;city;status
Valid Point;1 rue A;Paris;active
;rue sans nom;Lyon;active
CSV;

        $file = UploadedFile::fake()->createWithContent('points.csv', $csv);

        $response = $this->post(route('collectionpoints.import'), ['csv_file' => $file]);

        $response->assertSessionHas('success', '1 point(s) de collecte importé(s). 1 ligne(s) ignorée(s).');
        $this->assertDatabaseMissing('collection_points', ['city' => 'Lyon']);
    }

    public function test_import_rejects_non_csv_file(): void
    {
        $file = UploadedFile::fake()->create('points.xlsx', 100);

        $response = $this->post(route('collectionpoints.import'), ['csv_file' => $file]);

        $response->assertSessionHas('error');
        $this->assertDatabaseCount('collection_points', 0);
    }
}
