import { defineStore } from 'pinia';
import { router } from '@inertiajs/vue3';

export const useTasksStore = defineStore('tasks', {
    state: () => ({
        tasks: [],
        filters: {
            status: '',
        },
        pagination: {
            currentPage: 1,
            lastPage: 1,
            perPage: 10,
            total: 0,
        },
    }),

    actions: {
        async fetchTasks() {
            router.get(route('tasks.index'), this.filters, {
                preserveState: true,
                preserveScroll: true,
                onSuccess: (page) => {
                    this.tasks = page.props.tasks.data;
                    this.pagination = {
                        currentPage: page.props.tasks.current_page,
                        lastPage: page.props.tasks.last_page,
                        perPage: page.props.tasks.per_page,
                        total: page.props.tasks.total,
                    };
                },
            });
        },

        async createTask(taskData) {
            router.post(route('tasks.store'), taskData, {
                onSuccess: () => {
                    this.fetchTasks();
                },
            });
        },

        async updateTask(taskId, taskData) {
            router.put(route('tasks.update', taskId), taskData, {
                onSuccess: () => {
                    this.fetchTasks();
                },
            });
        },

        async deleteTask(taskId) {
            if (confirm('Are you sure you want to delete this task?')) {
                router.delete(route('tasks.destroy', taskId), {
                    onSuccess: () => {
                        this.fetchTasks();
                    },
                });
            }
        },

        setFilter(filter) {
            this.filters = { ...this.filters, ...filter };
            this.fetchTasks();
        },
    },
});