<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

interface Props {
    className?: HTMLAttributes['class'];
}

defineProps<Props>();
</script>

<template>
    <div class="h-15 w-50 rounded-full overflow-hidden">
        <img :src="'/images/Fany.jpg'" alt="Imagen Fany" class="h-full w-full object-cover" />
    </div>
</template>
