<template>
    <label :for="forId" class="block font-medium text-sm text-gray-700">
        <slot />
    </label>
</template>

<script setup>
defineProps({
    forId: {
        type: String,
        required: true
    }
});
</script>