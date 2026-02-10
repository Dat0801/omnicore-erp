<script setup>
import Modal from '@/Components/Modal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { useForm } from '@inertiajs/vue3';
import { watch } from 'vue';

const props = defineProps({
    show: Boolean,
    inventory: Object,
    warehouses: Array,
});

const emit = defineEmits(['close']);

const form = useForm({
    source_warehouse_id: '',
    target_warehouse_id: '',
    product_id: '',
    quantity: '',
});

watch(() => props.inventory, (newInventory) => {
    if (newInventory) {
        form.source_warehouse_id = newInventory.warehouse_id;
        form.product_id = newInventory.product_id;
        form.target_warehouse_id = '';
        form.quantity = '';
    }
}, { immediate: true });

const submit = () => {
    form.post(route('admin.inventory.transfer'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            emit('close');
        },
    });
};
</script>

<template>
    <Modal :show="show" @close="$emit('close')">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900">
                Transfer Stock
            </h2>
            <p v-if="inventory" class="mt-1 text-sm text-gray-600">
                Moving {{ inventory.product?.name }} from {{ inventory.warehouse?.name }}
            </p>

            <div class="mt-6">
                <!-- Target Warehouse -->
                <div class="mb-4">
                    <InputLabel for="target_warehouse" value="To Warehouse" />
                    <select
                        id="target_warehouse"
                        v-model="form.target_warehouse_id"
                        class="mt-1 block w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm"
                    >
                        <option value="" disabled>Select a warehouse</option>
                        <option 
                            v-for="warehouse in warehouses" 
                            :key="warehouse.id" 
                            :value="warehouse.id"
                            :disabled="warehouse.id === form.source_warehouse_id"
                        >
                            {{ warehouse.name }}
                        </option>
                    </select>
                    <InputError :message="form.errors.target_warehouse_id" class="mt-2" />
                </div>

                <!-- Quantity -->
                <div class="mb-4">
                    <InputLabel for="quantity" value="Quantity" />
                    <TextInput
                        id="quantity"
                        type="number"
                        v-model="form.quantity"
                        class="mt-1 block w-full"
                        min="1"
                        :max="inventory?.quantity"
                        placeholder="Enter quantity"
                    />
                    <p class="mt-1 text-sm text-gray-500">Available: {{ inventory?.quantity }}</p>
                    <InputError :message="form.errors.quantity" class="mt-2" />
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <SecondaryButton @click="$emit('close')"> Cancel </SecondaryButton>
                <PrimaryButton class="ml-3" :class="{ 'opacity-25': form.processing }" :disabled="form.processing" @click="submit">
                    Transfer Stock
                </PrimaryButton>
            </div>
        </div>
    </Modal>
</template>
