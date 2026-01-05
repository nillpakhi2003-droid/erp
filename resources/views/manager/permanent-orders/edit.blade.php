@extends('layouts.app')

@section('title', 'স্থায়ী অর্ডার সম্পাদনা')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">স্থায়ী অর্ডার সম্পাদনা</h1>
    </div>

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow p-6">
        <form method="POST" action="{{ route('manager.permanent-orders.update', $permanentOrder) }}">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <label for="customer_name" class="block text-gray-700 text-sm font-bold mb-2">গ্রাহকের নাম *</label>
                    <input type="text" name="customer_name" id="customer_name" value="{{ old('customer_name', $permanentOrder->customer_name) }}" class="shadow border rounded w-full py-2 px-3 text-gray-700 @error('customer_name') border-red-500 @enderror" required>
                    @error('customer_name')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="customer_phone" class="block text-gray-700 text-sm font-bold mb-2">ফোন নম্বর *</label>
                    <input type="text" name="customer_phone" id="customer_phone" value="{{ old('customer_phone', $permanentOrder->customer_phone) }}" class="shadow border rounded w-full py-2 px-3 text-gray-700 @error('customer_phone') border-red-500 @enderror" required>
                    @error('customer_phone')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mb-4">
                <label for="customer_address" class="block text-gray-700 text-sm font-bold mb-2">ঠিকানা</label>
                <textarea name="customer_address" id="customer_address" rows="2" class="shadow border rounded w-full py-2 px-3 text-gray-700 @error('customer_address') border-red-500 @enderror">{{ old('customer_address', $permanentOrder->customer_address) }}</textarea>
                @error('customer_address')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="product_id" class="block text-gray-700 text-sm font-bold mb-2">পণ্য নির্বাচন *</label>
                    <select name="product_id" id="product_id" class="shadow border rounded w-full py-2 px-3 text-gray-700 @error('product_id') border-red-500 @enderror" required>
                        <option value="">পণ্য নির্বাচন করুন</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" data-price="{{ $product->sell_price }}" {{ old('product_id', $permanentOrder->product_id) == $product->id ? 'selected' : '' }}>
                                {{ $product->name }} - ৳{{ number_format($product->sell_price, 2) }}
                            </option>
                        @endforeach
                    </select>
                    @error('product_id')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="quantity" class="block text-gray-700 text-sm font-bold mb-2">পরিমাণ *</label>
                    <input type="number" step="0.01" name="quantity" id="quantity" value="{{ old('quantity', $permanentOrder->quantity) }}" class="shadow border rounded w-full py-2 px-3 text-gray-700 @error('quantity') border-red-500 @enderror" required>
                    @error('quantity')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="unit_price" class="block text-gray-700 text-sm font-bold mb-2">একক মূল্য (৳) *</label>
                    <input type="number" step="0.01" name="unit_price" id="unit_price" value="{{ old('unit_price', $permanentOrder->unit_price) }}" class="shadow border rounded w-full py-2 px-3 text-gray-700 @error('unit_price') border-red-500 @enderror" required>
                    @error('unit_price')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="total_amount" class="block text-gray-700 text-sm font-bold mb-2">মোট টাকা (৳)</label>
                    <input type="number" step="0.01" id="total_amount" class="shadow border rounded w-full py-2 px-3 text-gray-700 bg-gray-100" readonly>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="paid_amount" class="block text-gray-700 text-sm font-bold mb-2">পরিশোধিত টাকা (৳) *</label>
                    <input type="number" step="0.01" name="paid_amount" id="paid_amount" value="{{ old('paid_amount', $permanentOrder->paid_amount) }}" class="shadow border rounded w-full py-2 px-3 text-gray-700 @error('paid_amount') border-red-500 @enderror" required>
                    @error('paid_amount')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="due_amount" class="block text-gray-700 text-sm font-bold mb-2">বাকি টাকা (৳)</label>
                    <input type="number" step="0.01" id="due_amount" class="shadow border rounded w-full py-2 px-3 text-gray-700 bg-gray-100" readonly>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="delivery_date" class="block text-gray-700 text-sm font-bold mb-2">ডেলিভারি তারিখ</label>
                    <input type="date" name="delivery_date" id="delivery_date" value="{{ old('delivery_date', $permanentOrder->delivery_date?->format('Y-m-d')) }}" class="shadow border rounded w-full py-2 px-3 text-gray-700 @error('delivery_date') border-red-500 @enderror">
                    @error('delivery_date')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="status" class="block text-gray-700 text-sm font-bold mb-2">স্ট্যাটাস *</label>
                    <select name="status" id="status" class="shadow border rounded w-full py-2 px-3 text-gray-700 @error('status') border-red-500 @enderror" required>
                        <option value="active" {{ old('status', $permanentOrder->status) == 'active' ? 'selected' : '' }}>সক্রিয়</option>
                        <option value="partial" {{ old('status', $permanentOrder->status) == 'partial' ? 'selected' : '' }}>আংশিক পরিশোধ</option>
                        <option value="completed" {{ old('status', $permanentOrder->status) == 'completed' ? 'selected' : '' }}>সম্পন্ন</option>
                        <option value="cancelled" {{ old('status', $permanentOrder->status) == 'cancelled' ? 'selected' : '' }}>বাতিল</option>
                    </select>
                    @error('status')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mb-6">
                <label for="notes" class="block text-gray-700 text-sm font-bold mb-2">নোট</label>
                <textarea name="notes" id="notes" rows="3" class="shadow border rounded w-full py-2 px-3 text-gray-700 @error('notes') border-red-500 @enderror">{{ old('notes', $permanentOrder->notes) }}</textarea>
                @error('notes')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-between">
                <a href="{{ route('manager.permanent-orders.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    বাতিল
                </a>
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    আপডেট করুন
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Calculate total and due amounts
    function calculateTotal() {
        const quantity = parseFloat(document.getElementById('quantity').value) || 0;
        const unitPrice = parseFloat(document.getElementById('unit_price').value) || 0;
        const paidAmount = parseFloat(document.getElementById('paid_amount').value) || 0;
        
        const total = quantity * unitPrice;
        const due = total - paidAmount;
        
        document.getElementById('total_amount').value = total.toFixed(2);
        document.getElementById('due_amount').value = due.toFixed(2);
    }

    document.getElementById('quantity').addEventListener('input', calculateTotal);
    document.getElementById('unit_price').addEventListener('input', calculateTotal);
    document.getElementById('paid_amount').addEventListener('input', calculateTotal);
    
    // Initialize on load
    calculateTotal();
</script>
@endsection
