<script setup>
defineProps({
    status: {
        type: String,
        required: true
    }
});

const getStatusColor = (status) => {
    switch (status) {
        case 'present':
            return 'bg-green-100 text-green-800';
        case 'absent':
            return 'bg-red-100 text-red-800';
        case 'leave':
            return 'bg-yellow-100 text-yellow-800';
        case 'holiday':
            return 'bg-blue-100 text-blue-800';
        default:
            return 'bg-gray-100 text-gray-800';
    }
};
</script>

<template>
    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full" :class="getStatusColor(status)">
        {{ status.charAt(0).toUpperCase() + status.slice(1) }}
    </span>
</template>
