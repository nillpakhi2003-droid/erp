#!/bin/bash

# Load Testing Script using Apache Bench and K6
# This script helps you perform load testing on your ERP system

set -e

echo "🧪 ERP System Load Testing"
echo "=========================="
echo ""

# Configuration
BASE_URL=${BASE_URL:-"http://localhost:8888"}
CONCURRENT_USERS=${CONCURRENT_USERS:-50}
TOTAL_REQUESTS=${TOTAL_REQUESTS:-1000}

echo "📋 Test Configuration:"
echo "  Base URL: $BASE_URL"
echo "  Concurrent Users: $CONCURRENT_USERS"
echo "  Total Requests: $TOTAL_REQUESTS"
echo ""

# Check if ab (Apache Bench) is installed
if ! command -v ab &> /dev/null; then
    echo "⚠️  Apache Bench not found. Installing..."
    sudo apt-get update
    sudo apt-get install -y apache2-utils
fi

# Create results directory
RESULTS_DIR="load-test-results/$(date +%Y%m%d_%H%M%S)"
mkdir -p "$RESULTS_DIR"

echo "🔄 Running Apache Bench tests..."
echo ""

# Test 1: Homepage
echo "📊 Test 1: Homepage Load Test"
ab -n $TOTAL_REQUESTS -c $CONCURRENT_USERS -g "$RESULTS_DIR/homepage.tsv" "$BASE_URL/" > "$RESULTS_DIR/homepage.txt" 2>&1
echo "✅ Homepage test complete"
echo ""

# Test 2: Login Page
echo "📊 Test 2: Login Page Load Test"
ab -n $TOTAL_REQUESTS -c $CONCURRENT_USERS -g "$RESULTS_DIR/login.tsv" "$BASE_URL/login" > "$RESULTS_DIR/login.txt" 2>&1
echo "✅ Login page test complete"
echo ""

# Test 3: Static Assets
echo "📊 Test 3: Static Assets Load Test"
ab -n $TOTAL_REQUESTS -c $CONCURRENT_USERS "$BASE_URL/css/app.css" > "$RESULTS_DIR/static.txt" 2>&1 || echo "⚠️  Static asset test skipped (file may not exist)"
echo ""

# Check if k6 is installed for advanced testing
if command -v k6 &> /dev/null; then
    echo "🚀 Running K6 Advanced Load Tests..."
    k6 run --out json="$RESULTS_DIR/k6-results.json" load-test.js
    echo "✅ K6 tests complete"
else
    echo "ℹ️  K6 not installed. Skipping advanced tests."
    echo "   Install K6: sudo apt-key adv --keyserver hkp://keyserver.ubuntu.com:80 --recv-keys C5AD17C747E3415A3642D57D77C6C491D6AC1D69"
    echo "   echo \"deb https://dl.k6.io/deb stable main\" | sudo tee /etc/apt/sources.list.d/k6.list"
    echo "   sudo apt-get update && sudo apt-get install k6"
fi

echo ""
echo "📈 Analyzing Results..."
echo ""

# Extract key metrics from homepage test
if [ -f "$RESULTS_DIR/homepage.txt" ]; then
    echo "🏠 Homepage Performance:"
    echo "----------------------------------------"
    grep -E "(Requests per second|Time per request|Failed requests)" "$RESULTS_DIR/homepage.txt" || true
    echo ""
fi

# Extract key metrics from login page test
if [ -f "$RESULTS_DIR/login.txt" ]; then
    echo "🔐 Login Page Performance:"
    echo "----------------------------------------"
    grep -E "(Requests per second|Time per request|Failed requests)" "$RESULTS_DIR/login.txt" || true
    echo ""
fi

# Generate summary report
cat > "$RESULTS_DIR/SUMMARY.md" << EOF
# Load Test Summary Report

**Date:** $(date)
**Base URL:** $BASE_URL
**Concurrent Users:** $CONCURRENT_USERS
**Total Requests:** $TOTAL_REQUESTS

## Test Results

### Homepage Test
$(grep -A 20 "Concurrency Level" "$RESULTS_DIR/homepage.txt" 2>/dev/null || echo "No data available")

### Login Page Test
$(grep -A 20 "Concurrency Level" "$RESULTS_DIR/login.txt" 2>/dev/null || echo "No data available")

## Recommendations

- Review failed requests if any
- Check server resource usage during tests
- Optimize slow endpoints (>500ms)
- Consider caching for static assets
- Monitor database query performance

## Files Generated

- homepage.txt - Homepage test results
- login.txt - Login page test results
- homepage.tsv - Homepage timing data
- login.tsv - Login timing data
$([ -f "$RESULTS_DIR/k6-results.json" ] && echo "- k6-results.json - K6 advanced test results")

EOF

echo "✅ Load Testing Complete!"
echo ""
echo "📁 Results saved to: $RESULTS_DIR"
echo "📄 Summary report: $RESULTS_DIR/SUMMARY.md"
echo ""
echo "🔍 Next Steps:"
echo "  1. Review the summary report"
echo "  2. Check for failed requests"
echo "  3. Analyze response times"
echo "  4. Monitor server resources (CPU, Memory, Disk)"
echo "  5. Optimize bottlenecks"
echo ""
