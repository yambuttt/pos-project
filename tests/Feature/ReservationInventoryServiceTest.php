<?php

namespace Tests\Feature;

use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\ProductRecipe;
use App\Models\RawMaterial;
use App\Models\Reservation;
use App\Models\ReservationItem;
use App\Models\ReservationMaterialLock;
use App\Models\ReservationResource;
use App\Services\ReservationInventoryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReservationInventoryServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_regular_reservation_locks_stock_when_dp_is_confirmed(): void
    {
        [$reservation, $material] = $this->regularReservationFixture();

        app(ReservationInventoryService::class)->lockForReservation($reservation);

        $this->assertDatabaseHas('reservation_material_locks', [
            'reservation_id' => $reservation->id,
            'raw_material_id' => $material->id,
            'qty_locked' => 6,
            'qty_released' => 0,
            'qty_consumed' => 0,
        ]);

        $this->assertSame(6.0, $material->fresh()->reserved_stock);
        $this->assertDatabaseHas('inventory_movements', [
            'raw_material_id' => $material->id,
            'type' => 'reserve',
            'reference_type' => Reservation::class,
            'reference_id' => $reservation->id,
        ]);
    }

    public function test_regular_reservation_checkout_consumes_locked_stock(): void
    {
        [$reservation, $material] = $this->regularReservationFixture();
        $service = app(ReservationInventoryService::class);

        $service->lockForReservation($reservation);
        $reservation->update(['status' => 'checked_in']);

        $service->consumeOnCheckout($reservation->fresh());

        $material->refresh();
        $this->assertSame(94.0, $material->stock_on_hand);
        $this->assertSame(0.0, $material->reserved_stock);

        $lock = ReservationMaterialLock::where('reservation_id', $reservation->id)->firstOrFail();
        $this->assertSame(6.0, $lock->qty_consumed);

        $this->assertDatabaseHas('inventory_movements', [
            'raw_material_id' => $material->id,
            'type' => 'commit_paid',
            'qty_out' => 6,
            'reference_type' => Reservation::class,
            'reference_id' => $reservation->id,
        ]);
    }

    public function test_checkout_creates_missing_lock_for_existing_checked_in_reservation(): void
    {
        [$reservation, $material] = $this->regularReservationFixture([
            'status' => 'checked_in',
        ]);

        app(ReservationInventoryService::class)->consumeOnCheckout($reservation);

        $material->refresh();
        $this->assertSame(94.0, $material->stock_on_hand);
        $this->assertSame(0.0, $material->reserved_stock);

        $this->assertDatabaseCount('reservation_material_locks', 1);
        $this->assertSame(1, InventoryMovement::where('type', 'reserve')->count());
        $this->assertSame(1, InventoryMovement::where('type', 'commit_paid')->count());
    }

    private function regularReservationFixture(array $reservationOverrides = []): array
    {
        $material = RawMaterial::create([
            'name' => 'Beras',
            'unit' => 'kg',
            'stock_on_hand' => 100,
            'reserved_stock' => 0,
            'min_stock' => 5,
            'default_cost' => 10000,
            'is_active' => true,
        ]);

        $product = Product::create([
            'name' => 'Nasi Goreng',
            'sku' => 'NG-TEST',
            'category' => 'Food',
            'price' => 22000,
            'is_active' => true,
        ]);

        ProductRecipe::create([
            'product_id' => $product->id,
            'raw_material_id' => $material->id,
            'qty' => 1.5,
        ]);

        $resource = ReservationResource::create([
            'type' => 'ROOM',
            'name' => 'VIP Room',
            'capacity' => 20,
            'hourly_rate' => 50000,
            'flat_rate' => null,
            'min_duration_minutes' => 60,
            'buffer_minutes' => 0,
            'is_active' => true,
        ]);

        $reservation = Reservation::create(array_merge([
            'code' => 'RSV-TEST',
            'reservation_resource_id' => $resource->id,
            'customer_name' => 'Customer Test',
            'customer_phone' => '0800000000',
            'start_at' => now()->addDay(),
            'end_at' => now()->addDay()->addHour(),
            'pax' => null,
            'menu_type' => 'REGULAR',
            'status' => 'confirmed',
            'menu_total' => 88000,
            'rental_total' => 50000,
            'grand_total' => 138000,
            'dp_amount' => 69000,
            'paid_amount' => 69000,
        ], $reservationOverrides));

        ReservationItem::create([
            'reservation_id' => $reservation->id,
            'item_type' => 'REGULAR_PRODUCT',
            'item_id' => $product->id,
            'snapshot_name' => $product->name,
            'unit_price' => 22000,
            'qty' => 4,
            'subtotal' => 88000,
        ]);

        return [$reservation, $material, $product];
    }
}
