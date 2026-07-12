import { createRouter, createWebHistory } from 'vue-router';
import Login from '../pages/Auth/Login.vue';
import Tasks from '../pages/Tasks.vue';
// import TaskDetail from '../pages/TaskDetail.vue';
import { useAuthStore } from '../store/auth';
import Projects from '../pages/Projects.vue';
import ProjectDetail from '../pages/ProjectDetail.vue';
// import CreateTask from '../pages/CreateTask.vue';
import AuthenticatedLayout from '../layouts/AuthenticatedLayout.vue';
import CreateProject from '../pages/CreateProject.vue';
import CreateTask from '../pages/CreateTask.vue';
import TaskDetail from '../pages/TaskDetail.vue';

const routes = [
    { path: '/login', component: Login, meta: { guest: true } },
    { path: '/', component: Projects, meta: { auth: true } },
    { path: '/projects', component: Projects, meta: { auth: true } },
    { path: '/projects/:id', component: ProjectDetail, meta: { auth: true } },
    {
        path: '/projects/:projectId/tasks/create',
        component: CreateTask,
        meta: { auth: true },
    },
    // { path: '/projects/:id/tasks/create', component: CreateTask, meta: { auth: true } },
    // { path: '/tasks/:id', component: TaskDetail, meta: { auth: true } },
    {
        path: '/projects/create',
        component: CreateProject,
        meta: { auth: true },
    },
    {
        path: '/tasks/create',
        component: CreateTask,
        meta: { auth: true },
    },
    {
        path: '/tasks/:id',
        component: TaskDetail,
        meta: { auth: true },
    },
    { path: '/tasks', component: Tasks, meta: { auth: true } },

];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach((to, from, next) => {
    const authStore = useAuthStore();

    if (to.meta.auth && !authStore.isAuthenticated) {
        return next('/login');
    }

    if (to.meta.guest && authStore.isAuthenticated) {
        return next('/');
    }

    next();
});

export default router;