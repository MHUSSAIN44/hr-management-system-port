<!-- resources/js/Components/FilterDropdown.vue -->
<script setup>
    import { ref, watch } from 'vue';

    const props = defineProps({
        modelValue: [String, Number],
        label: String,
        options: Array,
        optionValue: {
            type: String,
            default: null
        },
        optionLabel: {
            type: String,
            default: null
        },
        allowEmpty: {
            type: Boolean,
            default: false
        }
    });

    const emit = defineEmits(['update:modelValue']);

    const selectedValue = ref(props.modelValue);

    watch(selectedValue, (newValue) => {
        emit('update:modelValue', newValue);
    });

    watch(() => props.modelValue, (newValue) => {
        selectedValue.value = newValue;
    });

    const getOptionValue = (option) => {
        if (props.optionValue) {
            return option[props.optionValue];
        }
        return option;
    };

    const getOptionLabel = (option) => {
        if (props.optionLabel) {
            return option[props.optionLabel];
        }
        return option;
    };
</script>

<template>
    <div class="min-w-0">
        <label v-if="label" class="block text-xs font-medium text-gray-700 mb-1">{{ label }}</label>
        <select
                v-model="selectedValue"
                class="block w-full min-w-[120px] pl-3 pr-10 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
        >
            <option v-if="allowEmpty" value="">All {{ label }}</option>
            <option v-for="option in options" :key="getOptionValue(option)" :value="getOptionValue(option)">
                {{ getOptionLabel(option) }}
            </option>
        </select>
    </div>
</template>