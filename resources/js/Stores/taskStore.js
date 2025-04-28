import { defineStore } from 'pinia';
import { router } from '@inertiajs/vue3';

export const useTaskStore = defineStore('task', {
    state: () => ({
        tasks: {
            data: [],
            links: {},
            current_page: 1,
            last_page: 1,
            per_page: 10,
            total: 0
        },
        isLoading: false,
        error: null,
        statusFilter: '',
        currentPage: 1
    }),

    getters: {
        filteredTasks: (state) => {
            if (!state.statusFilter) return state.tasks.data;
            return state.tasks.data.filter(task => task.status === state.statusFilter);
        },
        paginatedTasks: (state) => {
            const start = (state.currentPage - 1) * state.per_page;
            const end = start + state.per_page;
            return state.filteredTasks.slice(start, end);
        }
    },

    actions: {
        async fetchTasks() {
            this.isLoading = true;
            this.error = null;
            try {
                const response = await router.get(route('dashboard'), {
                    page: this.currentPage,
                    status: this.statusFilter
                }, {
                    preserveState: true,
                    preserveScroll: true,
                    onSuccess: (page) => {
                        if (page.props.tasks) {
                            this.tasks = {
                                data: page.props.tasks.data || [],
                                links: page.props.tasks.links || {},
                                current_page: page.props.tasks.current_page || 1,
                                last_page: page.props.tasks.last_page || 1,
                                per_page: page.props.tasks.per_page || 10,
                                total: page.props.tasks.total || 0
                            };
                            this.currentPage = page.props.tasks.current_page || 1;
                        }
                    }
                });
            } catch (error) {
                this.error = error.message || 'Failed to fetch tasks';
            } finally {
                this.isLoading = false;
            }
        },

        async createTask(form) {
            this.isLoading = true;
            this.error = null;
            try {
                await router.post(route('tasks.store'), form, {
                    preserveState: true,
                    preserveScroll: true,
                    onSuccess: () => {
                        this.fetchTasks();
                    }
                });
            } catch (error) {
                this.error = error.message || 'Failed to create task';
            } finally {
                this.isLoading = false;
            }
        },

        async updateTask(taskId, form) {
            this.isLoading = true;
            this.error = null;
            try {
                await router.put(route('tasks.update', taskId), form, {
                    preserveState: true,
                    preserveScroll: true,
                    onSuccess: () => {
                        // Always fetch fresh data after an update
                        this.fetchTasks();
                    }
                });
            } catch (error) {
                this.error = error.message || 'Failed to update task';
                this.fetchTasks();
            } finally {
                this.isLoading = false;
            }
        },

        async deleteTask(taskId) {
            this.isLoading = true;
            this.error = null;
            try {
                await router.delete(route('tasks.destroy', taskId), {
                    preserveState: true,
                    preserveScroll: true,
                    onSuccess: () => {
                        this.fetchTasks();
                    }
                });
            } catch (error) {
                this.error = error.message || 'Failed to delete task';
            } finally {
                this.isLoading = false;
            }
        },

        setPage(page) {
            this.currentPage = page;
            this.fetchTasks();
        },

        setStatusFilter(status) {
            this.statusFilter = status;
            this.currentPage = 1;
            this.fetchTasks();
        }
    }
});