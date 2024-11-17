<template>
    <div class="container mx-auto p-4">
        <JobModal
            @close="closeModal"
            :show="showJobModal"
            :job="selectedJob"
            :jobClasses="jobClasses"
            @updateJob="updateJob"
            @submitJob="submitJobToQueue"
        />

        <h1 class="text-3xl font-semibold mb-6">Job Dashboard</h1>

        <!-- Add Job and Process Pending Tasks Buttons -->
        <div class="mb-4 flex gap-4">
            <button
                @click="addJobToQueue"
                class="px-6 py-2 bg-blue-500 text-white font-semibold rounded-lg hover:bg-blue-600"
            >
                Add Job to Queue
            </button>
            <button
                @click="processPendingTasks"
                class="px-6 py-2 bg-green-500 text-white font-semibold rounded-lg hover:bg-green-600"
            >
                Process Pending Tasks
            </button>
        </div>

        <!-- Job Table -->
        <div class="overflow-x-auto bg-white shadow-md rounded-lg">
            <table class="min-w-full table-auto">
                <thead class="bg-gray-800 text-white">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-medium">Name</th>
                        <th class="px-6 py-3 text-left text-sm font-medium">Status</th>
                        <th class="px-6 py-3 text-left text-sm font-medium">Priority</th>
                        <th class="px-6 py-3 text-left text-sm font-medium">Retries</th>
                        <th class="px-6 py-3 text-left text-sm font-medium">Maximum Retries</th>
                        <th class="px-6 py-3 text-left text-sm font-medium">Error</th>
                        <th class="px-6 py-3 text-left text-sm font-medium">Scheduled At</th>
                        <th class="px-6 py-3 text-left text-sm font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="job in jobs" :key="job.id" class="border-b">
                        <td class="px-6 py-4 text-sm font-medium">{{ job.name }}</td>
                        <td class="px-6 py-4 text-sm">{{ job.status }}</td>
                        <td class="px-6 py-4 text-sm">{{ job.priority }}</td>
                        <td class="px-6 py-4 text-sm">{{ job.retry_count }}</td>
                        <td class="px-6 py-4 text-sm">{{ job.max_retries }}</td>
                        <td class="px-6 py-4 text-sm">{{ job.error || 'No error' }}</td>
                        <td class="px-6 py-4 text-sm">{{ job.scheduled_at }}</td>
                        <td class="px-6 py-4 text-sm">
                            <button
                                v-if="job.status === 'failed'"
                                @click="retryJob(job.id)"
                                class="px-4 py-2 text-sm font-semibold text-white bg-yellow-500 hover:bg-yellow-600 rounded-lg"
                            >
                                Retry
                            </button>
                            <button
                                v-if="job.status === 'running'"
                                @click="cancelJob(job.id)"
                                class="px-4 py-2 text-sm font-semibold text-white bg-red-500 hover:bg-red-600 rounded-lg"
                            >
                                Cancel
                            </button>
                            <button
                                @click="editJob(job)"
                                class="px-4 py-2 text-sm font-semibold text-white bg-blue-500 hover:bg-blue-600 rounded-lg"
                            >
                                Edit
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script setup>
    import JobModal from '@/Components/JobModal.vue';
    import { router } from '@inertiajs/vue3';
    import { ref } from 'vue';

    const props = defineProps({
        jobs: Array,
        jobClasses: Array
    });

    const showJobModal = ref(false);
    const selectedJob = ref({});

    const closeModal = () => {
        showJobModal.value = false
        selectedJob.value = {}
    }

    const retryJob = (jobId) => {
        console.log(`Retrying job with ID: ${jobId}`);
    };

    const cancelJob = (jobId) => {
        console.log(`Canceling job with ID: ${jobId}`);
        router.post(`/jobs/cancel/${jobId}`);
    };

    const addJobToQueue = () => {
        showJobModal.value = true;
        
    };

    const submitJobToQueue = (data) => {
        router.post(`/jobs`, {
            data
        });
    };

    const editJob = (job) => {
        selectedJob.value = job; 
        showJobModal.value = true;
        console.log('Editing job:', job);
    };

    const updateJob = (data)=>{
        router.post(`/jobs/updateJob`, {
            data
        });
    }

    const processPendingTasks = () => {
        console.log('Processing pending tasks');
        router.post(`/jobs/process-pending`, {
            onSuccess: () => {
                alert('Pending tasks are being processed!');
            },
            onError: (error) => {
                console.error('Error processing tasks:', error);
                alert('Failed to process pending tasks. Check the console for details.');
            },
        });
    };
</script>
