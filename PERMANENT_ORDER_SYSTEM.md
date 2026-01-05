# Permanent Order System Documentation

## Overview
স্থায়ী অর্ডার সিস্টেম একটি বিশেষ ফিচার যা ব্যবসায়ীদের জন্য ডিজাইন করা হয়েছে যারা রড, সিমেন্ট এবং অন্যান্য নির্মাণ সামগ্রী বিক্রয় করেন। এই সিস্টেমের মাধ্যমে গ্রাহকরা স্থায়ী/পূর্ব অর্ডার দিতে পারবেন এবং বাকিতে পণ্য নিতে পারবেন।

## Features

### 1. স্থায়ী অর্ডার তৈরি
- গ্রাহকের সম্পূর্ণ তথ্য সংরক্ষণ (নাম, ফোন, ঠিকানা)
- পণ্য নির্বাচন এবং পরিমাণ নির্ধারণ
- ইউনিট মূল্য এবং মোট টাকা গণনা
- আংশিক পেমেন্ট গ্রহণ
- ডেলিভারি তারিখ নির্ধারণ
- বিশেষ নোট যোগ করা

### 2. ভাউচার সিস্টেম
- প্রতিটি অর্ডারের জন্য অনন্য ভাউচার নম্বর (PO-XXXXX)
- সুন্দর প্রিন্টযোগ্য ভাউচার ডিজাইন
- গ্রাহক এবং পণ্য তথ্য সহ
- পেমেন্ট স্ট্যাটাস প্রদর্শন

### 3. বাকি/ক্রেডিট সিস্টেম
- গ্রাহক প্রতি ক্রেডিট লিমিট নির্ধারণ
- স্বয়ংক্রিয় বাকি হিসাব
- আংশিক পেমেন্ট ট্র্যাকিং
- পেমেন্ট স্ট্যাটাস (সক্রিয়, আংশিক, সম্পন্ন, বাতিল)

### 4. Super Admin নিয়ন্ত্রণ
- প্রতিটি কোম্পানির জন্য আলাদাভাবে চালু/বন্ধ করা যায়
- ক্রেডিট সিস্টেম চালু/বন্ধ
- ক্রেডিট লিমিট নির্ধারণ

## Database Schema

### permanent_orders Table
```sql
- id (Primary Key)
- business_id (Foreign Key -> businesses)
- customer_name (String)
- customer_phone (String, Indexed)
- customer_address (Text, Nullable)
- product_id (Foreign Key -> products)
- quantity (Decimal 10,2)
- unit_price (Decimal 10,2)
- total_amount (Decimal 10,2)
- paid_amount (Decimal 10,2, Default: 0)
- due_amount (Decimal 10,2)
- voucher_number (String, Unique, Indexed)
- status (Enum: active, partial, completed, cancelled)
- order_date (Date)
- delivery_date (Date, Nullable)
- notes (Text, Nullable)
- created_by (Foreign Key -> users)
- timestamps
```

### businesses Table (New Columns)
```sql
- enable_permanent_orders (Boolean, Default: false)
- enable_credit_system (Boolean, Default: false)
- credit_limit (Decimal 12,2, Nullable)
```

## User Roles & Permissions

### Super Admin
- ✅ কোম্পানির জন্য স্থায়ী অর্ডার চালু/বন্ধ করতে পারবেন
- ✅ ক্রেডিট সিস্টেম সেটিংস নিয়ন্ত্রণ
- ✅ ক্রেডিট লিমিট নির্ধারণ

### Manager
- ✅ স্থায়ী অর্ডার তৈরি করতে পারবেন
- ✅ অর্ডার সম্পাদনা করতে পারবেন
- ✅ অর্ডার ডিলিট করতে পারবেন
- ✅ ভাউচার প্রিন্ট করতে পারবেন
- ✅ পেমেন্ট আপডেট করতে পারবেন
- ✅ সকল অর্ডার দেখতে পারবেন

### Owner
- ✅ ম্যানেজারের সকল অধিকার (ভবিষ্যতে যোগ করা যাবে)

## Setup Instructions

### 1. Run Migrations
```bash
php artisan migrate
```

এই কমান্ড দুইটি নতুন মাইগ্রেশন চালাবে:
- `create_permanent_orders_table` - স্থায়ী অর্ডার টেবিল
- `add_permanent_order_settings_to_businesses_table` - ব্যবসায় সেটিংস

### 2. Enable for a Company (Super Admin)

1. Super Admin হিসেবে লগইন করুন
2. **Businesses** মেনুতে যান
3. যে কোম্পানির জন্য চালু করতে চান সেটি এডিট করুন
4. **স্থায়ী অর্ডার সেটিংস** সেকশনে:
   - ✅ "স্থায়ী অর্ডার সিস্টেম চালু করুন" চেক করুন
   - ✅ "ক্রেডিট/বাকি সিস্টেম চালু করুন" চেক করুন (ঐচ্ছিক)
   - ক্রেডিট লিমিট সেট করুন (যেমন: 100000)
5. **আপডেট করুন** বাটনে ক্লিক করুন

### 3. Create Permanent Orders (Manager)

1. Manager হিসেবে লগইন করুন
2. **স্থায়ী অর্ডার** মেনুতে যান
3. **নতুন অর্ডার** বাটনে ক্লিক করুন
4. ফর্ম পূরণ করুন:
   - গ্রাহকের নাম *
   - ফোন নম্বর *
   - ঠিকানা
   - পণ্য নির্বাচন *
   - পরিমাণ *
   - একক মূল্য * (স্বয়ংক্রিয় পূরণ হবে)
   - পরিশোধিত টাকা
   - ডেলিভারি তারিখ
   - নোট
5. **অর্ডার তৈরি করুন** বাটনে ক্লিক করুন

## Routes

### Manager Routes
```php
GET    /manager/permanent-orders              // সকল অর্ডার দেখুন
GET    /manager/permanent-orders/create       // নতুন অর্ডার ফর্ম
POST   /manager/permanent-orders              // অর্ডার তৈরি করুন
GET    /manager/permanent-orders/{id}         // অর্ডার বিস্তারিত
GET    /manager/permanent-orders/{id}/edit    // অর্ডার এডিট ফর্ম
PUT    /manager/permanent-orders/{id}         // অর্ডার আপডেট করুন
DELETE /manager/permanent-orders/{id}         // অর্ডার ডিলিট করুন
GET    /manager/permanent-orders/{id}/voucher // ভাউচার প্রিন্ট
```

## Models & Relationships

### PermanentOrder Model
```php
// Relationships
belongsTo(Business::class)
belongsTo(Product::class)
belongsTo(User::class, 'created_by')

// Methods
isPaid(): bool
isPartiallyPaid(): bool
getDuePercentageAttribute(): float

// Auto Calculations
- voucher_number (Auto-generated on create)
- due_amount (Auto-calculated from total - paid)
- status (Auto-updated based on payment)
```

### Business Model
```php
// New Relationship
hasMany(PermanentOrder::class)

// New Attributes
enable_permanent_orders: boolean
enable_credit_system: boolean
credit_limit: decimal
```

## Validation Rules

### Creating Order
- `customer_name`: required, string, max:255
- `customer_phone`: required, string, max:20
- `customer_address`: nullable, string, max:500
- `product_id`: required, exists in products
- `quantity`: required, numeric, min:0.01
- `unit_price`: required, numeric, min:0
- `paid_amount`: nullable, numeric, min:0
- `delivery_date`: nullable, date, after_or_equal:today
- `notes`: nullable, string, max:1000

### Business Rules
- পরিশোধিত টাকা মোট টাকার চেয়ে বেশি হতে পারবে না
- ক্রেডিট লিমিট চালু থাকলে, গ্রাহকের মোট বাকি ক্রেডিট লিমিটের বেশি হতে পারবে না
- স্থায়ী অর্ডার সিস্টেম বন্ধ থাকলে নতুন অর্ডার তৈরি করা যাবে না

## Views

### Index View
- সকল অর্ডারের তালিকা
- ভাউচার নম্বর, গ্রাহক, পণ্য, পরিমাণ, মোট টাকা, বাকি
- স্ট্যাটাস ব্যাজ (সক্রিয়, আংশিক, সম্পন্ন, বাতিল)
- অ্যাকশন বাটন (দেখুন, সম্পাদনা, ভাউচার)
- পেজিনেশন

### Create View
- সম্পূর্ণ ফর্ম
- JavaScript দ্বারা স্বয়ংক্রিয় গণনা
- পণ্য নির্বাচনে স্বয়ংক্রিয় মূল্য পূরণ

### Edit View
- সকল ফিল্ড এডিট করা যায়
- স্ট্যাটাস পরিবর্তন করা যায়
- পেমেন্ট আপডেট করা যায়

### Show View
- সম্পূর্ণ অর্ডার বিবরণ
- সুন্দর UI ডিজাইন
- পেমেন্ট সারাংশ
- অ্যাকশন বাটন

### Voucher View
- প্রিন্টযোগ্য ডিজাইন
- কোম্পানি তথ্য
- গ্রাহক তথ্য
- পণ্য বিবরণ
- পেমেন্ট সারাংশ
- স্বাক্ষর সেকশন

## Use Cases

### রড/সিমেন্ট দোকানের জন্য
1. একজন ঠিকাদার 100 টন সিমেন্ট অর্ডার করেন
2. তিনি 50% অগ্রিম পেমেন্ট করেন
3. সিস্টেম স্বয়ংক্রিয়ভাবে বাকি হিসাব করে
4. ভাউচার প্রিন্ট করে গ্রাহককে দেওয়া হয়
5. পরে আরো পেমেন্ট করলে অর্ডার আপডেট করা হয়
6. পূর্ণ পেমেন্ট হলে স্ট্যাটাস "সম্পন্ন" হয়

### নির্মাণ সামগ্রী সরবরাহকারীর জন্য
1. গ্রাহক নিয়মিত রড কিনেন
2. প্রতিটি অর্ডারের জন্য আলাদা ভাউচার
3. একসাথে একাধিক অর্ডারের বাকি ট্র্যাক করা
4. ক্রেডিট লিমিট পার হলে সতর্কতা

## Future Enhancements

### সম্ভাব্য উন্নতি
- [ ] পেমেন্ট হিস্ট্রি ট্র্যাকিং
- [ ] SMS/Email নোটিফিকেশন
- [ ] গ্রাহক প্রোফাইল পেজ
- [ ] মাসিক/বার্ষিক রিপোর্ট
- [ ] পেমেন্ট রিমাইন্ডার
- [ ] বাল্ক অর্ডার ইমপোর্ট
- [ ] Owner রোলের জন্য এক্সেস

## Testing

### Manual Testing Checklist
- [ ] Super Admin বিজনেস সেটিংস আপডেট করতে পারেন
- [ ] সিস্টেম বন্ধ থাকলে Manager অর্ডার তৈরি করতে পারেন না
- [ ] সিস্টেম চালু থাকলে Manager অর্ডার তৈরি করতে পারেন
- [ ] ক্রেডিট লিমিট সঠিকভাবে কাজ করছে
- [ ] ভাউচার সঠিকভাবে প্রিন্ট হচ্ছে
- [ ] স্ট্যাটাস স্বয়ংক্রিয়ভাবে আপডেট হচ্ছে
- [ ] বাকি হিসাব সঠিক
- [ ] সকল ভ্যালিডেশন কাজ করছে

## Support

কোন সমস্যা হলে যোগাযোগ করুন:
- GitHub Issues: https://github.com/gsagg03-cmyk/ERP/issues
- Developer: gsagg03-cmyk

## Version History

### v1.0.0 (December 22, 2025)
- ✅ Initial release
- ✅ Permanent order CRUD operations
- ✅ Voucher system
- ✅ Credit/Due system
- ✅ Super Admin controls
- ✅ Manager interface
