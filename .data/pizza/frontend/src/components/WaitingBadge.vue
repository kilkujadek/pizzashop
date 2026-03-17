<template>
  <div class="waiting-badge">
    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
    <span>{{ minutes }} min</span>
  </div>
</template>

<script>
import { ref, onMounted, onUnmounted } from 'vue';
import axios from 'axios';
import { API_ORDERS, REFRESH_INTERVAL } from '../constants';

export default {
  name: 'WaitingBadge',
  setup() {
    const minutes = ref(0);
    let refreshInterval = null;

    const loadWaitingTime = async () => {
      console.log('Fetching waiting time...');
      try {
        const response = await axios.get(API_ORDERS);
        if (response.data) {
          minutes.value = response.data.waiting_minutes || 0;
        }
      } catch (e) {
        console.error('Failed to load waiting time:', e.message);
      }
    };

    onMounted(() => {
      loadWaitingTime();
      refreshInterval = setInterval(loadWaitingTime, REFRESH_INTERVAL);
    });

    onUnmounted(() => {
      if (refreshInterval) clearInterval(refreshInterval);
    });

    return { minutes, loadWaitingTime };
  }
}
</script>
