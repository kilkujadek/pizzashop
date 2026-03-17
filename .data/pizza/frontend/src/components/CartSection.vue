<template>
  <div class="cart-section">
    <h2>Koszyk</h2>
    <div v-if="cart.length === 0">Twój koszyk jest pusty</div>
    <template v-else>
      <div class="cart-items">
        <div v-for="item in cart" :key="item.id" class="cart-item">
          <span class="cart-name">{{ item.name }}</span>
          <span class="cart-qty">x{{ item.quantity }}</span>
          <span class="cart-total">{{ item.price * item.quantity }} zł</span>
        </div>
      </div>
      <div class="cart-summary">
        <strong>Suma: {{ total }} zł</strong>
        <Button label="Zamów" @click="$emit('order')" class="order-btn" />
      </div>
    </template>
  </div>
</template>

<script>
import { computed } from 'vue';
import Button from 'primevue/button';

export default {
  name: 'CartSection',
  components: { Button },
  props: {
    cart: {
      type: Array,
      default: () => []
    }
  },
  emits: ['order', 'add-to-cart'],
  setup(props, { emit }) {
    const total = computed(() => props.cart.reduce((sum, item) => sum + item.price * item.quantity, 0));

    return { total };
  },
}
</script>
