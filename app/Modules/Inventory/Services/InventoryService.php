<?php

namespace App\Modules\Inventory\Services;

use App\Modules\Inventory\Models\Inventory;
use App\Modules\Inventory\Models\StockMovement;
use Exception;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    /**
     * Get current stock quantity for a product in a warehouse.
     */
    public function getStock(int $warehouseId, int $productId): int
    {
        $inventory = Inventory::where('warehouse_id', $warehouseId)
            ->where('product_id', $productId)
            ->first();

        return $inventory ? $inventory->quantity : 0;
    }

    /**
     * Add stock (Stock In).
     */
    public function addStock(int $warehouseId, int $productId, int $quantity, string $reason, ?int $userId = null): StockMovement
    {
        return $this->createMovement($warehouseId, $productId, 'in', $quantity, $reason, $userId);
    }

    /**
     * Remove stock (Stock Out).
     * Throws exception if insufficient stock.
     */
    public function removeStock(int $warehouseId, int $productId, int $quantity, string $reason, ?int $userId = null): StockMovement
    {
        return $this->createMovement($warehouseId, $productId, 'out', $quantity, $reason, $userId);
    }

    /**
     * Adjust stock (correction).
     * Can be positive (add) or negative (subtract).
     * Stored as 'adjustment' type.
     * If quantity is negative, we treat it as removing stock (absolute value stored, but logic handles sign).
     * Wait, the schema has 'quantity' as integer. Let's say we store absolute value in DB, but 'adjustment' type might be ambiguous if we don't know direction.
     *
     * Strategy:
     * If adjustment is positive -> treat like IN
     * If adjustment is negative -> treat like OUT
     *
     * However, to keep the 'type' field clean, maybe we should just use 'in'/'out' for adjustments too?
     * The requirements say: Entities: StockMovement.
     * Let's stick to the schema: type enum('in', 'out', 'adjustment').
     * If type is 'adjustment', does it mean + or -?
     *
     * Better approach:
     * 'in' adds.
     * 'out' subtracts.
     * 'adjustment' -> if we allow signed quantity, we can just sum them.
     * But earlier I defined quantity as absolute.
     *
     * Let's refine the logic:
     * createMovement will take the signed quantity for logic, but store absolute.
     * Actually, strictly speaking, 'out' means negative flow.
     *
     * Let's enforce:
     * IN: quantity must be > 0.
     * OUT: quantity must be > 0.
     * ADJUSTMENT: quantity can be positive or negative?
     *
     * If I store absolute quantity, I need a way to know if adjustment is + or -.
     * I will change the logic to:
     * If I want to adjust -5, I create a movement of type 'adjustment' with quantity 5, and some flag?
     * Or, I can just use 'in'/'out' for everything and use 'reason' to say "Adjustment".
     *
     * BUT, user requirements might want to distinguish "Sales" vs "Adjustment".
     *
     * Let's stick to:
     * 'in' -> +
     * 'out' -> -
     * 'adjustment' -> This is tricky with absolute quantity.
     *
     * Alternative: 'adjustment_in', 'adjustment_out'? No, schema is set.
     *
     * Let's assume 'adjustment' with positive quantity = add, negative quantity = subtract?
     * But DB schema `quantity` is integer, usually unsigned in concept but signed in SQL (int).
     *
     * Let's allow negative quantity in DB for `adjustment`?
     * The schema: `$table->integer('quantity');` (signed).
     *
     * So:
     * IN: +qty
     * OUT: -qty (stored as positive in DB? No, usually stock ledger stores signed values or has direction).
     *
     * Let's look at `StockMovement` model again.
     * `$table->enum('type', ['in', 'out', 'adjustment']);`
     * `$table->integer('quantity');`
     *
     * I will decide:
     * `in`: always positive effect.
     * `out`: always negative effect.
     * `adjustment`: can be positive or negative effect.
     *
     * So when calculating inventory:
     * Sum = (SUM(quantity where type='in') - SUM(quantity where type='out') + SUM(quantity where type='adjustment'))
     *
     * Wait, if adjustment is negative, we add a negative number. Correct.
     *
     * So `removeStock` calls `createMovement(..., 'out', $qty)`.
     * `createMovement` logic:
     *   if 'out', effect is -$qty.
     *   if 'in', effect is +$qty.
     *   if 'adjustment', effect is +$qty (so if you want to reduce, pass negative qty).
     *
     * Re-reading "Stock cannot go below zero".
     */
    private function createMovement(int $warehouseId, int $productId, string $type, int $quantity, string $reason, ?int $userId): StockMovement
    {
        return DB::transaction(function () use ($warehouseId, $productId, $type, $quantity, $reason, $userId) {
            $inventory = Inventory::firstOrCreate(
                ['warehouse_id' => $warehouseId, 'product_id' => $productId],
                ['quantity' => 0]
            );

            $currentStock = $inventory->quantity;
            $newStock = $currentStock;

            // Determine the effect on stock
            if ($type === 'in') {
                if ($quantity <= 0) {
                    throw new Exception('Stock In quantity must be positive.');
                }
                $newStock += $quantity;
            } elseif ($type === 'out') {
                if ($quantity <= 0) {
                    throw new Exception('Stock Out quantity must be positive.');
                }
                $newStock -= $quantity;
            } elseif ($type === 'adjustment') {
                // Adjustment can be positive or negative
                $newStock += $quantity;
            }

            // Validation: Prevent negative stock
            if ($newStock < 0) {
                throw new Exception("Insufficient stock. Current: {$currentStock}, Requested change results in: {$newStock}");
            }

            $inventory->quantity = $newStock;
            $inventory->save();

            return StockMovement::create([
                'inventory_id' => $inventory->id,
                'type' => $type,
                'quantity' => $quantity, // Store the signed value for adjustment? Or absolute?
                // If I store absolute for IN/OUT, but signed for ADJUSTMENT, it's inconsistent.
                // Let's store the raw quantity passed.
                // For 'out', we passed positive 5, so we store 5. Logic knows 'out' means subtract.
                // For 'adjustment', if we pass -5, we store -5. Logic knows 'adjustment' adds the value.
                'reason' => $reason,
                'user_id' => $userId,
            ]);
        });
    }
}
