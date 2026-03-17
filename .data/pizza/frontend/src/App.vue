<template>
  <div class="app-shell">
    <div class="app-header">
      <h1>Menu</h1>
      <WaitingBadge :key="waitingBadgeKey" />
    </div>
    <div v-if="loading">Loading...</div>
    <div v-else-if="error">{{ error }}</div>
    <template v-else>
      <MenuSelector :items="items" @add-to-cart="handleAddToCart" />
      <CartSection :cart="cart" @order="showDialog = true" />
    </template>
    <OrderDialog v-model:show="showDialog" :cart="cart" @order-submitted="onOrderSubmitted" />

  </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { API_ITEMS } from './constants';
import WaitingBadge from './components/WaitingBadge.vue';
import MenuSelector from './components/MenuSelector.vue';
import CartSection from './components/CartSection.vue';
import OrderDialog from './components/OrderDialog.vue';

export default {
  name: 'App',
  components: { WaitingBadge, MenuSelector, CartSection, OrderDialog },
  setup() {
const items = ref([]);
const cart = ref([]);
const loading = ref(true);
const error = ref(null);
const showDialog = ref(false);
const waitingBadgeKey = ref(0);
const handleAddToCart = (item) => {
    const existing = cart.value.find(i => i.id === item.id);
    if (existing) {
      existing.quantity++;
    } else {
      cart.value.push(item);
    }
};
const onOrderSubmitted = () => {
  waitingBadgeKey.value++;
  cart.value = [];
};

    onMounted(async () => {
      document.documentElement.classList.add('dark');
      try {
        const response = await axios.get(API_ITEMS);
        items.value = response.data;
      } catch (e) {
        error.value = 'Failed to load data';
      } finally {
        loading.value = false;
      }
    });

    return { items, cart, loading, error, showDialog, waitingBadgeKey, handleAddToCart, onOrderSubmitted };
  }
}
</script>

<style>
@import './styles.css';
</style>
