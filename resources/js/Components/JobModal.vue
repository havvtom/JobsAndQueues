<template>
    <dialog
        class="z-50 m-0 min-h-full min-w-full overflow-y-auto bg-transparent backdrop:bg-transparent"
        ref="dialog"
    >
        <div
            class="fixed inset-0 z-50 overflow-y-auto px-4 py-6 sm:px-0"
            scroll-region
        >
            <Transition
                enter-active-class="ease-out duration-300"
                enter-from-class="opacity-0"
                enter-to-class="opacity-100"
                leave-active-class="ease-in duration-200"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <div
                    v-show="show"
                    class="fixed inset-0 transform transition-all"
                    @click="close"
                >
                    <div
                        class="absolute inset-0 bg-gray-500 opacity-75"
                    />
                </div>
            </Transition>

            <Transition
                enter-active-class="ease-out duration-300"
                enter-from-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                enter-to-class="opacity-100 translate-y-0 sm:scale-100"
                leave-active-class="ease-in duration-200"
                leave-from-class="opacity-100 translate-y-0 sm:scale-100"
                leave-to-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            >
                <div
                    v-show="show"
                    class="mb-6 transform overflow-hidden rounded-lg bg-white shadow-xl transition-all sm:mx-auto sm:w-full"
                    :class="maxWidthClass"
                >
                <form @submit.prevent="handleSubmit" v-if="showSlot" class="p-6">
                    <h2 class="text-xl font-bold mb-4">
                        {{ isEditMode ? 'Edit Job' : 'Add Job' }}
                    </h2>

                    <div class="mb-4">
                        <label for="name" class="block text-sm font-medium text-gray-700">
                            Job Name
                        </label>
                        <input
                            v-if="isEditMode"
                            :disabled="isEditMode"
                            id="name"
                            v-model="jobForm.name"
                            type="text"
                            class="mt-1 block w-full border-gray-300 text-gray-400 rounded-md shadow-sm"
                            required
                        />
                        <select                           
                            id="priority"
                            v-model="jobForm.name"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                            required
                        >
                        <option 
                            v-for="(className, index) in jobClasses" 
                            :key="index" 
                            :value="className">{{ className }}
                        </option>
                                                
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="priority" class="block text-sm font-medium text-gray-700">
                            Priority
                        </label>
                        <select
                            id="priority"
                            v-model="jobForm.priority"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                            required
                        >
                            <option value="" disabled>Select Priority</option>
                            <option value="1">Low</option>
                            <option value="2">Medium</option>
                            <option value="3">High</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="maxRetries" class="block text-sm font-medium text-gray-700">
                            Maximum Retries
                        </label>
                        <input
                            id="maxRetries"
                            v-model="jobForm.maxRetries"
                            type="number"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                            min="1"
                            required
                        />
                    </div>

                    <div class="mb-4">
                        <label for="parameters" class="block text-sm font-medium text-gray-700">
                            Parameters
                        </label>
                        <textarea
                            id="parameters"
                            v-model="jobForm.parameters"
                            rows="4"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                            placeholder="Enter JSON parameters"
                        ></textarea>
                    </div>

                    <div class="mb-4">
                        <label for="delayInSeconds" class="block text-sm font-medium text-gray-700">
                            Delay in Seconds
                        </label>
                        <input
                            id="delayInSeconds"
                            v-model="jobForm.delayInSeconds"
                            type="number"
                            min="0"
                            step="1"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                            required
                            placeholder="Enter seconds to delay"
                        />
                    </div>

                    <div class="flex justify-end gap-4">
                        <button
                            type="button"
                            @click="close"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-500 rounded-md hover:bg-blue-600"
                        >
                            {{ isEditMode ? 'Update Job' : 'Submit' }}
                        </button>
                    </div>
                </form>


                </div>
            </Transition>
        </div>
    </dialog>
</template>

<script setup>
    import { computed, onMounted, onUnmounted, ref, watch, reactive } from 'vue';

    const props = defineProps({
        show: {
            type: Boolean,
            default: false,
        },
        job: {
            type: Object,
            default: {}
        },
        jobClasses:{
            type: Array,
            default: []
        },
        maxWidth: {
            type: String,
            default: '2xl',
        },
        closeable: {
            type: Boolean,
            default: true,
        },
    });

    const dialog = ref();
    const showSlot = ref(props.show);

    watch(
        () => props.show,
        () => {
            if (props.show) {
                document.body.style.overflow = 'hidden';
                showSlot.value = true;
                dialog.value?.showModal();
            } else {
                document.body.style.overflow = '';
                setTimeout(() => {
                    dialog.value?.close();
                    showSlot.value = false;
                }, 200);
            }
        },
    );

    const close = () => {
        if (props.closeable) {
            emit('close');
        }
    };

    const closeOnEscape = (e) => {
        if (e.key === 'Escape') {
            e.preventDefault();
            if (props.show) {
                close();
            }
        }
    };

    onMounted(() => document.addEventListener('keydown', closeOnEscape));
    onUnmounted(() => {
        document.removeEventListener('keydown', closeOnEscape);
        document.body.style.overflow = '';
    });

    const maxWidthClass = computed(() => ({
        sm: 'sm:max-w-sm',
        md: 'sm:max-w-md',
        lg: 'sm:max-w-lg',
        xl: 'sm:max-w-xl',
        '2xl': 'sm:max-w-2xl',
    }[props.maxWidth]));

    const emit = defineEmits(['close', 'submitJob', 'updateJob']);

    // Detect edit mode
    const isEditMode = computed(() => !!props.job && Object.keys(props.job).length > 0);


    // Reactive form data
    const jobForm = reactive({
        name: '',
        priority: '',
        maxRetries: 3,
        parameters: '',
        scheduledAt: '',
    });

    // Watch the `job` prop and populate form data if editing
    watch(
        () => props.job,
        (newJob) => {
            if (newJob) {
                Object.assign(jobForm, newJob);
            } else {
                resetForm();
            }
        },
        { immediate: true }
    );

    const resetForm = () => {
        Object.assign(jobForm, {
            name: '',
            priority: '',
            maxRetries: 3,
            parameters: '',
            scheduledAt: '',
        });
    };

    // Handle form submission
    const handleSubmit = () => {
        if (isEditMode.value) {
            emit('updateJob', { ...jobForm });
        } else {
            emit('submitJob', { ...jobForm });
        }

        resetForm();
        close();
    };
</script>

