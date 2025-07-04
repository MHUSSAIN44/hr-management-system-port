<!-- resources/js/Components/DocumentUploader.vue -->
<script setup>
    import { ref, watch, nextTick } from 'vue';
    import InputLabel from '@/Components/InputLabel.vue';
    import TextInput from '@/Components/TextInput.vue';
    import InputError from '@/Components/InputError.vue';

    const props = defineProps({
        modelValue: {
            type: Array,
            default: () => []
        },
        errors: {
            type: Object,
            default: () => ({})
        }
    });

    const emit = defineEmits(['update:modelValue']);

    // Initialize with props value
    const documents = ref([...props.modelValue]);

    // Flag to prevent infinite loops
    const isUpdatingFromProps = ref(false);

    // Watch for external changes (parent to child)
    watch(() => props.modelValue, (newValue) => {
        if (!isUpdatingFromProps.value) {
            isUpdatingFromProps.value = true;
            documents.value = [...newValue];
            nextTick(() => {
                isUpdatingFromProps.value = false;
            });
        }
    }, { deep: true });

    // Function to emit changes (child to parent)
    const emitUpdate = () => {
        if (!isUpdatingFromProps.value) {
            emit('update:modelValue', [...documents.value]);
        }
    };

    const addDocument = () => {
        documents.value.push({
            title: '',
            file: null,
            expiry_date: ''
        });
        emitUpdate();
    };

    const removeDocument = (index) => {
        documents.value.splice(index, 1);
        emitUpdate();
    };

    const handleFileChange = (index, event) => {
        documents.value[index].file = event.target.files[0];
        emitUpdate();
    };

    const updateDocumentField = (index, field, value) => {
        documents.value[index][field] = value;
        emitUpdate();
    };
</script>

<template>
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h4 class="text-lg font-medium text-gray-900">Employee Documents</h4>
            <button
                    type="button"
                    @click="addDocument"
                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Add Document
            </button>
        </div>

        <div v-if="documents.length === 0" class="text-center py-8 text-gray-500">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <p class="mt-2">No documents added yet.</p>
            <button
                    type="button"
                    @click="addDocument"
                    class="mt-2 text-blue-600 hover:text-blue-900"
            >
                Add your first document
            </button>
        </div>

        <div v-for="(document, index) in documents" :key="`doc-${index}`" class="border border-gray-200 rounded-lg p-4 bg-gray-50">
            <div class="flex items-center justify-between mb-4">
                <h5 class="text-sm font-medium text-gray-700">Document {{ index + 1 }}</h5>
                <button
                        type="button"
                        @click="removeDocument(index)"
                        class="text-red-600 hover:text-red-900"
                        v-if="documents.length > 0"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Document Title -->
                <div>
                    <InputLabel :for="`document_title_${index}`" value="Document Title" />
                    <TextInput
                            :id="`document_title_${index}`"
                            :model-value="document.title"
                            @update:model-value="updateDocumentField(index, 'title', $event)"
                            type="text"
                            class="mt-1 block w-full"
                            placeholder="e.g., Academic Certificate"
                    />
                    <InputError :message="errors[`documents.${index}.title`]" class="mt-2" />
                </div>

                <!-- Document File -->
                <div>
                    <InputLabel :for="`document_file_${index}`" value="Document File" />
                    <input
                            type="file"
                            :id="`document_file_${index}`"
                            @change="handleFileChange(index, $event)"
                            class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                            accept="image/*,application/pdf,.doc,.docx"
                    />
                    <InputError :message="errors[`documents.${index}.file`]" class="mt-2" />
                    <p class="mt-1 text-xs text-gray-500">PDF, DOC, DOCX, or image files only</p>
                </div>

                <!-- Expiry Date -->
                <div>
                    <InputLabel :for="`document_expiry_${index}`" value="Expiry Date (Optional)" />
                    <TextInput
                            :id="`document_expiry_${index}`"
                            :model-value="document.expiry_date"
                            @update:model-value="updateDocumentField(index, 'expiry_date', $event)"
                            type="date"
                            class="mt-1 block w-full"
                    />
                    <InputError :message="errors[`documents.${index}.expiry_date`]" class="mt-2" />
                </div>
            </div>
        </div>
    </div>
</template>