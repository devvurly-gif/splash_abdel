# Quick Start: Payment, Deposit & Delivery Implementation

## Simplified Approach (Recommended for Start)

### Option 1: Embedded in Orders (Simpler)
Add payment and delivery fields directly to Sales/Purchase Orders table:

```php
// Add to sales_orders migration
$table->decimal('deposit_amount', 15, 2)->default(0);
$table->decimal('paid_amount', 15, 2)->default(0);
$table->enum('payment_method', ['cash', 'bank_transfer', 'credit_card', 'check'])->nullable();
$table->enum('payment_status', ['unpaid', 'deposit', 'partial', 'paid'])->default('unpaid');
$table->enum('delivery_method', ['pickup', 'standard', 'express'])->nullable();
$table->enum('delivery_status', ['not_required', 'pending', 'in_transit', 'delivered'])->default('not_required');
$table->date('delivery_date')->nullable();
$table->string('tracking_number')->nullable();
$table->text('delivery_address')->nullable();
```

**Pros:**
- Simple and fast to implement
- All data in one place
- Easy queries

**Cons:**
- Limited to one payment per order
- Can't track multiple payments
- Less flexible for complex scenarios

### Option 2: Separate Tables (More Flexible)
Use the full design from `PAYMENT_DELIVERY_DESIGN.md`

**Pros:**
- Multiple payments per order
- Full payment history
- Better for complex business needs
- Supports refunds and partial payments

**Cons:**
- More complex
- More tables to manage
- More code to write

## Recommended Implementation Steps

### Phase 1: Basic Implementation (Week 1)
1. Add payment/delivery fields to orders table
2. Create enums for payment/delivery methods
3. Update order forms to include these fields
4. Add status badges in order list

### Phase 2: Enhanced Features (Week 2)
1. Create separate payments table
2. Support multiple payments per order
3. Add payment history tracking
4. Implement deposit handling

### Phase 3: Advanced Features (Week 3)
1. Create deliveries table
2. Add delivery tracking
3. Implement status workflows
4. Add notifications

## Quick Code Examples

### Model with Embedded Fields
```php
// app/Models/SalesOrder.php
class SalesOrder extends Model
{
    protected $fillable = [
        'code', 'customer_id', 'subtotal', 'tax_amount',
        'discount_amount', 'total_amount',
        'deposit_amount', 'paid_amount',
        'payment_method', 'payment_status',
        'delivery_method', 'delivery_status',
        'delivery_date', 'tracking_number', 'delivery_address'
    ];
    
    protected $casts = [
        'deposit_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'delivery_date' => 'date',
    ];
    
    // Payment status calculation
    public function updatePaymentStatus()
    {
        if ($this->paid_amount == 0) {
            $this->payment_status = 'unpaid';
        } elseif ($this->paid_amount >= $this->total_amount) {
            $this->payment_status = 'paid';
        } elseif ($this->paid_amount >= $this->deposit_amount && $this->deposit_amount > 0) {
            $this->payment_status = 'deposit';
        } else {
            $this->payment_status = 'partial';
        }
        $this->save();
    }
    
    // Get remaining amount
    public function getRemainingAmountAttribute()
    {
        return $this->total_amount - $this->paid_amount;
    }
}
```

### Controller Method
```php
// app/Http/Controllers/SalesOrderController.php
public function store(Request $request)
{
    $validated = $request->validate([
        'customer_id' => 'required|exists:customers,id',
        'items' => 'required|array',
        'deposit_amount' => 'nullable|numeric|min:0',
        'payment_method' => 'nullable|in:cash,bank_transfer,credit_card,check',
        'delivery_method' => 'nullable|in:pickup,standard,express',
        'delivery_address' => 'nullable|string|max:500',
    ]);
    
    // Calculate totals
    $subtotal = 0;
    foreach ($validated['items'] as $item) {
        $subtotal += $item['quantity'] * $item['price'];
    }
    
    $total = $subtotal; // Add tax, discount calculations
    
    $order = SalesOrder::create([
        'code' => SalesOrder::generateCode(),
        'customer_id' => $validated['customer_id'],
        'subtotal' => $subtotal,
        'total_amount' => $total,
        'deposit_amount' => $validated['deposit_amount'] ?? 0,
        'paid_amount' => $validated['deposit_amount'] ?? 0,
        'payment_method' => $validated['payment_method'],
        'delivery_method' => $validated['delivery_method'],
        'delivery_address' => $validated['delivery_address'],
        'payment_status' => $validated['deposit_amount'] > 0 ? 'deposit' : 'unpaid',
        'delivery_status' => $validated['delivery_method'] ? 'pending' : 'not_required',
    ]);
    
    return response()->json([
        'success' => true,
        'data' => $order
    ]);
}
```

### Frontend Form Component
```vue
<!-- resources/js/components/OrderPaymentDeliveryForm.vue -->
<template>
    <div class="payment-delivery-section">
        <!-- Payment Section -->
        <div class="form-section">
            <h3>Payment Information</h3>
            
            <div class="form-group">
                <label>Deposit Amount</label>
                <input 
                    v-model="form.deposit_amount" 
                    type="number" 
                    step="0.01"
                    min="0"
                    :max="form.total_amount"
                />
            </div>
            
            <div class="form-group">
                <label>Payment Method</label>
                <select v-model="form.payment_method">
                    <option value="">Select Method</option>
                    <option value="cash">Cash</option>
                    <option value="bank_transfer">Bank Transfer</option>
                    <option value="credit_card">Credit Card</option>
                    <option value="check">Check</option>
                </select>
            </div>
            
            <div v-if="form.deposit_amount > 0" class="info-box">
                Remaining: {{ remainingAmount }}
            </div>
        </div>
        
        <!-- Delivery Section -->
        <div class="form-section">
            <h3>Delivery Information</h3>
            
            <div class="form-group">
                <label>Delivery Method</label>
                <select v-model="form.delivery_method">
                    <option value="">No Delivery</option>
                    <option value="pickup">Pickup</option>
                    <option value="standard">Standard Shipping</option>
                    <option value="express">Express Shipping</option>
                </select>
            </div>
            
            <div v-if="form.delivery_method" class="form-group">
                <label>Delivery Address</label>
                <textarea v-model="form.delivery_address" rows="3"></textarea>
            </div>
            
            <div v-if="form.delivery_method" class="form-group">
                <label>Scheduled Date</label>
                <input v-model="form.delivery_date" type="date" />
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    form: Object,
    totalAmount: Number
});

const remainingAmount = computed(() => {
    return (props.totalAmount || 0) - (props.form.deposit_amount || 0);
});
</script>
```

## Status Management

### Payment Status Logic
```php
// In SalesOrder model
public static function calculatePaymentStatus($total, $paid, $deposit = 0)
{
    if ($paid == 0) return 'unpaid';
    if ($paid >= $total) return 'paid';
    if ($deposit > 0 && $paid >= $deposit) return 'deposit';
    return 'partial';
}
```

### Delivery Status Logic
```php
// Delivery status progression
'not_required' → 'pending' → 'in_transit' → 'delivered'
```

## Database Seeder Example
```php
// Seed payment methods
PaymentMethod::create(['code' => 'PM-001', 'name' => 'Cash', 'is_active' => true]);
PaymentMethod::create(['code' => 'PM-002', 'name' => 'Bank Transfer', 'requires_reference' => true, 'is_active' => true]);
PaymentMethod::create(['code' => 'PM-003', 'name' => 'Credit Card', 'is_active' => true]);

// Seed delivery methods
DeliveryMethod::create(['code' => 'DM-001', 'name' => 'Pickup', 'base_cost' => 0, 'is_active' => true]);
DeliveryMethod::create(['code' => 'DM-002', 'name' => 'Standard Shipping', 'base_cost' => 10.00, 'is_active' => true]);
DeliveryMethod::create(['code' => 'DM-003', 'name' => 'Express Shipping', 'base_cost' => 25.00, 'is_active' => true]);
```

## Next Steps

1. **Choose your approach** (Embedded vs Separate Tables)
2. **Create migrations** for the chosen approach
3. **Update models** with relationships and helper methods
4. **Create controllers** with CRUD operations
5. **Build frontend components** for forms and displays
6. **Add validation** and business logic
7. **Test thoroughly** with different scenarios

## Questions to Consider

1. **Do you need multiple payments per order?** → Use separate tables
2. **Do you need payment history?** → Use separate tables
3. **Do you need refund tracking?** → Use separate tables
4. **Is one payment per order enough?** → Use embedded fields
5. **Do you need delivery tracking with timeline?** → Use separate tables

Choose based on your business requirements!

