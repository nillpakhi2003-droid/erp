# Voucher Template Management System

## Overview
Super Admin এখন voucher এর UI সম্পূর্ণভাবে customize করতে পারবে। প্রতিটি Owner এর জন্য আলাদা আলাদা design সেট করা যাবে।

## Features

### ✅ Customizable Design Elements
1. **Company Information**
   - Company Name
   - Address
   - Phone Number
   - Header Text
   - Footer Text

2. **Color Scheme**
   - Primary Color (Headings, Borders)
   - Secondary Color (Backgrounds, Accents)
   - Real-time color picker with hex code input

3. **Typography**
   - Font Size Options (11px - 15px)
   - Multiple size presets

4. **Layout**
   - Page Margin Control (3mm - 10mm)
   - Compact to spacious options

5. **Branding**
   - Logo URL support
   - Watermark toggle
   - Custom watermark text

## How to Use

### For Super Admin

#### Access Voucher Templates
1. Login as Super Admin
2. Go to Dashboard
3. Click "Voucher Templates" button
4. URL: `/superadmin/voucher-templates`

#### Create New Template
1. Click "+ নতুন টেমপ্লেট"
2. Select Owner
3. Fill in company details
4. Customize design:
   - Choose primary & secondary colors
   - Set font size
   - Adjust page margins
   - Add logo URL (optional)
   - Enable watermark (optional)
5. Click "টেমপ্লেট সেভ করুন"

#### Edit Existing Template
1. From templates list, click "Edit"
2. Modify any settings
3. Click "আপডেট করুন"

#### Delete Template
1. From templates list, click "Delete"
2. Confirm deletion

## Database Schema

### New Fields in `voucher_templates` table:
```
- primary_color (string, default: '#1e40af')
- secondary_color (string, default: '#3b82f6')
- font_size (string, default: '13px')
- page_margin (string, default: '5mm')
- logo_url (string, nullable)
- show_watermark (boolean, default: false)
- watermark_text (string, default: 'PAID')
```

## Routes

```php
Route::prefix('superadmin')->group(function () {
    Route::resource('voucher-templates', VoucherTemplateController::class);
});
```

### Available Routes:
- GET `/superadmin/voucher-templates` - List all templates
- GET `/superadmin/voucher-templates/create` - Create form
- POST `/superadmin/voucher-templates` - Store new template
- GET `/superadmin/voucher-templates/{id}/edit` - Edit form
- PUT `/superadmin/voucher-templates/{id}` - Update template
- DELETE `/superadmin/voucher-templates/{id}` - Delete template

## Design Presets

### Font Sizes
- **11px** - Very Small (compact printing)
- **12px** - Small (space-saving)
- **13px** - Default (balanced)
- **14px** - Medium (easy reading)
- **15px** - Large (high visibility)

### Page Margins
- **3mm** - Minimal (maximum content)
- **5mm** - Default (standard printing)
- **8mm** - Standard (comfortable spacing)
- **10mm** - Large (premium look)

### Color Defaults
- **Primary:** #1e40af (Blue 800)
- **Secondary:** #3b82f6 (Blue 500)

## Implementation

### Controller
`App\Http\Controllers\SuperAdmin\VoucherTemplateController`
- index() - List templates
- create() - Show creation form
- store() - Save new template
- edit() - Show edit form
- update() - Update template
- destroy() - Delete template

### Views
- `resources/views/superadmin/voucher-templates/index.blade.php`
- `resources/views/superadmin/voucher-templates/create.blade.php`
- `resources/views/superadmin/voucher-templates/edit.blade.php`

### Model
`App\Models\VoucherTemplate`
- Updated fillable fields with design options

## Next Steps (Future Enhancement)

### Phase 2 - Apply Custom Templates to Vouchers
1. Update voucher print views to use template settings
2. Dynamic color injection in CSS
3. Logo display support
4. Watermark implementation

### Phase 3 - Advanced Customization
1. Multiple template variants per owner
2. Template preview functionality
3. Font family selection
4. Border style options
5. Custom CSS injection

## Benefits

✅ **For Super Admin:**
- Complete control over voucher appearance
- Easy branding management
- Professional customization per business

✅ **For Owners:**
- Branded invoices matching their business
- Professional image
- No technical knowledge required

✅ **For System:**
- Centralized design management
- Easy maintenance
- Scalable customization

## Screenshots Location
Dashboard → Quick Actions → "Voucher Templates" button
