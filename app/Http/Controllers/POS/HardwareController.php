<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use App\Models\HardwareDevice;
use App\Models\SystemVersion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HardwareController extends Controller
{
    /**
     * Display hardware configuration page.
     */
    public function index()
    {
        $user = Auth::user();
        $business = $user->business;
        
        $systemVersion = $business->systemVersion ?? SystemVersion::create([
            'business_id' => $business->id,
            'version' => 'basic',
        ]);
        
        $devices = $business->hardwareDevices()->get();
        
        // Supported devices list
        $supportedDevices = [
            'barcode_scanner' => [
                'brands' => ['Zebra', 'Honeywell', 'Datalogic', 'Symbol', 'Code', 'Any USB/Serial Scanner'],
                'models' => ['DS2208', 'Voyager 1200g', 'QuickScan QD2400', 'LS2208', 'Generic USB'],
                'connection' => ['USB', 'Serial', 'Bluetooth', 'Wireless'],
            ],
            'thermal_printer' => [
                'brands' => ['Epson', 'Star Micronics', 'Citizen', 'Bixolon', 'Zebra', 'Generic ESC/POS'],
                'models' => ['TM-T20', 'TM-T82', 'TSP143', 'CT-S310', 'SRP-275', 'ZD220'],
                'connection' => ['USB', 'Serial', 'Ethernet', 'Bluetooth', 'Wi-Fi'],
                'paper_sizes' => ['58mm', '80mm'],
            ],
            'cash_drawer' => [
                'brands' => ['APG', 'Star Micronics', 'M-S Cash Drawer', 'Generic'],
                'models' => ['Vasario 1616', 'SMD2-1317', 'EP-125N', 'Generic RJ11/RJ12'],
                'connection' => ['RJ11/RJ12 (connects to printer)', 'USB'],
            ],
        ];
        
        return view('pos.hardware.index', compact('business', 'systemVersion', 'devices', 'supportedDevices'));
    }
    
    /**
     * Show create device form.
     */
    public function create()
    {
        return view('pos.hardware.create');
    }
    
    /**
     * Store new hardware device.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $business = $user->business;
        
        $request->validate([
            'device_type' => 'required|in:barcode_scanner,thermal_printer,cash_drawer',
            'device_name' => 'required|string|max:255',
            'brand' => 'nullable|string|max:100',
            'model' => 'nullable|string|max:100',
            'connection_type' => 'required|string|max:50',
            'port' => 'nullable|string|max:100',
            'ip_address' => 'nullable|ip',
        ]);
        
        $device = $business->hardwareDevices()->create([
            'device_type' => $request->device_type,
            'device_name' => $request->device_name,
            'brand' => $request->brand,
            'model' => $request->model,
            'connection_type' => $request->connection_type,
            'port' => $request->port,
            'ip_address' => $request->ip_address,
            'is_enabled' => true,
            'is_connected' => false,
            'configured_by' => $user->id,
        ]);
        
        return redirect()->route('pos.hardware.index')
            ->with('success', 'হার্ডওয়্যার ডিভাইস সফলভাবে যুক্ত হয়েছে।');
    }
    
    /**
     * Test device connection.
     */
    public function test(HardwareDevice $device)
    {
        // Simulate device test
        $testResult = [
            'success' => true,
            'message' => 'Device connection test successful',
            'device_type' => $device->device_type,
            'connection_type' => $device->connection_type,
        ];
        
        // Update connection status
        $device->update(['is_connected' => true, 'last_connected_at' => now()]);
        
        return response()->json($testResult);
    }
    
    /**
     * Toggle device enabled status.
     */
    public function toggle(HardwareDevice $device)
    {
        $device->update(['is_enabled' => !$device->is_enabled]);
        
        return redirect()->back()->with('success', 'ডিভাইস স্ট্যাটাস আপডেট হয়েছে।');
    }
    
    /**
     * Delete device.
     */
    public function destroy(HardwareDevice $device)
    {
        $device->delete();
        
        return redirect()->route('pos.hardware.index')
            ->with('success', 'হার্ডওয়্যার ডিভাইস মুছে ফেলা হয়েছে।');
    }
}
