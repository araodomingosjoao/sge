<template>
    <div class="form-check">
        <input
            type="checkbox"
            :id="id"
            class="form-check-input"
            :value="value"
            v-model="checked"
            @change="$emit('change', checked)"
        />
        <label class="form-check-label" :for="id">
            <slot>{{ label }}</slot>
        </label>
    </div>
</template>

<script>
export default {
    name: 'AppFormCheckbox',
    props: {
        id: {
            type: String,
            required: true,
        },
        label: {
            type: String,
            default: '',
        },
        value: {
            type: Boolean,
            default: false,
        },
    },
    data() {
        return {
            checked: this.value,
        };
    },
    watch: {
        value(newValue) {
            this.checked = newValue;
        },
        checked(newValue) {
            this.$emit('input', newValue);
        },
    },
};
</script>

<style scoped>
.form-check {
    margin-bottom: 1rem;
}
</style>
