import http from 'k6/http';
import { check, sleep } from 'k6';

export const options = {
  stages: [
    { duration: '20s', target: 300 },
    { duration: '1m', target: 300 },
    { duration: '20s', target: 0 },
  ],
  thresholds: {
    http_req_failed: ['rate<0.05'],
    http_req_duration: ['p(95)<2000'],
  },
};

const BASE_URL = 'http://host.docker.internal:8080/api';

export function setup() {
  const loginRes = http.post(`${BASE_URL}/login`, JSON.stringify({
    email: 'admin@ordershield.com',
    password: '12345678',
  }), {
    headers: { 'Content-Type': 'application/json' },
  });

  check(loginRes, {
    'login status 200': (r) => r.status === 200,
    'login has token': (r) => !!r.json('token'),
  });

  return {
    token: loginRes.json('token'),
  };
}

export default function (data) {
  const headers = {
    Authorization: `Bearer ${data.token}`,
    'Content-Type': 'application/json',
  };

  const orderPayload = JSON.stringify({
    customer_id: '01kpcm3r50089c2h4eczpw2kws',
    address_id: '01kpcm3r5x8yjj15gt7p80r3q9',
    total_amount: 6500,
    source: 'load-test',
  });

  const orderRes = http.post(`${BASE_URL}/orders`, orderPayload, { headers });

  check(orderRes, {
    'order created': (r) => r.status === 201,
  });

  sleep(1);
}