<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Coverage;
use App\Models\Organization;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CatalogFoundationTest extends TestCase
{
    use DatabaseTransactions;

    protected static bool $schemaIsReady = false;

    protected function setUp(): void
    {
        parent::setUp();

        if (! self::$schemaIsReady) {
            $this->artisan('migrate:fresh', ['--seed' => true]);
            self::$schemaIsReady = true;
        }
    }

    public function test_catalog_foundation_seed_creates_minimal_supplier_company_product_and_coverages(): void
    {
        $supplier = Supplier::query()->where('code', 'macsupplier')->first();
        $company = Company::query()->where('code', 'neo-insure')->first();
        $product = Product::query()->where('code', 'cvt-protection')->first();
        $organization = Organization::query()->where('code', 'macsolution-neo')->first();

        $this->assertNotNull($supplier);
        $this->assertNotNull($company);
        $this->assertNotNull($product);
        $this->assertNotNull($organization);
        $this->assertSame($supplier->id, $company->supplier_id);
        $this->assertSame($company->id, $product->company_id);
        $this->assertCount(2, $product->coverages);
        $this->assertSame($product->id, $organization->enabledProducts()->firstOrFail()->id);
    }

    public function test_coverages_are_modeled_as_distinct_from_product(): void
    {
        $product = Product::query()->create([
            'company_id' => Company::query()->firstOrFail()->id,
            'code' => 'test-product',
            'name' => 'Test Product',
            'kind' => 'coverage_bundle',
            'is_active' => true,
        ]);

        $coverage = Coverage::query()->create([
            'code' => 'test-coverage',
            'name' => 'Test Coverage',
            'category' => 'base',
            'is_active' => true,
        ]);

        $product->coverages()->attach($coverage->id, ['sort_order' => 1]);

        $this->assertSame('test-product', $product->code);
        $this->assertSame('test-coverage', $coverage->code);
        $this->assertNotSame($product->id, $coverage->id);
        $this->assertSame($coverage->id, $product->coverages()->firstOrFail()->id);
    }

    public function test_dealer_enablement_is_modeled_as_organization_to_product_availability(): void
    {
        $organization = Organization::query()->firstOrFail();
        $product = Product::query()->firstOrFail();

        $enablement = $organization->productEnablements()->firstOrFail();

        $this->assertSame($organization->id, $enablement->organization_id);
        $this->assertSame($product->id, $enablement->product_id);
        $this->assertSame('enabled', $enablement->status);
        $this->assertSame('bootstrap', $enablement->source);
        $this->assertSame($product->company_id, $enablement->product->company_id);
    }
}
