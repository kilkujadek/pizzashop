<template>
  <Dialog v-model:visible="visible" modal header="Szczegóły zamówienia" :style="{ width: '400px' }" class="order-dialog">
    <div class="form-field">
      <label>Email</label>
      <InputText v-model="email" placeholder="twoj@email.com" class="full-width" :class="{ 'p-invalid': emailError }" />
      <small v-if="emailError" class="p-error">Email jest wymagany</small>
    </div>
    <div class="form-field">
      <label>Adres dostawy</label>
      <Textarea v-model="address" rows="3" placeholder="Wpisz swój adres" class="full-width" :class="{ 'p-invalid': addressError }" />
      <small v-if="addressError" class="p-error">Adres jest wymagany</small>
    </div>
    <div v-if="serverError" class="p-error mb-4" style="text-align: center; font-weight: bold;">
      {{ serverError }}
    </div>
    <template #footer>
      <Button label="Anuluj" severity="secondary" @click="close" />
      <Button label="Złóż zamówienie" @click="submit" />
    </template>
  </Dialog>
</template>

<script>
import { ref, computed } from 'vue';
import axios from 'axios';
import Dialog from 'primevue/dialog';
import InputText from 'primevue/inputtext';
import Textarea from 'primevue/textarea';
import Button from 'primevue/button';
import { API_ORDERS_CREATE } from '../constants';

export default {
  name: 'OrderDialog',
  components: { Dialog, InputText, Textarea, Button },
  props: {
    show: Boolean,
    cart: {
      type: Array,
      default: () => []
    }
  },
  emits: ['update:show', 'order-submitted'],
  setup(props, { emit }) {
    const email = ref('');
    const address = ref('');
    const emailError = ref(false);
    const addressError = ref(false);
    const serverError = ref('');

    const visible = computed({
      get: () => props.show,
      set: (val) => emit('update:show', val)
    });

    const close = () => {
      email.value = '';
      address.value = '';
      emailError.value = false;
      addressError.value = false;
      emit('update:show', false);
    };

    const submit = async () => {
      emailError.value = !email.value.trim();
      addressError.value = !address.value.trim();
      serverError.value = ''; // Reset server error

      if (emailError.value || addressError.value) return;

      const total = props.cart.reduce((sum, item) => sum + item.price * item.quantity, 0);
      try {
        await axios.post(API_ORDERS_CREATE, {
          email: email.value.trim(),
          address: address.value.trim(),
          items: props.cart,
          total
        });
        close();
        emit('order-submitted');
      } catch (e) {
        if (e.response && e.response.data && e.response.data.error) {
          serverError.value = e.response.data.error;
        } else {
          serverError.value = 'Wystąpił błąd podczas składania zamówienia.';
        }
        console.error('Order failed:', e);
      }
    };

    return { visible, email, address, emailError, addressError, serverError, close, submit };
  }
}
</script>
