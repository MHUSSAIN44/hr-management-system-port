<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    links: {
        type: Array,
        required: true
    }
});
</script>

<template>
    <div v-if="links.length > 3" class="flex flex-wrap justify-center md:justify-between items-center">
        <div class="text-sm text-gray-700 py-2">
            Showing <span class="font-medium">{{ links[0].label === '&laquo; Previous' ? links[1].label : links[0].label }}</span> to
            <span class="font-medium">{{ links[links.length - 2].label }}</span> of
            <span class="font-medium">{{ links[links.length - 1].meta?.total || '' }}</span> results
        </div>

        <div class="inline-flex rounded-md shadow-sm">
            <div v-for="(link, key) in links" :key="key" class="inline-flex">
                <Link
                    v-if="link.url"
                    :href="link.url"
                    class="relative inline-flex items-center px-4 py-2 text-sm font-medium"
                    :class="[
                        key === 0 ? 'rounded-l-md' : '',
                        key === links.length - 1 ? 'rounded-r-md' : '',
                        link.active
                            ? 'z-10 bg-primary-50 border-primary-500 text-primary-600'
                            : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
                        'border'
                    ]"
                    v-html="link.label"
                />
                <span
                    v-else
                    class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700"
                    :class="[
                        key === 0 ? 'rounded-l-md' : '',
                        key === links.length - 1 ? 'rounded-r-md' : '',
                    ]"
                    v-html="link.label"
                />
            </div>
        </div>
    </div>
</template>
