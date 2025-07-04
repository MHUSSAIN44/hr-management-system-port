<script setup>
defineProps({
    status: {
        type: String,
        required: true
    }
});

const getStatusColor = (status) => {
    switch (status) {
        case 'Full-time':
            return 'bg-green-100 text-green-800';
        case 'Part-time':
            return 'bg-blue-100 text-blue-800';
        case 'Contract':
            return 'bg-purple-100 text-purple-800';
        case 'Probation':
            return 'bg-yellow-100 text-yellow-800';
        case 'Intern':
            return 'bg-indigo-100 text-indigo-800';
        default:
            return 'bg-gray-100 text-gray-800';
    }
};
</script>

<template>
    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full" :class="getStatusColor(status)">
        {{ status }}
    </span>
</template>
