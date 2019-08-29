
const base_url = document.head.querySelector('meta[name="base-url"]');
const token = document.head.querySelector('meta[name="csrf-token"]');
const api_key = document.head.querySelector('meta[name="api-key"]');

export default {
  api_key: api_key.content || '',
  base_url: base_url.content || '',
  csrf_token: token.content || '',
};
