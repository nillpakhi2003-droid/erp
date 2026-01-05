<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Product;
use App\Models\Sale;
use App\Models\StockEntry;
use App\Models\VoucherTemplate;
use App\Models\ProfitRealization;
use App\Models\Business;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Seed roles and permissions first
        $this->call(RolePermissionSeeder::class);

        // Create Super Admin
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'phone' => '1234567890',
            'password' => Hash::make('password'),
            'created_by' => null,
            'business_id' => null,
        ]);
        $superAdmin->assignRole('superadmin');

        // Create Business
        $business = Business::create([
            'name' => 'ABC ট্রেডিং কোম্পানি',
            'email' => 'abc@trading.com',
            'phone' => '01711111111',
            'address' => 'মতিঝিল, ঢাকা-১০০০',
            'is_active' => true,
        ]);

        // Create Owner 1 for the business
        $owner1 = User::create([
            'name' => 'রহিম সাহেব',
            'phone' => '01711111111',
            'password' => Hash::make('password'),
            'created_by' => null,
            'business_id' => $business->id,
            'due_system_enabled' => true,
        ]);
        $owner1->assignRole('owner');

        // Create Owner 2 for the same business
        $owner2 = User::create([
            'name' => 'করিম সাহেব',
            'phone' => '01722222222',
            'password' => Hash::make('password'),
            'created_by' => null,
            'business_id' => $business->id,
            'due_system_enabled' => true,
        ]);
        $owner2->assignRole('owner');

        // Create Voucher Template for the Business
        VoucherTemplate::create([
            'business_id' => $business->id,
            'company_name' => 'ABC ট্রেডিং কোম্পানি',
            'company_address' => 'মতিঝিল, ঢাকা-১০০০',
            'company_phone' => '০১৭১১-১১১১১১',
            'header_text' => 'আপনার বিশ্বাসের ঠিকানা',
            'footer_text' => 'ধন্যবাদ। আবার আসবেন।',
        ]);

        // Create sample Manager for Owner 1
        $manager = User::create([
            'name' => 'মেহেদী হাসান',
            'phone' => '01733333333',
            'password' => Hash::make('password'),
            'created_by' => $owner1->id,
            'business_id' => $business->id,
        ]);
        $manager->assignRole('manager');

        // Create sample Salesman
        $salesman = User::create([
            'name' => 'রফিক সেলসম্যান',
            'phone' => '01755555555',
            'password' => Hash::make('password'),
            'created_by' => $manager->id,
            'business_id' => $business->id,
        ]);
        $salesman->assignRole('salesman');

        // Create another Salesman
        $salesman2 = User::create([
            'name' => 'হাসান সেলসম্যান',
            'phone' => '01744444444',
            'password' => Hash::make('password'),
            'created_by' => $manager->id,
            'business_id' => $business->id,
        ]);
        $salesman2->assignRole('salesman');

        // Create Demo Products
        $products = [
            [
                'name' => 'স্যামসাং গ্যালাক্সি A54',
                'sku' => 'SAM-A54-001',
                'purchase_price' => 35000,
                'sell_price' => 42000,
                'current_stock' => 15,
            ],
            [
                'name' => 'আইফোন ১৪ প্রো',
                'sku' => 'IPH-14P-001',
                'purchase_price' => 125000,
                'sell_price' => 145000,
                'current_stock' => 8,
            ],
            [
                'name' => 'শাওমি রেডমি নোট ১২',
                'sku' => 'XIA-RN12-001',
                'purchase_price' => 18000,
                'sell_price' => 22000,
                'current_stock' => 25,
            ],
            [
                'name' => 'এয়ারপডস প্রো',
                'sku' => 'APP-APR-001',
                'purchase_price' => 18000,
                'sell_price' => 22500,
                'current_stock' => 12,
            ],
            [
                'name' => 'জেবিএল স্পিকার',
                'sku' => 'JBL-SPK-001',
                'purchase_price' => 3500,
                'sell_price' => 4500,
                'current_stock' => 20,
            ],
        ];

        foreach ($products as $productData) {
            $product = Product::create([
                'business_id' => $business->id,
                'name' => $productData['name'],
                'sku' => $productData['sku'],
                'purchase_price' => $productData['purchase_price'],
                'sell_price' => $productData['sell_price'],
                'current_stock' => 0,
            ]);

            // Add stock entry
            StockEntry::create([
                'product_id' => $product->id,
                'quantity' => $productData['current_stock'],
                'purchase_price' => $productData['purchase_price'],
                'added_by' => $manager->id,
            ]);

            $product->update(['current_stock' => $productData['current_stock']]);
        }

        // Create Demo Sales with Vouchers
        $productsList = Product::all();
        
        // Sale 1 - Cash sale (Today)
        $sale1 = Sale::create([
            'product_id' => $productsList[0]->id,
            'user_id' => $salesman->id,
            'quantity' => 2,
            'sell_price' => 42000,
            'customer_name' => 'মি. আহমেদ',
            'customer_phone' => '01811111111',
            'paid_amount' => 84000,
            'voucher_number' => 'V-20251122-0001',
            'created_at' => now()->setTime(10, 14, 0),
            'updated_at' => now()->setTime(10, 14, 0),
        ]);
        $productsList[0]->reduceStock(2);
        
        // Create profit realization for sale 1
        $profitRatio1 = $sale1->profit / $sale1->total_amount;
        ProfitRealization::create([
            'sale_id' => $sale1->id,
            'payment_date' => now()->setTime(10, 14, 0),
            'payment_amount' => 84000,
            'payment_voucher_number' => $sale1->voucher_number,
            'profit_amount' => 84000 * $profitRatio1,
            'recorded_by' => $salesman->id,
            'notes' => 'Initial sale payment',
            'created_at' => now()->setTime(10, 14, 0),
            'updated_at' => now()->setTime(10, 14, 0),
        ]);

        // Sale 2 - Credit sale (Today, 2 hours ago)
        $sale2 = Sale::create([
            'product_id' => $productsList[2]->id,
            'user_id' => $salesman2->id,
            'quantity' => 3,
            'sell_price' => 22000,
            'customer_name' => 'মিসেস খানম',
            'customer_phone' => '01822222222',
            'paid_amount' => 40000,
            'voucher_number' => 'V-20251122-0002',
            'expected_clear_date' => now()->addDays(7),
            'created_at' => now()->setTime(8, 14, 0),
            'updated_at' => now()->setTime(8, 14, 0),
        ]);
        $productsList[2]->reduceStock(3);
        
        // Create profit realization for sale 2
        $profitRatio2 = $sale2->profit / $sale2->total_amount;
        ProfitRealization::create([
            'sale_id' => $sale2->id,
            'payment_date' => now()->setTime(8, 14, 0),
            'payment_amount' => 40000,
            'payment_voucher_number' => $sale2->voucher_number,
            'profit_amount' => 40000 * $profitRatio2,
            'recorded_by' => $salesman2->id,
            'notes' => 'Initial sale payment',
            'created_at' => now()->setTime(8, 14, 0),
            'updated_at' => now()->setTime(8, 14, 0),
        ]);

        // Sale 3 - Cash sale (Today, 5 hours ago)
        $sale3 = Sale::create([
            'product_id' => $productsList[4]->id,
            'user_id' => $salesman->id,
            'quantity' => 5,
            'sell_price' => 4500,
            'customer_name' => 'মি. রফিক',
            'customer_phone' => '01833333333',
            'paid_amount' => 22500,
            'voucher_number' => 'V-20251122-0003',
            'created_at' => now()->setTime(5, 14, 0),
            'updated_at' => now()->setTime(5, 14, 0),
        ]);
        $productsList[4]->reduceStock(5);
        
        // Create profit realization for sale 3
        $profitRatio3 = $sale3->profit / $sale3->total_amount;
        ProfitRealization::create([
            'sale_id' => $sale3->id,
            'payment_date' => now()->setTime(5, 14, 0),
            'payment_amount' => 22500,
            'payment_voucher_number' => $sale3->voucher_number,
            'profit_amount' => 22500 * $profitRatio3,
            'recorded_by' => $salesman->id,
            'notes' => 'Initial sale payment',
            'created_at' => now()->setTime(5, 14, 0),
            'updated_at' => now()->setTime(5, 14, 0),
        ]);

        // Sale 4 - Credit sale with partial payment (Yesterday)
        $sale4 = Sale::create([
            'product_id' => $productsList[1]->id,
            'user_id' => $salesman2->id,
            'quantity' => 1,
            'sell_price' => 145000,
            'customer_name' => 'মি. করিম',
            'customer_phone' => '01844444444',
            'paid_amount' => 100000,
            'voucher_number' => 'V-20251121-0002',
            'expected_clear_date' => now()->addDays(15),
            'created_at' => now()->subDay()->setTime(14, 30, 0),
            'updated_at' => now()->subDay()->setTime(14, 30, 0),
        ]);
        $productsList[1]->reduceStock(1);
        
        // Create profit realization for sale 4
        $profitRatio4 = $sale4->profit / $sale4->total_amount;
        ProfitRealization::create([
            'sale_id' => $sale4->id,
            'payment_date' => now()->subDay()->setTime(14, 30, 0),
            'payment_amount' => 100000,
            'payment_voucher_number' => $sale4->voucher_number,
            'profit_amount' => 100000 * $profitRatio4,
            'recorded_by' => $salesman2->id,
            'notes' => 'Initial sale payment',
            'created_at' => now()->subDay()->setTime(14, 30, 0),
            'updated_at' => now()->subDay()->setTime(14, 30, 0),
        ]);

        // Sale 5 - Cash sale (Yesterday)
        $sale5 = Sale::create([
            'product_id' => $productsList[3]->id,
            'user_id' => $salesman->id,
            'quantity' => 3,
            'sell_price' => 22500,
            'customer_name' => 'মিসেস আক্তার',
            'customer_phone' => '01855555555',
            'paid_amount' => 67500,
            'voucher_number' => 'V-20251121-0001',
            'created_at' => now()->subDay()->setTime(11, 20, 0),
            'updated_at' => now()->subDay()->setTime(11, 20, 0),
        ]);
        $productsList[3]->reduceStock(3);
        
        // Create profit realization for sale 5
        $profitRatio5 = $sale5->profit / $sale5->total_amount;
        ProfitRealization::create([
            'sale_id' => $sale5->id,
            'payment_date' => now()->subDay()->setTime(11, 20, 0),
            'payment_amount' => 67500,
            'payment_voucher_number' => $sale5->voucher_number,
            'profit_amount' => 67500 * $profitRatio5,
            'recorded_by' => $salesman->id,
            'notes' => 'Initial sale payment',
            'created_at' => now()->subDay()->setTime(11, 20, 0),
            'updated_at' => now()->subDay()->setTime(11, 20, 0),
        ]);

        // Sale 6 - Cash sale (20th Nov)
        $sale6 = Sale::create([
            'product_id' => $productsList[2]->id,
            'user_id' => $salesman->id,
            'quantity' => 2,
            'sell_price' => 22000,
            'customer_name' => 'মি. জামাল',
            'customer_phone' => '01866666666',
            'paid_amount' => 44000,
            'voucher_number' => 'V-20251120-0001',
            'created_at' => now()->subDays(2)->setTime(15, 45, 0),
            'updated_at' => now()->subDays(2)->setTime(15, 45, 0),
        ]);
        $productsList[2]->reduceStock(2);
        
        // Create profit realization for sale 6
        $profitRatio6 = $sale6->profit / $sale6->total_amount;
        ProfitRealization::create([
            'sale_id' => $sale6->id,
            'payment_date' => now()->subDays(2)->setTime(15, 45, 0),
            'payment_amount' => 44000,
            'payment_voucher_number' => $sale6->voucher_number,
            'profit_amount' => 44000 * $profitRatio6,
            'recorded_by' => $salesman->id,
            'notes' => 'Initial sale payment',
            'created_at' => now()->subDays(2)->setTime(15, 45, 0),
            'updated_at' => now()->subDays(2)->setTime(15, 45, 0),
        ]);

        // Sale 7 - Credit sale (20th Nov)
        $sale7 = Sale::create([
            'product_id' => $productsList[0]->id,
            'user_id' => $salesman2->id,
            'quantity' => 1,
            'sell_price' => 42000,
            'customer_name' => 'মিসেস বেগম',
            'customer_phone' => '01877777777',
            'paid_amount' => 30000,
            'voucher_number' => 'V-20251120-0002',
            'expected_clear_date' => now()->addDays(10),
            'created_at' => now()->subDays(2)->setTime(9, 30, 0),
            'updated_at' => now()->subDays(2)->setTime(9, 30, 0),
        ]);
        $productsList[0]->reduceStock(1);
        
        // Create profit realization for sale 7
        $profitRatio7 = $sale7->profit / $sale7->total_amount;
        ProfitRealization::create([
            'sale_id' => $sale7->id,
            'payment_date' => now()->subDays(2)->setTime(9, 30, 0),
            'payment_amount' => 30000,
            'payment_voucher_number' => $sale7->voucher_number,
            'profit_amount' => 30000 * $profitRatio7,
            'recorded_by' => $salesman2->id,
            'notes' => 'Initial sale payment',
            'created_at' => now()->subDays(2)->setTime(9, 30, 0),
            'updated_at' => now()->subDays(2)->setTime(9, 30, 0),
        ]);
        
        // ==============================================
        // Create Second Business for Testing Isolation
        // ==============================================
        
        $business2 = Business::create([
            'name' => 'XYZ ইলেকট্রনিক্স',
            'email' => 'xyz@electronics.com',
            'phone' => '01811111111',
            'address' => 'গুলশান, ঢাকা-১২১২',
            'is_active' => true,
        ]);

        // Create Owner for second business
        $owner3 = User::create([
            'name' => 'জামাল সাহেব',
            'phone' => '01811111111',
            'password' => Hash::make('password'),
            'created_by' => null,
            'business_id' => $business2->id,
            'due_system_enabled' => true,
        ]);
        $owner3->assignRole('owner');
        
        VoucherTemplate::create([
            'business_id' => $business2->id,
            'company_name' => 'XYZ ইলেকট্রনিক্স',
            'company_address' => 'গুলশান, ঢাকা-১২১২',
            'company_phone' => '০১৮১১-১১১১১১',
            'header_text' => 'প্রযুক্তির সেরা সমাধান',
            'footer_text' => 'আপনার সেবায় আমরা।',
        ]);
        
        // Create Manager for second business
        $manager2 = User::create([
            'name' => 'সাকিব ম্যানেজার',
            'phone' => '01822222222',
            'password' => Hash::make('password'),
            'created_by' => $owner3->id,
            'business_id' => $business2->id,
        ]);
        $manager2->assignRole('manager');
        
        // Create Products for second business
        $product2_1 = Product::create([
            'business_id' => $business2->id,
            'name' => 'ডেল ল্যাপটপ',
            'sku' => 'DELL-L15-001',
            'purchase_price' => 45000,
            'sell_price' => 55000,
            'current_stock' => 0,
        ]);
        
        StockEntry::create([
            'product_id' => $product2_1->id,
            'quantity' => 10,
            'purchase_price' => 45000,
            'added_by' => $manager2->id,
        ]);
        
        $product2_1->update(['current_stock' => 10]);
        
        $product2_2 = Product::create([
            'business_id' => $business2->id,
            'name' => 'এইচপি প্রিন্টার',
            'sku' => 'HP-PRN-001',
            'purchase_price' => 12000,
            'sell_price' => 15000,
            'current_stock' => 0,
        ]);
        
        StockEntry::create([
            'product_id' => $product2_2->id,
            'quantity' => 15,
            'purchase_price' => 12000,
            'added_by' => $manager2->id,
        ]);
        
        $product2_2->update(['current_stock' => 15]);
    }
}
