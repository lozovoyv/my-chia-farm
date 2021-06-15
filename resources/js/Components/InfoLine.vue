<template>
    <div class="px-6 my-4">
        <div class="block font-medium text-sm text-gray-700">
            <span v-if="label">{{ label }}:</span> <span class="font-bold">{{ value }}</span> <slot></slot>
        </div>
    </div>
</template>

<script>
export default {
    props: ['label', 'value'],
}
</script>

