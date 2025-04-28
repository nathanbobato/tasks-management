<template>
    <input
        type="checkbox"
        :value="modelValue"
        @change="$emit('update:modelValue', $event.target.checked)"
        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
        :class="{ 'border-red-300': error }"
        v-bind="$attrs"
    />
</template>

<script setup>
defineProps({
    modelValue: {
        type: Boolean,
        default: false
    },
    error: {
        type: Boolean,
        default: false
    }
});

defineEmits(['update:modelValue']);
</script>