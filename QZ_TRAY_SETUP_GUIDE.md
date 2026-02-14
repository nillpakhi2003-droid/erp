# QZ Tray Setup Guide for Rongta RP400H Barcode Printer

## Overview
This guide helps you set up QZ Tray for direct thermal printing to your Rongta RP400H barcode/sticker printer from the web browser.

## What is QZ Tray?
QZ Tray is a free software that allows web applications to communicate directly with thermal printers, bypassing the browser's print dialog. This gives you:
- **Better control** over label positioning
- **Faster printing** without dialog boxes
- **Professional results** optimized for thermal printers

## Hardware Requirements
✅ **Rongta RP400H Specifications** (as shown on your printer):
- Model: RP400H
- Paper Width: 115mm
- Interface: USB
- Power: 24V DC, 2.5A
- Printing Speed: 120mm/sec

## Setup Steps

### Step 1: Install QZ Tray Application

1. **Download QZ Tray**
   - Visit: https://qz.io/download/
   - Download the installer for your operating system:
     - Windows: `qz-tray-X.X.X.exe`
     - macOS: `qz-tray-X.X.X.pkg`
     - Linux: `qz-tray-X.X.X.run`

2. **Install QZ Tray**
   - Run the downloaded installer
   - Follow the installation wizard
   - Accept the default installation location
   - Complete the installation

3. **Start QZ Tray**
   - Windows: Look for "QZ Tray" in Start Menu → Startup folder
   - Or manually run from: `C:\Program Files\QZ Tray\qz-tray.exe`
   - You should see a **QZ icon** in your system tray (bottom-right corner)

4. **Verify QZ Tray is Running**
   - Check system tray for QZ icon
   - Right-click icon → About to see version info
   - QZ Tray runs on port 8181 by default

### Step 2: Install Rongta RP400H Printer Driver

1. **Download Driver**
   - Visit Rongta's official website: https://www.rongtatech.com
   - Navigate to: Support → Downloads → RP400 Series
   - Download the USB driver for your OS

2. **Install Driver**
   - Extract the downloaded ZIP file
   - Run the driver installer (usually `Setup.exe` on Windows)
   - Follow installation instructions
   - Restart computer if prompted

3. **Connect Printer**
   - Plug in the **24V power adapter** (important!)
   - Connect USB cable to computer
   - Turn printer ON
   - Windows should detect "Rongta RP400H" or "Rongta Printer"

4. **Verify Printer**
   - Open Windows Settings → Devices → Printers & scanners
   - You should see "Rongta RP400H" or similar name
   - Set as default printer (optional but recommended)
   - Print test page to confirm working

### Step 3: Configure Browser Permissions

1. **Allow Certificate (First Time Only)**
   - When you first click "Print to Rongta RP400H", you'll see a certificate warning
   - This is normal - QZ Tray uses a self-signed certificate
   - Click "Advanced" → "Proceed to localhost"
   - Or permanently trust the QZ certificate

2. **Allow Pop-ups**
   - Some browsers block QZ dialogs
   - Allow pop-ups for your ERP domain
   - Chrome: Click lock icon → Site settings → Pop-ups and redirects → Allow

## Using the Barcode Printer

### Print Workflow

1. **Select Products**
   - Go to: Owner Dashboard → Barcode → Print Labels
   - Select products to print
   - Choose quantity for each

2. **Configure Size**
   - Default: 45mm × 35mm (matches your stickers)
   - Other sizes available in dropdown

3. **Adjust Position** (if needed)
   - Use Left/Right/Up/Down buttons for fine-tuning
   - Moves label in 2mm increments

4. **Print Methods**
   - **🖨️ Print to Rongta RP400H** (Primary)
     - Uses QZ Tray for direct printing
     - Best quality and speed
   
   - **🔍 Check QZ Status** (Diagnostic)
     - Verifies QZ Tray is running
     - Shows detected printers
     - Helps troubleshoot issues
   
   - **🖨️ Browser Print (Fallback)**
     - Opens browser print dialog
     - Works without QZ Tray
     - Manual paper size selection needed

## Troubleshooting

### QZ Tray Not Detected

**Symptoms:**
- "QZ Tray Library: NOT LOADED" error
- "QZ Tray App: NOT RUNNING" message

**Solutions:**
1. Check internet connection (QZ scripts load from CDN)
2. Refresh browser page (Ctrl+F5)
3. Start QZ Tray application
4. Check system tray for QZ icon
5. Restart QZ Tray: Right-click icon → Exit → Restart

### Printer Not Found

**Symptoms:**
- "No printers found" message
- Rongta not in printer list

**Solutions:**
1. Verify printer is ON (24V power connected)
2. Check USB cable connection
3. Install/reinstall Rongta driver
4. Check Windows "Devices and Printers"
5. Restart QZ Tray after installing driver
6. Click "Check QZ Status" to see detected printers

### Certificate Errors

**Symptoms:**
- "Certificate not trusted" warning
- "Secure connection failed" error

**Solutions:**
1. Click "Advanced" → "Proceed to localhost"
2. Or download QZ Tray's certificate:
   - Visit: https://qz.io/wiki/using-trusted-certificate
   - Install certificate to "Trusted Root Certification Authorities"
3. Restart browser after installing certificate

### Poor Print Quality

**Symptoms:**
- Barcode not scanning
- Text cut off
- Misaligned labels

**Solutions:**
1. Adjust position using Up/Down/Left/Right buttons
2. Check printer darkness settings
3. Use correct sticker size (45mm × 35mm)
4. Clean printer head (consult Rongta manual)
5. Ensure stickers are loaded straight
6. Check that Netum scanner has enough light

## Technical Details

### Default Print Configuration
```javascript
{
    printer: "Rongta RP400H",
    size: {
        width: 45,    // mm
        height: 35    // mm
    },
    units: "mm",
    margins: {
        top: 0,
        right: 0,
        bottom: 0,
        left: 0
    }
}
```

### Barcode Settings
- **Format:** CODE128
- **Width:** 2-4.5 (auto-scaled by label size)
- **Height:** Proportional to label
- **Quiet Zones:** 0.5-2mm (for Netum scanner compatibility)

### Sticker Sizes Supported
- 20mm × 10mm (tiny)
- 30mm × 20mm (small)
- 40mm × 30mm (medium)
- **45mm × 35mm** (default)
- 50mm × 30mm (wide)
- 60mm × 40mm (large)
- 70mm × 50mm (extra large)
- 100mm × 50mm (maximum)

## Support Resources

### Official Sites
- **QZ Tray:** https://qz.io
- **QZ Documentation:** https://qz.io/wiki
- **Rongta Tech:** https://www.rongtatech.com
- **Rongta Support:** Contact local distributor

### System Requirements
- **OS:** Windows 7+, macOS 10.10+, Linux (Ubuntu/Debian)
- **Browser:** Chrome 60+, Edge 79+, Firefox 55+, Safari 11+
- **Java:** Not required (QZ Tray 2.x uses native platform)
- **RAM:** 50MB minimum
- **Disk:** 100MB installation space

### Quick Reference

**Check QZ Version:**
- Open browser console (F12)
- Type: `qz.version`
- Should show: "2.2.x" or higher

**Manual Print Test:**
```javascript
// In browser console
qz.websocket.connect().then(function() {
    return qz.printers.find();
}).then(function(printers) {
    console.log(printers);
});
```

**QZ Tray Logs:**
- Windows: `%APPDATA%\QZ Industries\QZ Tray\qz-tray.log`
- macOS: `~/Library/Application Support/QZ Tray/qz-tray.log`
- Linux: `~/.qz-tray/qz-tray.log`

## FAQ

**Q: Do I need to install QZ Tray on every computer?**
A: Yes, each computer that prints needs QZ Tray installed and running.

**Q: Is QZ Tray free?**
A: Yes, QZ Tray is free and open source software.

**Q: Can I use multiple printers?**
A: Yes, QZ Tray detects all installed printers. The system prioritizes Rongta RP400H.

**Q: What if QZ Tray doesn't work?**
A: Use the "Browser Print (Fallback)" button - it works without QZ Tray but requires manual paper size selection.

**Q: How do I update QZ Tray?**
A: Download latest version from https://qz.io/download/ and reinstall.

**Q: Does this work on mobile devices?**
A: No, QZ Tray requires desktop OS (Windows/macOS/Linux).

**Q: Can I print remotely?**
A: Yes, but QZ Tray must be running on the computer connected to the printer.

---

**Last Updated:** 2025-01-XX
**Document Version:** 1.0
**Application:** ERP Barcode Management System
