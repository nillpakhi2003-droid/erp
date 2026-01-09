@extends('layouts.app')

@section('title', 'নতুন বিক্রয়')

@section('content')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<style>
    .select2-container--default .select2-selection--single {
        height: 42px !important;
        border: 1px solid #d1d5db !important;
        border-radius: 0.375rem !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 42px !important;
        padding-left: 12px !important;
        font-size: 14px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px !important;
        right: 8px !important;
    }
    .select2-container {
        width: 100% !important;
        font-size: 14px !important;
    }
    .select2-dropdown {
        border: 1px solid #d1d5db !important;
        border-radius: 0.375rem !important;
    }
    .select2-search--dropdown .select2-search__field {
        border: 1px solid #d1d5db !important;
        border-radius: 0.375rem !important;
        padding: 8px !important;
    }
    .select2-results__option {
        padding: 10px !important;
        font-size: 14px !important;
    }
    @media (max-width: 640px) {
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            font-size: 12px !important;
        }
        .select2-results__option {
            font-size: 12px !important;
        }
    }
    .cart-item {
        transition: all 0.3s ease;
    }
    .cart-item:hover {
        background-color: #f9fafb;
    }
</style>

<div class="min-h-screen w-full px-2 sm:px-4 lg:px-6">
    <div class="mb-4 sm:mb-6">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">নতুন বিক্রয় তৈরি করুন</h1>
    </div>

    <div class="bg-white rounded-lg shadow p-4 sm:p-6 lg:p-8">
        @php
            $routePrefix = $baseRoute ?? (auth()->user()?->isOwner() ? 'owner' : (auth()->user()?->isManager() ? 'manager' : 'salesman'));
        @endphp
        
        <!-- Product Selection Section -->
        <div class="mb-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
            <h3 class="text-lg font-bold text-gray-800 mb-4">পণ্য যোগ করুন</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                <div class="md:col-span-5">
                    <label class="block text-gray-700 text-xs sm:text-sm font-bold mb-2">পণ্য খুঁজুন *</label>
                    <select id="product_search" class="w-full">
                        <option value="">পণ্য খুঁজুন (বাংলা/English)...</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" 
                                    data-name="{{ $product->name }}"
                                    data-sku="{{ $product->sku }}"
                                    data-price="{{ $product->sell_price }}" 
                                    data-stock="{{ $product->current_stock }}">
                                {{ $product->name }} (কোড: {{ $product->sku }}) - স্টক: {{ $product->current_stock }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-gray-700 text-xs sm:text-sm font-bold mb-2">পরিমাণ *</label>
                    <input type="number" id="temp_quantity" value="1" min="1" 
                           class="shadow border rounded w-full py-2 px-3 text-sm sm:text-base text-gray-700">
                </div>
                
                <div class="md:col-span-3">
                    <label class="block text-gray-700 text-xs sm:text-sm font-bold mb-2">বিক্রয়মূল্য (৳) *</label>
                    <input type="number" id="temp_price" step="0.01" min="0" 
                           class="shadow border rounded w-full py-2 px-3 text-sm sm:text-base text-gray-700">
                </div>
                
                <div class="md:col-span-2 flex items-end">
                    <button type="button" onclick="addToCart()" 
                            class="w-full bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                        যোগ করুন
                    </button>
                </div>
            </div>
        </div>

        <!-- Cart Items Display -->
        <div id="cart_section" class="mb-6 hidden">
            <h3 class="text-lg font-bold text-gray-800 mb-3">নির্বাচিত পণ্য</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-200 rounded-lg">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-bold text-gray-700">পণ্য</th>
                            <th class="px-4 py-2 text-center text-xs font-bold text-gray-700">পরিমাণ</th>
                            <th class="px-4 py-2 text-right text-xs font-bold text-gray-700">মূল্য</th>
                            <th class="px-4 py-2 text-right text-xs font-bold text-gray-700">মোট</th>
                            <th class="px-4 py-2 text-center text-xs font-bold text-gray-700">মুছুন</th>
                        </tr>
                    </thead>
                    <tbody id="cart_items">
                    </tbody>
                    <tfoot class="bg-gray-50 font-bold">
                        <tr>
                            <td colspan="3" class="px-4 py-3 text-right text-sm">সর্বমোট টাকা:</td>
                            <td class="px-4 py-3 text-right text-lg text-blue-600" id="grand_total">৳০.০০</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Form Submission -->
        <form method="POST" action="{{ route($routePrefix . '.sales.store') }}" id="saleForm">
            @csrf
            <input type="hidden" name="cart_data" id="cart_data">
            
            <!-- Customer Fields -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6 mb-4 sm:mb-6">
                <div>
                    <label for="customer_name" class="block text-gray-700 text-xs sm:text-sm font-bold mb-2">
                        কাস্টমার নাম <span id="customer_name_required" class="text-red-500 hidden">*</span>
                    </label>
                    <input type="text" 
                           name="customer_name" 
                           id="customer_name" 
                           value="{{ old('customer_name') }}" 
                           class="shadow border rounded w-full py-2 px-3 text-sm sm:text-base text-gray-700 @error('customer_name') border-red-500 @enderror"
                           placeholder="কাস্টমারের নাম (ঐচ্ছিক)">
                    @error('customer_name')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="customer_phone" class="block text-gray-700 text-xs sm:text-sm font-bold mb-2">
                        কাস্টমার ফোন নম্বর <span id="customer_phone_required" class="text-red-500 hidden">*</span>
                    </label>
                    <input type="text" 
                           name="customer_phone" 
                           id="customer_phone" 
                           value="{{ old('customer_phone') }}" 
                           class="shadow border rounded w-full py-2 px-3 text-sm sm:text-base text-gray-700 @error('customer_phone') border-red-500 @enderror"
                           placeholder="০১৭XXXXXXXXX (ঐচ্ছিক)">
                    @error('customer_phone')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            @if(auth()->user()->isDueSystemEnabled())
            <!-- বাকিতে Toggle -->
            <div class="mb-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                <label class="flex items-center cursor-pointer">
                    <input type="checkbox" name="is_credit" id="is_credit" value="1" class="mr-3 w-5 h-5" onchange="toggleCreditFields()">
                    <span class="text-base sm:text-lg font-bold text-gray-800">বাকিতে বিক্রয়</span>
                </label>
            </div>
            @endif

            @if(auth()->user()->isDueSystemEnabled())
            <!-- Credit Payment Fields (Hidden by default) -->
            <div id="creditFields" class="hidden">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6 mb-4 sm:mb-6">
                    <div>
                        <label for="paid_amount" class="block text-gray-700 text-xs sm:text-sm font-bold mb-2">পরিশোধিত টাকা *</label>
                        <input type="number" 
                               name="paid_amount" 
                               id="paid_amount" 
                               value="{{ old('paid_amount', 0) }}" 
                               step="0.01"
                               min="0" 
                               class="shadow border rounded w-full py-2 px-3 text-sm sm:text-base text-gray-700 @error('paid_amount') border-red-500 @enderror" 
                               onchange="updateDue()">
                        @error('paid_amount')
                            <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="p-3 sm:p-4 bg-yellow-50 rounded border border-yellow-200 flex items-center">
                        <div class="text-base sm:text-lg font-bold text-red-600">বকেয়া: <span id="dueAmount">৳০.০০</span></div>
                    </div>
                </div>

                <div class="mb-6">
                    <label for="expected_clear_date" class="block text-gray-700 text-xs sm:text-sm font-bold mb-2">বকেয়া পরিশোধের তারিখ</label>
                    <input type="date" 
                           name="expected_clear_date" 
                           id="expected_clear_date" 
                           value="{{ old('expected_clear_date') }}" 
                           min="{{ date('Y-m-d') }}"
                           class="shadow border rounded w-full py-2 px-3 text-sm sm:text-base text-gray-700 @error('expected_clear_date') border-red-500 @enderror">
                    @error('expected_clear_date')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            @endif

            <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 items-center sm:justify-between">
                <a href="{{ route($routePrefix . '.sales.index') }}" class="w-full sm:w-auto bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 sm:px-6 rounded text-sm sm:text-base text-center">
                    বাতিল
                </a>
                <button type="submit" class="w-full sm:w-auto bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 sm:px-6 rounded text-sm sm:text-base">
                    বিক্রয় তৈরি করুন
                </button>
            </div>
        </form>
    </div>

    @if($products->isEmpty())
        <div class="mt-6 bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded">
            <p>স্টকে কোন পণ্য নেই। অনুগ্রহ করে আপনার ম্যানেজারের সাথে যোগাযোগ করুন।</p>
        </div>
    @endif
</div>

<script>
const isDueSystemEnabled = {{ auth()->user()->isDueSystemEnabled() ? 'true' : 'false' }};
let cart = [];

function addToCart() {
    const productSelect = $('#product_search');
    const selectedOption = productSelect.find(':selected');
    const productId = selectedOption.val();
    
    if (!productId) {
        alert('অনুগ্রহ করে একটি পণ্য নির্বাচন করুন');
        return;
    }
    
    const quantity = parseInt($('#temp_quantity').val());
    const price = parseFloat($('#temp_price').val());
    const stock = parseInt(selectedOption.data('stock'));
    
    if (!price || price <= 0) {
        alert('সঠিক মূল্য দিন');
        return;
    }
    
    if (quantity > stock) {
        alert('স্টকে পর্যাপ্ত পণ্য নেই! বর্তমান স্টক: ' + stock);
        return;
    }
    
    // Check if product already in cart
    const existingIndex = cart.findIndex(item => item.product_id === productId);
    if (existingIndex > -1) {
        cart[existingIndex].quantity += quantity;
        cart[existingIndex].total = cart[existingIndex].quantity * cart[existingIndex].price;
    } else {
        cart.push({
            product_id: productId,
            name: selectedOption.data('name'),
            sku: selectedOption.data('sku'),
            quantity: quantity,
            price: price,
            total: quantity * price
        });
    }
    
    renderCart();
    
    // Reset inputs
    productSelect.val('').trigger('change');
    $('#temp_quantity').val(1);
    $('#temp_price').val('');
}

function removeFromCart(index) {
    cart.splice(index, 1);
    renderCart();
}

function renderCart() {
    const cartItems = $('#cart_items');
    const cartSection = $('#cart_section');
    const grandTotalEl = $('#grand_total');
    
    if (cart.length === 0) {
        cartSection.addClass('hidden');
        return;
    }
    
    cartSection.removeClass('hidden');
    cartItems.empty();
    
    let grandTotal = 0;
    cart.forEach((item, index) => {
        grandTotal += item.total;
        cartItems.append(`
            <tr class="cart-item border-b">
                <td class="px-4 py-3 text-sm">
                    <div class="font-semibold">${item.name}</div>
                    <div class="text-xs text-gray-500">কোড: ${item.sku}</div>
                </td>
                <td class="px-4 py-3 text-center text-sm">${item.quantity}</td>
                <td class="px-4 py-3 text-right text-sm">৳${item.price.toFixed(2)}</td>
                <td class="px-4 py-3 text-right text-sm font-semibold">৳${item.total.toFixed(2)}</td>
                <td class="px-4 py-3 text-center">
                    <button type="button" onclick="removeFromCart(${index})" 
                            class="text-red-600 hover:text-red-800 font-bold">✕</button>
                </td>
            </tr>
        `);
    });
    
    grandTotalEl.text('৳' + grandTotal.toFixed(2));
    $('#cart_data').val(JSON.stringify(cart));
    
    // Update due calculation if applicable
    updateDueFromCart(grandTotal);
}

function updateDueFromCart(grandTotal) {
    if (!isDueSystemEnabled) return;
    
    const paidField = document.getElementById('paid_amount');
    if (!paidField) return;
    
    const isCreditChecked = document.getElementById('is_credit') ? document.getElementById('is_credit').checked : false;
    if (!isCreditChecked) {
        paidField.value = grandTotal.toFixed(2);
    }
    
    const paid = parseFloat(paidField.value) || 0;
    const due = grandTotal - paid;
    
    const dueElement = document.getElementById('dueAmount');
    if (dueElement) {
        dueElement.textContent = '৳' + due.toFixed(2);
    }
}

function toggleCreditFields() {
    const isCreditChecked = document.getElementById('is_credit').checked;
    const creditFields = document.getElementById('creditFields');
    const paidAmount = document.getElementById('paid_amount');
    const customerNameRequired = document.getElementById('customer_name_required');
    const customerPhoneRequired = document.getElementById('customer_phone_required');
    const customerNameInput = document.getElementById('customer_name');
    const customerPhoneInput = document.getElementById('customer_phone');
    
    if (isCreditChecked) {
        creditFields.classList.remove('hidden');
        paidAmount.value = '0';
        
        // Make customer fields required for credit sales
        customerNameRequired.classList.remove('hidden');
        customerPhoneRequired.classList.remove('hidden');
        customerNameInput.setAttribute('required', 'required');
        customerPhoneInput.setAttribute('required', 'required');
        customerNameInput.placeholder = 'কাস্টমারের নাম (আবশ্যক)';
        customerPhoneInput.placeholder = '০১৭XXXXXXXXX (আবশ্যক)';
    } else {
        creditFields.classList.add('hidden');
        const grandTotalText = document.getElementById('grand_total').textContent;
        const grandTotal = parseFloat(grandTotalText.replace('৳', '')) || 0;
        paidAmount.value = grandTotal.toFixed(2);
        
        // Make customer fields optional for cash sales
        customerNameRequired.classList.add('hidden');
        customerPhoneRequired.classList.add('hidden');
        customerNameInput.removeAttribute('required');
        customerPhoneInput.removeAttribute('required');
        customerNameInput.placeholder = 'কাস্টমারের নাম (ঐচ্ছিক)';
        customerPhoneInput.placeholder = '০১৭XXXXXXXXX (ঐচ্ছিক)';
    }
    
    const grandTotalText = document.getElementById('grand_total').textContent;
    const grandTotal = parseFloat(grandTotalText.replace('৳', '')) || 0;
    updateDueFromCart(grandTotal);
}

function updateDue() {
    const paidField = document.getElementById('paid_amount');
    if (!paidField) return;
    
    const grandTotalText = document.getElementById('grand_total').textContent;
    const grandTotal = parseFloat(grandTotalText.replace('৳', '')) || 0;
    updateDueFromCart(grandTotal);
}

// Initialize on page load
$(document).ready(function() {
    // Initialize Select2 for product search dropdown
    $('#product_search').select2({
        placeholder: 'পণ্য খুঁজুন (বাংলা/English)',
        allowClear: true,
        width: '100%',
        language: {
            noResults: function() {
                return 'কোন পণ্য পাওয়া যায়নি';
            },
            searching: function() {
                return 'খুঁজছি...';
            }
        },
        matcher: function(params, data) {
            if ($.trim(params.term) === '') {
                return data;
            }
            var term = params.term.toLowerCase();
            var text = data.text.toLowerCase();
            if (text.indexOf(term) > -1) {
                return data;
            }
            return null;
        }
    }).on('change', function() {
        const selected = $(this).find(':selected');
        if (selected.val()) {
            $('#temp_price').val(selected.data('price'));
        }
    });
    
    // Form validation before submit
    $('#saleForm').on('submit', function(e) {
        if (cart.length === 0) {
            e.preventDefault();
            alert('অনুগ্রহ করে অন্তত একটি পণ্য যোগ করুন');
            return false;
        }
    });
});
</script>
@endsection
