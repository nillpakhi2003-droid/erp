// Load Testing Configuration for K6

import http from 'k6/http';
import { check, sleep } from 'k6';
import { Rate } from 'k6/metrics';

const errorRate = new Rate('errors');

export const options = {
    stages: [
        { duration: '2m', target: 10 },   // Ramp up to 10 users
        { duration: '5m', target: 10 },   // Stay at 10 users
        { duration: '2m', target: 50 },   // Ramp up to 50 users
        { duration: '5m', target: 50 },   // Stay at 50 users
        { duration: '2m', target: 100 },  // Ramp up to 100 users
        { duration: '5m', target: 100 },  // Stay at 100 users
        { duration: '5m', target: 0 },    // Ramp down to 0 users
    ],
    thresholds: {
        http_req_duration: ['p(95)<500'], // 95% of requests should be below 500ms
        http_req_failed: ['rate<0.05'],   // Error rate should be less than 5%
        errors: ['rate<0.1'],             // Custom error rate
    },
};

const BASE_URL = __ENV.BASE_URL || 'http://localhost:8888';

export default function () {
    // Test homepage
    let response = http.get(`${BASE_URL}/`);
    check(response, {
        'homepage status is 200': (r) => r.status === 200,
        'homepage response time < 500ms': (r) => r.timings.duration < 500,
    }) || errorRate.add(1);

    sleep(1);

    // Test login page
    response = http.get(`${BASE_URL}/login`);
    check(response, {
        'login page status is 200': (r) => r.status === 200,
    }) || errorRate.add(1);

    sleep(1);

    // Test products page (if public)
    response = http.get(`${BASE_URL}/products`);
    check(response, {
        'products page loaded': (r) => r.status === 200 || r.status === 302,
    }) || errorRate.add(1);

    sleep(2);
}

// Spike test scenario
export function spikeTest() {
    const response = http.get(`${BASE_URL}/`);
    check(response, {
        'status is 200': (r) => r.status === 200,
    });
}

// Stress test scenario
export function stressTest() {
    const response = http.get(`${BASE_URL}/products`);
    check(response, {
        'products loaded under stress': (r) => r.status === 200,
    });
}
