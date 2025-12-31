# Payment, Deposit & Delivery System Design

## Overview
This document outlines a comprehensive approach to handle **Deposit**, **Payment**, and **Delivery** as parameters in the system, integrated with Sales and Purchase orders.

## Database Structure

### 1. Payment Methods Table
Stores available payment methods (Cash, Bank Transfer, Credit Card, etc.)

```php
Schema::create('payment_methods', function (Blueprint $table) {
    $table->id();
    $table->string('code')->unique(); // PM-001
    $table->string('name'); // Cash, Bank Transfer, Credit Card
    $table->text('description')->nullable();
    $table->boolean('requires_reference')->default(false); // For bank transfers
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});
```

### 2. Payments Table
Tracks all payments made (full payments, deposits, partial payments)

```php
Schema::create('payments', function (Blueprint $table) {
    $table->id();
    $table->string('code')->unique(); // PAY-2024-001
    $table->morphs('payable'); // Polymorphic: can be linked to Sale, Purchase, etc.
    $table->foreignId('payment_method_id')->constrained();
    $table->decimal('amount', 15, 2);
    $table->decimal('paid_amount', 15, 2)->default(0); // Amount actually received
    $table->enum('type', ['deposit', 'partial', 'full', 'refund'])->default('full');
    $table->enum('status', ['pending', 'completed', 'failed', 'cancelled'])->default('pending');
    $table->date('payment_date');
    $table->date('due_date')->nullable();
    $table->string('reference_number')->nullable(); // Bank reference, check number, etc.
    $table->text('notes')->nullable();
    $table->foreignId('created_by')->constrained('users');
    $table->timestamps();
    
    $table->index(['payable_type', 'payable_id']);
    $table->index('status');
    $table->index('payment_date');
});
```

### 3. Delivery Methods Table
Stores delivery/shipping methods

```php
Schema::create('delivery_methods', function (Blueprint $table) {
    $table->id();
    $table->string('code')->unique(); // DM-001
    $table->string('name'); // Standard Shipping, Express, Pickup
    $table->text('description')->nullable();
    $table->decimal('base_cost', 10, 2)->default(0);
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});
```

### 4. Deliveries Table
Tracks delivery status and information

```php
Schema::create('deliveries', function (Blueprint $table) {
    $table->id();
    $table->string('code')->unique(); // DEL-2024-001
    $table->morphs('deliverable'); // Polymorphic: can be linked to Sale, Purchase, etc.
    $table->foreignId('delivery_method_id')->constrained();
    $table->enum('status', [
        'pending', 
        'preparing', 
        'in_transit', 
        'out_for_delivery', 
        'delivered', 
        'failed', 
        'cancelled'
    ])->default('pending');
    $table->date('scheduled_date')->nullable();
    $table->date('delivered_date')->nullable();
    $table->decimal('delivery_cost', 10, 2)->default(0);
    $table->string('tracking_number')->nullable();
    $table->text('delivery_address')->nullable();
    $table->text('notes')->nullable();
    $table->foreignId('created_by')->constrained('users');
    $table->timestamps();
    
    $table->index(['deliverable_type', 'deliverable_id']);
    $table->index('status');
    $table->index('scheduled_date');
});
```

### 5. Sales Orders Table (Example Integration)
Shows how to integrate with sales

```php
Schema::create('sales_orders', function (Blueprint $table) {
    $table->id();
    $table->string('code')->unique(); // SO-24-0001
    $table->foreignId('customer_id')->nullable()->constrained('customers');
    $table->decimal('subtotal', 15, 2);
    $table->decimal('tax_amount', 15, 2)->default(0);
    $table->decimal('discount_amount', 15, 2)->default(0);
    $table->decimal('total_amount', 15, 2);
    $table->decimal('deposit_amount', 15, 2)->default(0);
    $table->decimal('paid_amount', 15, 2)->default(0);
    $table->decimal('remaining_amount', 15, 2);
    $table->enum('payment_status', [
        'unpaid', 
        'partial', 
        'deposit_paid', 
        'paid', 
        'overpaid'
    ])->default('unpaid');
    $table->enum('delivery_status', [
        'not_required',
        'pending',
        'in_progress',
        'delivered',
        'cancelled'
    ])->default('not_required');
    $table->enum('order_status', [
        'draft',
        'pending',
        'confirmed',
        'processing',
        'completed',
        'cancelled'
    ])->default('draft');
    $table->date('order_date');
    $table->date('due_date')->nullable();
    $table->text('notes')->nullable();
    $table->foreignId('created_by')->constrained('users');
    $table->timestamps();
    
    $table->index('order_status');
    $table->index('payment_status');
    $table->index('order_date');
});
```

## Model Relationships

### Payment Model
```php
class Payment extends Model
{
    protected $fillable = [
        'code', 'payment_method_id', 'amount', 'paid_amount',
        'type', 'status', 'payment_date', 'due_date',
        'reference_number', 'notes', 'created_by'
    ];
    
    protected $casts = [
        'amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'payment_date' => 'date',
        'due_date' => 'date',
    ];
    
    // Polymorphic relationship
    public function payable()
    {
        return $this->morphTo();
    }
    
    // Relationships
    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }
    
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
```

### Delivery Model
```php
class Delivery extends Model
{
    protected $fillable = [
        'code', 'delivery_method_id', 'status',
        'scheduled_date', 'delivered_date', 'delivery_cost',
        'tracking_number', 'delivery_address', 'notes', 'created_by'
    ];
    
    protected $casts = [
        'delivery_cost' => 'decimal:2',
        'scheduled_date' => 'date',
        'delivered_date' => 'date',
    ];
    
    // Polymorphic relationship
    public function deliverable()
    {
        return $this->morphTo();
    }
    
    // Relationships
    public function deliveryMethod()
    {
        return $this->belongsTo(DeliveryMethod::class);
    }
    
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
```

### Sales Order Model (Example)
```php
class SalesOrder extends Model
{
    // Relationships
    public function payments()
    {
        return $this->morphMany(Payment::class, 'payable');
    }
    
    public function delivery()
    {
        return $this->morphOne(Delivery::class, 'deliverable');
    }
    
    // Helper methods
    public function calculatePaymentStatus()
    {
        $totalPaid = $this->payments()
            ->where('status', 'completed')
            ->sum('paid_amount');
        
        if ($totalPaid == 0) {
            return 'unpaid';
        } elseif ($totalPaid >= $this->total_amount) {
            return 'paid';
        } elseif ($totalPaid >= $this->deposit_amount && $this->deposit_amount > 0) {
            return 'deposit_paid';
        } else {
            return 'partial';
        }
    }
    
    public function updatePaymentStatus()
    {
        $this->paid_amount = $this->payments()
            ->where('status', 'completed')
            ->sum('paid_amount');
        $this->remaining_amount = $this->total_amount - $this->paid_amount;
        $this->payment_status = $this->calculatePaymentStatus();
        $this->save();
    }
}
```

## Usage Examples

### 1. Creating a Payment (Deposit)
```php
// When creating a sales order with deposit
$salesOrder = SalesOrder::create([...]);

// Create deposit payment
$deposit = Payment::create([
    'code' => Payment::generateCode(),
    'payable_type' => SalesOrder::class,
    'payable_id' => $salesOrder->id,
    'payment_method_id' => $paymentMethodId,
    'amount' => 500.00, // Deposit amount
    'type' => 'deposit',
    'status' => 'completed',
    'payment_date' => now(),
]);

// Update sales order payment status
$salesOrder->updatePaymentStatus();
```

### 2. Creating Full Payment
```php
$payment = Payment::create([
    'code' => Payment::generateCode(),
    'payable_type' => SalesOrder::class,
    'payable_id' => $salesOrder->id,
    'payment_method_id' => $paymentMethodId,
    'amount' => $salesOrder->remaining_amount,
    'type' => 'full',
    'status' => 'completed',
    'payment_date' => now(),
]);

$salesOrder->updatePaymentStatus();
```

### 3. Creating Delivery
```php
$delivery = Delivery::create([
    'code' => Delivery::generateCode(),
    'deliverable_type' => SalesOrder::class,
    'deliverable_id' => $salesOrder->id,
    'delivery_method_id' => $deliveryMethodId,
    'status' => 'pending',
    'scheduled_date' => now()->addDays(3),
    'delivery_cost' => 25.00,
    'delivery_address' => $customer->address,
]);

// Update sales order delivery status
$salesOrder->delivery_status = 'pending';
$salesOrder->save();
```

### 4. Updating Delivery Status
```php
$delivery->update([
    'status' => 'in_transit',
    'tracking_number' => 'TRACK123456',
]);

// When delivered
$delivery->update([
    'status' => 'delivered',
    'delivered_date' => now(),
]);

$salesOrder->delivery_status = 'delivered';
$salesOrder->save();
```

## API Endpoints Structure

### Payments
- `GET /api/payments` - List all payments (with filters)
- `POST /api/payments` - Create new payment
- `GET /api/payments/{id}` - Get payment details
- `PUT /api/payments/{id}` - Update payment
- `DELETE /api/payments/{id}` - Cancel payment
- `GET /api/sales-orders/{id}/payments` - Get payments for a sales order
- `POST /api/sales-orders/{id}/payments` - Add payment to sales order

### Deliveries
- `GET /api/deliveries` - List all deliveries
- `POST /api/deliveries` - Create new delivery
- `GET /api/deliveries/{id}` - Get delivery details
- `PUT /api/deliveries/{id}` - Update delivery
- `PATCH /api/deliveries/{id}/status` - Update delivery status
- `GET /api/sales-orders/{id}/delivery` - Get delivery for a sales order

## Frontend Components Structure

### Payment Components
- `PaymentMethodSelector.vue` - Select payment method
- `PaymentForm.vue` - Create/edit payment
- `PaymentList.vue` - Display payments for an order
- `PaymentStatusBadge.vue` - Show payment status
- `DepositForm.vue` - Special form for deposits

### Delivery Components
- `DeliveryMethodSelector.vue` - Select delivery method
- `DeliveryForm.vue` - Create/edit delivery
- `DeliveryTracking.vue` - Track delivery status
- `DeliveryStatusTimeline.vue` - Visual timeline of delivery progress

## Key Features

### 1. Flexible Payment Handling
- Support multiple payment methods
- Track deposits separately from full payments
- Handle partial payments
- Support refunds
- Automatic payment status calculation

### 2. Delivery Tracking
- Multiple delivery methods
- Status tracking with timeline
- Tracking number support
- Scheduled delivery dates
- Delivery cost calculation

### 3. Integration Points
- Works with Sales Orders
- Works with Purchase Orders
- Polymorphic relationships allow flexibility
- Auto-generated codes using NumberingSystem
- Status synchronization

## Status Flow Examples

### Payment Status Flow
```
unpaid → deposit_paid → partial → paid
```

### Delivery Status Flow
```
pending → preparing → in_transit → out_for_delivery → delivered
```

## Benefits of This Design

1. **Flexibility**: Polymorphic relationships allow payments/deliveries to work with any entity
2. **Scalability**: Easy to add new payment/delivery methods
3. **Traceability**: All payments and deliveries are tracked with codes
4. **Integration**: Works seamlessly with existing numbering system
5. **Status Management**: Automatic status calculation and updates
6. **Reporting**: Easy to generate reports on payments and deliveries

