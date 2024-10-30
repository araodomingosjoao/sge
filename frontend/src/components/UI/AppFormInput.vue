<template>
    <div class="mb-3">
        <label :for="id" class="form-label">{{ label }}</label>
        <input
            :id="id"
            :type="type"
            :placeholder="placeholder"
            class="form-control"
            v-model="modelValue"
            :class="{'is-invalid': errors.length > 0}"
            @input="$emit('update:modelValue', modelValue)"
        />
        <div v-if="errors.length" class="invalid-feedback">
            <span v-for="(error, index) in errors" :key="index">{{ error }}</span>
        </div>
    </div>
</template>

<script>
import { defineComponent, computed } from 'vue';
import { useField } from 'vee-validate';

export default defineComponent({
    name: 'AppFormInput',
    props: {
        id: {
            type: String,
            required: true,
        },
        label: {
            type: String,
            required: true,
        },
        placeholder: {
            type: String,
            default: '',
        },
        modelValue: {
            type: String,
            default: '',
        },
        type: {
            type: String,
            default: 'text',
        },
        rules: {
            type: String,
            default: '',
        },
    },
    setup(props) {
        const { value, errorMessage, handleBlur } = useField(props.label, props.rules);
        
        const errors = computed(() => {
            return errorMessage.value ? [errorMessage.value] : [];
        });

        return {
            modelValue: value,
            errors,
            handleBlur,
        };
    },
});
</script>
