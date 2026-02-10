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
});

const emit = defineEmits(['close']);

const form = useForm({
    warehouse_id: '',
    product_id: '',
    quantity: '',
    type: 'add',
    reason: '',
});

watch(() => props.inventory, (newInventory) => {
    if (newInventory) {
        form.warehouse_id = newInventory.warehouse_id;
        form.product_id = newInventory.product_id;
        form.quantity = '';
        form.type = 'add';
        form.reason = '';
    }
}, { immediate: true });

const submit = () => {
    form.post(route('admin.inventory.adjust'), {
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
                Adjust Stock
            </h2>
            <p v-if="inventory" class="mt-1 text-sm text-gray-600">
                {{ inventory.product?.name }} ({{ inventory.warehouse?.name }})
            </p>

            <div class="mt-6">
                <!-- Type Selection -->
                <div class="mb-4">
                    <InputLabel value="Adjustment Type" />
                    <div class="flex gap-4 mt-2">
                        <label class="flex items-center">
                            <input type="radio" v-model="form.type" value="add" class="mr-2 border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"> Add
                        </label>
                        <label class="flex items-center">
                            <input type="radio" v-model="form.type" value="remove" class="mr-2 border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"> Remove
                        </label>
                        <label class="flex items-center">
                            <input type="radio" v-model="form.type" value="set" class="mr-2 border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"> Set to
                        </label>
                    </div>
                    <InputError :message="form.errors.type" class="mt-2" />
                </div>

                <!-- Quantity -->
                <div class="mb-4">
                    <InputLabel for="quantity" value="Quantity" />
                    <TextInput
                        id="quantity"
                        type="number"
                        v-model="form.quantity"
                        class="mt-1 block w-full"
                        min="0"
                        placeholder="Enter quantity"
                    />
                    <InputError :message="form.errors.quantity" class="mt-2" />
                </div>

                <!-- Reason -->
                <div class="mb-4">
                    <InputLabel for="reason" value="Reason" />
                    <TextInput
                        id="reason"
                        type="text"
                        v-model="form.reason"
                        class="mt-1 block w-full"
                        placeholder="e.g. Received shipment, Damaged, Stock count"
                    />
                    <InputError :message="form.errors.reason" class="mt-2" />
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <SecondaryButton @click="$emit('close')"> Cancel </SecondaryButton>
                <PrimaryButton class="ml-3" :class="{ 'opacity-25': form.processing }" :disabled="form.processing" @click="submit">
                    Save Adjustment
                </PrimaryButton>
            </div>
        </div>
    </Modal>
</template>
