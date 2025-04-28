<template>
    <input
        :type="type"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
        :id="id"
        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block w-full"
        :class="{ 'border-red-300': error }"
        v-bind="$attrs"
    />
</template>

<script setup>
defineProps({
    modelValue: {
        type: [String, Number],
        default: ''
    },
    type: {
        type: String,
        default: 'text'
    },
    id: {
        type: String,
        default: ''
    },
    error: {
        type: Boolean,
        default: false
    }
});

defineEmits(['update:modelValue']);
</script>