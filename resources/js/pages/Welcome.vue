<script setup lang="ts">
import { Head, Link } from "@inertiajs/vue3";
</script>

<template>
  <Head title="Welcome">
    <link rel="preconnect" href="https://rsms.me/" />
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
  </Head>

  <div
    class="flex min-h-screen flex-col items-center justify-center bg-[#FDFDFC] p-6 text-[#1b1b18] dark:bg-[#0a0a0a] dark:text-[#EDEDEC]"
  >
    <div class="flex w-full items-center justify-center">
      <main
        class="flex w-full max-w-sm flex-col overflow-hidden rounded-lg bg-white shadow-lg lg:max-w-4xl lg:flex-row dark:bg-gray-800"
      >
        <div class="relative hidden w-full lg:block lg:w-1/2">
          <img
            src="https://images.unsplash.com/photo-1510915694723-4549f217415e"
            alt="Ilustración de código"
            class="absolute inset-0 h-full w-full object-cover rounded-l-lg"
          />
          <div
            class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center"
          >
            <div class="text-center text-white p-6">
              <h2 class="text-3xl font-bold mb-2">¡Bienvenido!</h2>
              <p class="text-lg">
                Accede a tu cuenta o regístrate para comenzar a utilizar
                nuestros servicios.
              </p>
            </div>
          </div>
        </div>

        <div class="w-full p-8 lg:w-1/2">
          <div class="mb-8 flex justify-center space-x-4">
            <Link
              :href="route('login')"
              class="inline-block rounded-full px-6 py-2 text-sm font-semibold transition-colors duration-300"
              :class="{
                'bg-blue-600 text-white shadow-md': route().current('login'),
                'text-gray-600 hover:bg-gray-200 dark:text-gray-400 dark:hover:bg-gray-700': !route().current(
                  'login'
                ),
              }"
            >
              Login
            </Link>
            <Link
              :href="route('register')"
              class="inline-block rounded-full px-6 py-2 text-sm font-semibold transition-colors duration-300"
              :class="{
                'bg-blue-600 text-white shadow-md': route().current('register'),
                'text-gray-600 hover:bg-gray-200 dark:text-gray-400 dark:hover:bg-gray-700': !route().current(
                  'register'
                ),
              }"
            >
              Registro
            </Link>
          </div>

          <div class="text-center">
            <h1 class="text-2xl font-bold mb-4" v-if="route().current('login')">
              Inicia Sesión
            </h1>
            <h1 class="text-2xl font-bold mb-4" v-else>Crea tu Cuenta</h1>
            <p class="text-sm text-gray-500 mb-6 dark:text-gray-400">
              Por favor, completa los campos para continuar.
            </p>
          </div>
        </div>
      </main>
    </div>
  </div>
</template>
