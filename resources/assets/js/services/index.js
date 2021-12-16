import axios from 'axios'

let webBaseURL = process.env.MIX_SENTRY_DSN_API;
let token = document.head.querySelector('meta[name="csrf-token"]') || ''

const request =  axios.create({
  baseURL: webBaseURL,
  headers: {
    'X-Requested-With': 'XMLHttpRequest',
    'X-CSRF-TOKEN': token
  }
});

export default request
