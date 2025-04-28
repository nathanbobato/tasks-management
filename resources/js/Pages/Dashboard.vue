<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Your Tasks
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Add Task Form -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-medium mb-4">Add New Task</h3>
                        <form @submit.prevent="submit" class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Title</label>
                                <input v-model="form.title" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <div v-if="form.errors.title" class="text-red-500 text-sm mt-1">{{ form.errors.title }}</div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Description</label>
                                <textarea v-model="form.description" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                                <div v-if="form.errors.description" class="text-red-500 text-sm mt-1">{{ form.errors.description }}</div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Status</label>
                                <select v-model="form.status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="pending">Pending</option>
                                    <option value="in_progress">In Progress</option>
                                    <option value="completed">Completed</option>
                                </select>
                                <div v-if="form.errors.status" class="text-red-500 text-sm mt-1">{{ form.errors.status }}</div>
                            </div>
                            <div v-if="form.hasErrors" class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded relative mb-4">
                                Please fix the errors in the form.
                            </div>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Add Task
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Tasks Table -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <!-- Filter Section -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700">Filter by Status:</label>
                        <select
                            :value="taskStore.statusFilter"
                            @change="(e) => taskStore.setStatusFilter(e.target.value)"
                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
                        >
                            <option value="">All</option>
                            <option value="pending">Pending</option>
                            <option value="in_progress">In Progress</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>

                    <!-- Tasks Table -->
                    <div class="overflow-x-auto min-h-[610px]">
                        <div v-if="taskStore.isLoading" class="text-center py-4">
                            Loading tasks...
                        </div>
                        <div v-else-if="taskStore.error" class="text-center py-4 text-red-500">
                            {{ taskStore.error }}
                        </div>
                        <div v-else-if="taskStore.tasks.data.length === 0" class="text-center py-4">
                            No tasks found.
                        </div>
                        <table v-else class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Title
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Description
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="task in filteredTasks"
                                    :key="task.id"
                                    class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ task.title }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-500 max-w-xs truncate">{{ task.description }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span :class="[
                                            'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                                            task.status === 'completed' ? 'bg-green-100 text-green-800' :
                                            task.status === 'in_progress' ? 'bg-blue-100 text-blue-800' :
                                            'bg-yellow-100 text-yellow-800'
                                        ]">
                                            {{ task.status === 'in_progress' ? 'In Progress' :
                                               task.status === 'completed' ? 'Completed' :
                                               'Pending' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button
                                            @click="editTask(task)"
                                            class="text-indigo-600 hover:text-indigo-900 mr-3"
                                        >
                                            Edit
                                        </button>
                                        <button
                                            @click="confirmDelete(task)"
                                            class="text-red-600 hover:text-red-900"
                                        >
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div v-if="taskStore.tasks.data.length > 0" class="mt-6">
                        <Pagination
                            :current-page="taskStore.currentPage"
                            :total="taskStore.tasks.total"
                            @page-changed="taskStore.setPage"
                        />
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Dialog -->
        <ConfirmationDialog
            :is-open="showDeleteDialog"
            title="Delete Task"
            :message="`Are you sure you want to delete the task '${taskToDelete?.title}'?`"
            confirm-button-text="Delete"
            @confirm="handleDelete"
            @cancel="cancelDelete"
        />

        <!-- Edit Task Dialog -->
        <div v-if="showEditDialog" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity">
            <div class="fixed inset-0 z-10 overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-lg bg-white px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6">
                        <div class="absolute right-0 top-0 hidden pr-4 pt-4 sm:block">
                            <button @click="cancelEdit" type="button" class="rounded-md bg-white text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                <span class="sr-only">Close</span>
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                                <h3 class="text-lg font-semibold leading-6 text-gray-900">Edit Task</h3>
                                <div class="mt-4">
                                    <form class="space-y-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Title</label>
                                            <input v-model="editForm.title" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <div v-if="editForm.errors.title" class="text-red-500 text-sm mt-1">{{ editForm.errors.title }}</div>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Description</label>
                                            <textarea v-model="editForm.description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                                            <div v-if="editForm.errors.description" class="text-red-500 text-sm mt-1">{{ editForm.errors.description }}</div>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Status</label>
                                            <select v-model="editForm.status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                <option value="pending">Pending</option>
                                                <option value="in_progress">In Progress</option>
                                                <option value="completed">Completed</option>
                                            </select>
                                            <div v-if="editForm.errors.status" class="text-red-500 text-sm mt-1">{{ editForm.errors.status }}</div>
                                        </div>
                                        <div v-if="editForm.hasErrors" class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded relative mb-4">
                                            Please fix the errors in the form.
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                            <button @click="handleEdit" type="button" class="inline-flex w-full justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 sm:ml-3 sm:w-auto">
                                Save
                            </button>
                            <button @click="cancelEdit" type="button" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3';
import { onMounted, watch, ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Pagination from '@/Components/Pagination.vue';
import ConfirmationDialog from '@/Components/ConfirmationDialog.vue';
import { useTaskStore } from '@/Stores/taskStore';

const page = usePage();
const taskStore = useTaskStore();

const form = useForm({
    title: '',
    description: '',
    status: 'pending'
});

// Confirmation dialog state
const showDeleteDialog = ref(false);
const taskToDelete = ref(null);

// Edit dialog state
const showEditDialog = ref(false);
const taskToEdit = ref(null);
const editForm = useForm({
    title: '',
    description: '',
    status: 'pending'
});

// Add computed property for filtered tasks
const filteredTasks = computed(() => {
    if (!taskStore.statusFilter) return taskStore.tasks.data;
    return taskStore.tasks.data.filter(task => task.status === taskStore.statusFilter);
});

// Initialize store with initial props
onMounted(() => {
    if (page.props.tasks) {
        taskStore.tasks = {
            data: page.props.tasks.data || [],
            links: page.props.tasks.links || {},
            current_page: page.props.tasks.current_page || 1,
            last_page: page.props.tasks.last_page || 1,
            per_page: page.props.tasks.per_page || 10,
            total: page.props.tasks.total || 0
        };

        // Set initial filter from props if available
        if (page.props.filters?.status !== undefined) {
            taskStore.statusFilter = page.props.filters.status;
        }
    }
    taskStore.fetchTasks();
});

// Watch for changes in the store
watch(() => taskStore.tasks, (newTasks) => {
}, { deep: true });

watch(() => taskStore.isLoading, (loading) => {
});

watch(() => taskStore.error, (error) => {
});

watch(() => taskStore.statusFilter, (newFilter) => {
}, { immediate: true });

watch(() => taskStore.currentPage, (newPage) => {
}, { immediate: true });

const submit = () => {
    taskStore.createTask(form);
    form.reset();
};

const editTask = (task) => {
    taskToEdit.value = task;
    editForm.title = task.title;
    editForm.description = task.description;
    editForm.status = task.status;
    showEditDialog.value = true;
};

const handleEdit = async () => {
    if (taskToEdit.value) {
        await taskStore.updateTask(taskToEdit.value.id, editForm);
        showEditDialog.value = false;
        taskToEdit.value = null;
        editForm.reset();
    }
};

const cancelEdit = () => {
    showEditDialog.value = false;
    taskToEdit.value = null;
    editForm.reset();
};

const confirmDelete = (task) => {
    taskToDelete.value = task;
    showDeleteDialog.value = true;
};

const handleDelete = async () => {
    if (taskToDelete.value) {
        await taskStore.deleteTask(taskToDelete.value.id);
        showDeleteDialog.value = false;
        taskToDelete.value = null;
    }
};

const cancelDelete = () => {
    showDeleteDialog.value = false;
    taskToDelete.value = null;
};
</script>