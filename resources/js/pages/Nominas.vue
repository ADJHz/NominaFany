<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import PlaceholderPattern from '../components/PlaceholderPattern.vue';
import { usePage, router } from '@inertiajs/vue3';
import { computed, ref, watch, onMounted } from 'vue';
import Swal from 'sweetalert2';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Nominas',
        href: '/nominas',
    },
];

// Estado para guardar el archivo seleccionado
const file = ref<File | null>(null);
const loading = ref(false);

// Mensajes de éxito y error
const page = usePage();
const success = computed(() => page.props.flash?.success);
const error = computed(() => page.props.flash?.error);
const files = computed(() => page.props.flash?.files || []);

// Mostrar SweetAlert2 cuando hay éxito o error (reactivo)
watch(success, (val) => {
    if (val) {
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: val,
            confirmButtonColor: '#2563eb'
        });
    }
});
watch(error, (val) => {
    if (val) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: val,
            confirmButtonColor: '#dc2626'
        });
    }
});

// Mostrar SweetAlert2 al montar si ya hay mensaje (por navegación Inertia)
onMounted(() => {
    if (success.value) {
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: success.value,
            confirmButtonColor: '#2563eb'
        });
    }
    if (error.value) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: error.value,
            confirmButtonColor: '#dc2626'
        });
    }
});

// Función que se ejecuta cuando se selecciona un archivo
const handleFileChange = (event: Event) => {
    const input = event.target as HTMLInputElement;
    if (input.files && input.files.length > 0) {
        file.value = input.files[0];
    }
};

// Función para procesar el archivo y enviarlo al controlador de Laravel
const generateReceipts = () => {
    if (file.value) {
        loading.value = true;
        router.post(route('nominas.procesar'), {
            excel_file: file.value
        }, {
            forceFormData: true,
            onFinish: () => {
                loading.value = false;
                file.value = null; // Limpiar el archivo después de enviar
            }
        });
    }
};

// Descarga los recibos en ZIP
const downloadReceipts = () => {
    if (files.value.length > 0) {
        // Usamos fetch para enviar los nombres de los archivos y descargar el ZIP
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = route('nominas.descargarRecibos');
        form.style.display = 'none';

        // CSRF token
        const csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content || '';
        form.appendChild(csrf);

        // Archivos
        files.value.forEach((file: string) => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'files[]';
            input.value = file;
            form.appendChild(input);
        });

        document.body.appendChild(form);
        form.submit();
        document.body.removeChild(form);
    }
};
</script>

<template>
    <Head title="Nominas" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 overflow-x-auto">
            <!-- Spinner de Flowbite -->
            <div v-if="loading" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
                <div class="flex flex-col items-center gap-4 bg-white p-8 rounded-lg shadow-lg">
                    <!-- Flowbite spinner -->
                    <div class="flex items-center justify-center">
                        <div class="w-12 h-12 border-4 border-blue-500 border-t-transparent border-solid rounded-full animate-spin"></div>
                    </div>
                    <span class="text-lg font-semibold text-blue-700">Se están generando los recibos de nómina...</span>
                </div>
            </div>

            <!-- Mensajes de éxito/error
            <div v-if="success" class="mb-4 p-4 rounded bg-green-100 text-green-800">
                {{ success }}
                <button
                    v-if="files.length > 0"
                    @click="downloadReceipts"
                    class="ml-4 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
                >
                    Descargar recibos ZIP
                </button>
            </div>-->
            <div v-if="error" class="mb-4 p-4 rounded bg-red-100 text-red-800">
                {{ error }}
            </div>
            <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                <div class="md:col-span-3">
                    <div class="flex items-center justify-center w-full">
                        <label for="dropzone-file" class="flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-bray-800 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-600">
                            <div v-if="!file" class="flex flex-col items-center justify-center pt-5 pb-6">
                                <svg class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.207l-.766-.766A4.5 4.5 0 0 0 1 10.5a4.5 4.5 0 0 0 9.246 1H13Z"/>
                                </svg>
                                <p class="mb-2 text-sm text-gray-500 dark:text-gray-400">
                                    <span class="font-semibold">Haz clic para subir</span> o arrastra y suelta
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    Solo archivos .xlsx
                                </p>
                            </div>
                            <div v-else class="flex flex-col items-center justify-center pt-5 pb-6">
                                <svg class="w-10 h-10 mb-3 text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512">
                                    <path fill="currentColor" d="M369.9 97.9L286 14C277.2 5.2 265.9 0 254.6 0H24c-13.3 0-24 10.7-24 24v464c0 13.3 10.7 24 24 24h336c13.3 0 24-10.7 24-24V124.9c0-11.4-4.6-22-12.1-29.1zm-158 13.7l60.2 46.8-59.5 59.5c-4 4-4 10.4 0 14.4l1.2 1.2c4 4 10.4 4 14.4 0l65.8-65.8c4-4 4-10.4 0-14.4L213 128c-4-4-10.4-4-14.4 0l-1.2 1.2c-4 4-4 10.4 0 14.4zM336 480H48V32h160v104c0 13.2 10.8 24 24 24h104v320zm-208-160v-64h-32v64h-32v-64h-32v64H48V208h288v112h-96zm-96-32h32v-32h-32v32zm-64 0h32v-32h-32v32zm-64 0h32v-32h-32v32zm192 0h32v-32h-32v32zM192 384h32v-32h-32v32zM128 384h32v-32h-32v32zM64 384h32v-32h-32v32zM320 384h32v-32h-32v32z"/>
                                </svg>
                                <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ file.name }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Archivo listo para procesar.</p>
                            </div>
                            <input id="dropzone-file" type="file" class="hidden" accept=".xlsx" @change="handleFileChange" />
                        </label>
                    </div>
                    <div v-if="file" class="mt-4 flex justify-center">
                        <button
                            @click="generateReceipts"
                            :disabled="loading"
                            class="inline-flex items-center px-6 py-3 text-base font-medium text-white bg-blue-600 rounded-lg shadow-md hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-300 dark:bg-blue-500 dark:hover:bg-blue-600 dark:focus:ring-blue-800"
                        >
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m-5 3h4a2 2 0 002-2V7a2 2 0 00-2-2H9a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            Generar recibos de nómina
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
