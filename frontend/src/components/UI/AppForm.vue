<template>
    <form @submit.stop.prevent="handleSubmit">
        <slot></slot>
    </form>
</template>

<script>
import { useForm } from 'vee-validate';

export default {
    name: 'AppForm',
    setup(_, { emit }) {
        const { validate } = useForm();

        const handleSubmit = async () => {
            const isValid = await validate();

            if (isValid.valid) {
                emit('event:submit');
            }
        };

        return {
            handleSubmit,
        };
    },
};
</script>
