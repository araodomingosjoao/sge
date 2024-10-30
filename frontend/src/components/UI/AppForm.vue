<template>
    <form @submit.prevent="handleSubmit">
        <slot />
    </form>
</template>

<script>
import { defineComponent } from 'vue';
import { useForm } from 'vee-validate';

export default defineComponent({
    name: 'AppForm',
    setup(_, { emit }) {
        const { handleSubmit } = useForm();

        const submitForm = handleSubmit((values) => {
            emit('submit', values);
        });

        return {
            handleSubmit: submitForm,
        };
    },
});
</script>
