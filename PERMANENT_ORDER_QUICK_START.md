# Permanent Order System - Quick Setup Guide

## What's Been Added

A complete **Permanent Order System** has been implemented for businesses that deal with construction materials (rod, cement, etc.) where customers need to place pre-orders with credit/due payment options.

## Files Created/Modified

### Database Migrations (✅ Already Run)
- `2025_12_22_054531_create_permanent_orders_table.php`
- `2025_12_22_054557_add_permanent_order_settings_to_businesses_table.php`

### Models
- `app/Models/PermanentOrder.php` - Main model with auto-calculations
- `app/Models/Business.php` - Updated with new settings

### Controllers
- `app/Http/Controllers/Manager/PermanentOrderController.php` - Full CRUD + voucher
- `app/Http/Controllers/SuperAdmin/BusinessController.php` - Updated validation

### Views
- `resources/views/manager/permanent-orders/index.blade.php` - Order list
- `resources/views/manager/permanent-orders/create.blade.php` - Create form
- `resources/views/manager/permanent-orders/edit.blade.php` - Edit form
- `resources/views/manager/permanent-orders/show.blade.php` - Order details
- `resources/views/manager/permanent-orders/voucher.blade.php` - Printable voucher
- `resources/views/superadmin/businesses/edit.blade.php` - Updated with new settings

### Routes
- Added to `routes/web.php` under Manager section

### Documentation
- `PERMANENT_ORDER_SYSTEM.md` - Complete documentation
- `README.md` - Updated with new features

## How to Use

### For Super Admin:
1. Login as Super Admin
2. Go to **Businesses** menu
3. Click **Edit** on any business
4. Scroll to **স্থায়ী অর্ডার সেটিংস** section
5. Check boxes:
   - ✅ স্থায়ী অর্ডার সিস্টেম চালু করুন
   - ✅ ক্রেডিট/বাকি সিস্টেম চালু করুন (optional)
   - Set credit limit (e.g., 100000)
6. Click **আপডেট করুন**

### For Manager:
1. Login as Manager
2. Go to **স্থায়ী অর্ডার** menu (new menu item)
3. Click **নতুন অর্ডার** button
4. Fill in the form:
   - Customer details (name, phone, address)
   - Select product
   - Enter quantity
   - Unit price (auto-filled from product)
   - Paid amount
   - Delivery date (optional)
   - Notes (optional)
5. Click **অর্ডার তৈরি করুন**
6. View/Edit/Print vouchers from the list

## Key Features

### Auto-Calculations
- Total amount = quantity × unit price
- Due amount = total - paid
- Status updates automatically based on payment

### Voucher System
- Unique voucher numbers (PO-XXXXX)
- Beautiful printable design
- Customer and product details
- Payment summary
- Signature section

### Credit Control
- Per-business credit limits
- Per-customer due tracking
- Automatic limit checking
- Super Admin control

### Status Management
- **Active** (সক্রিয়) - New order, no payment
- **Partial** (আংশিক) - Some payment made
- **Completed** (সম্পন্ন) - Fully paid
- **Cancelled** (বাতিল) - Order cancelled

## Database Schema

### permanent_orders
```
- id
- business_id (FK)
- customer_name
- customer_phone
- customer_address
- product_id (FK)
- quantity
- unit_price
- total_amount
- paid_amount
- due_amount
- voucher_number (unique)
- status (enum)
- order_date
- delivery_date
- notes
- created_by (FK)
- timestamps
```

### businesses (new columns)
```
- enable_permanent_orders (boolean)
- enable_credit_system (boolean)
- credit_limit (decimal)
```

## Testing

### Test Scenario 1: Enable System
1. Login as Super Admin
2. Edit a business
3. Enable permanent orders
4. Set credit limit to 100000
5. Save

### Test Scenario 2: Create Order
1. Login as Manager
2. Go to permanent orders
3. Create new order:
   - Customer: "জামাল মিয়া"
   - Phone: "01711111111"
   - Product: Select any
   - Quantity: 100
   - Paid: 50000
4. Submit
5. Verify voucher number generated
6. Print voucher

### Test Scenario 3: Edit Order
1. Open any order
2. Add more payment
3. Verify status changes to "Partial" or "Completed"
4. Check due amount recalculated

### Test Scenario 4: Credit Limit
1. Create order for customer with high due
2. Try to exceed credit limit
3. Should show error message

## Production Ready

✅ **Yes!** The system is production-ready:
- All migrations run successfully
- Models with proper relationships
- Validation in place
- Error handling
- Beautiful UI
- Printable vouchers
- Super Admin controls

## What's Next

Optional enhancements:
- [ ] Payment history tracking
- [ ] SMS notifications
- [ ] Customer profile page
- [ ] Monthly reports
- [ ] Payment reminders
- [ ] Bulk import
- [ ] Owner role access

## Support

For detailed documentation, see: [PERMANENT_ORDER_SYSTEM.md](PERMANENT_ORDER_SYSTEM.md)

---
**Added on:** December 22, 2025
**Status:** ✅ Production Ready
