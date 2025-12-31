# Database Schema - Simple View

## Entity Relationship Diagram (Text Format)

```
┌─────────────────┐
│     USERS       │
├─────────────────┤
│ id (PK)         │
│ name            │
│ email (UK)      │
│ password        │
└────────┬────────┘
         │
         │ creates
         │
         ├──────────────────┐
         │                  │
         ▼                  ▼
┌─────────────────┐  ┌─────────────────┐
│    PAYMENTS     │  │   DELIVERIES    │
├─────────────────┤  ├─────────────────┤
│ id (PK)         │  │ id (PK)         │
│ code (UK)       │  │ code (UK)       │
│ payable_type    │  │ deliverable_type│
│ payable_id      │  │ deliverable_id  │
│ payment_method  │  │ delivery_method │
│ amount          │  │ status          │
│ type            │  │ tracking_number│
│ status          │  │ delivery_cost   │
│ created_by (FK) │  │ created_by (FK) │
└────────┬────────┘  └────────┬────────┘
         │                    │
         │ uses              │ uses
         │                    │
         ▼                    ▼
┌─────────────────┐  ┌─────────────────┐
│ PAYMENT_METHODS │  │ DELIVERY_METHODS│
├─────────────────┤  ├─────────────────┤
│ id (PK)         │  │ id (PK)         │
│ code (UK)       │  │ code (UK)       │
│ name            │  │ name            │
│ is_active       │  │ base_cost       │
└─────────────────┘  └─────────────────┘

┌─────────────────┐
│ NUMBERING_SYSTEM│
├─────────────────┤
│ id (PK)         │
│ domain          │
│ type            │
│ template        │
│ next_trick      │
└────────┬────────┘
         │
         │ generates codes for
         │
         ├──────────────────┬──────────────────┐
         │                  │                  │
         ▼                  ▼                  ▼
┌─────────────────┐  ┌─────────────────┐  ┌─────────────────┐
│   CATEGORIES    │  │     BRANDS      │  │  PAYMENT_METHODS│
├─────────────────┤  ├─────────────────┤  ├─────────────────┤
│ id (PK)         │  │ id (PK)         │  │ id (PK)         │
│ code (UK)       │  │ code (UK)       │  │ code (UK)       │
│ title           │  │ title           │  │ name            │
│ status          │  │ status          │  │ is_active       │
└─────────────────┘  └─────────────────┘  └─────────────────┘
```

## Table List

### Core System
- **users** - User accounts
- **numbering_systems** - Code generation configuration

### Structure
- **categories** - Product categories
- **brands** - Product brands

### Payment System
- **payment_methods** - Payment method definitions
- **payments** - Payment transactions (polymorphic)

### Delivery System
- **delivery_methods** - Delivery method definitions
- **deliveries** - Delivery tracking (polymorphic)

## Key Relationships

1. **Polymorphic Payments**
   - payments.payable_type + payments.payable_id → Any entity (SalesOrder, PurchaseOrder, etc.)

2. **Polymorphic Deliveries**
   - deliveries.deliverable_type + deliveries.deliverable_id → Any entity (SalesOrder, PurchaseOrder, etc.)

3. **Code Generation**
   - All entities with `code` field → numbering_systems (by domain + type)

4. **User Tracking**
   - payments.created_by → users.id
   - deliveries.created_by → users.id

