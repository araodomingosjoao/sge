<template>
    <div class="mb-3">
        <label :for="id" class="form-label">{{ label }}</label>
        <select
            :id="id"
            class="form-select"
            :value="selectValue"
            :class="{'is-invalid': errors.length > 0}"
            @change="handleSelect"
            :disabled="disabled"
        >
            <option value="">{{ placeholder }}</option>
            <option 
                v-for="option in options" 
                :key="option[optionValue]"
                :value="option[optionValue]"
            >
                {{ option[optionLabel] }}
            </option>
        </select>
        <div v-if="errors.length" class="invalid-feedback">
            <span v-for="(error, index) in errors" :key="index">{{
                error
            }}</span>
        </div>
    </div>
</template>

<script>
import { defineComponent, computed, watch } from "vue";
import { useField } from "vee-validate";

export default defineComponent({
    name: "AppFormSelect",
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
            default: "Selecione...",
        },
        modelValue: {
            type: [String, Number],
            default: "",
        },
        options: {
            type: Array,
            required: true,
        },
        optionLabel: {
            type: String,
            default: "name",
        },
        optionValue: {
            type: String,
            default: "id",
        },
        rules: {
            type: String,
            default: "",
        },
    },
    emits: ["update:modelValue"],
    setup(props, { emit }) {
        const { errorMessage, value, validate, handleChange } = useField(props.id, props.rules);
        
        watch(() => props.modelValue, (newValue) => {
            value.value = newValue;
            validate();
        }, { immediate: true });

        const errors = computed(() => {
            return errorMessage.value ? [errorMessage.value] : [];
        });

        const handleSelect = (event) => {
            const newValue = event.target.value;
            value.value = newValue;
            emit('update:modelValue', newValue);
        };

        return {
            errors,
            handleSelect,
            selectValue: value,
        };
    },
});
</script>
