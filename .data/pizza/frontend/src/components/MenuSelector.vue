<template>
  <div class="menu-section">
    <div class="menu-selector">
      <Select v-model="selectedItem" :options="items" optionLabel="name" placeholder="Wybierz pizzę" class="pizza-select">
        <template #option="{ option }">
          <span class="pizza-option">{{ option.name }}</span>
          <span class="pizza-option-price">{{ option.price }} zł</span>
        </template>
        <template #value="{ value }">
          <span v-if="value" class="pizza-option">{{ value.name }}</span>
          <span v-if="value" class="pizza-option-price">{{ value.price }} zł</span>
          <span v-else>Wybierz pizzę</span>
        </template>
      </Select>
      <Button label="Dodaj" @click="addToCart" :disabled="!selectedItem" />
    </div>
  </div>
</template>

<script>
import { ref } from 'vue';
import Select from 'primevue/select';
import Button from 'primevue/button';

export default {
  name: 'MenuSelector',
  components: { Select, Button },
  props: {
    items: {
      type: Array,
      default: () => []
    }
  },
  emits: ['add-to-cart'],
  setup(props, { emit }) {
    const selectedItem = ref(null);

    const addToCart = () => {
      if (!selectedItem.value) return;
      emit('add-to-cart', { ...selectedItem.value, quantity: 1 });
      // selectedItem.value = null; // Optionally reset selection after adding to cart
    };

    return { selectedItem, addToCart };
  }
}
</script>
