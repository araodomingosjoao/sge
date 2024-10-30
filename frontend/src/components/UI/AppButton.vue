<template>
    <button
        :type="type"
        :class="['btn', btnClass]"
        @click="$emit('click')"
    >
        <i v-if="icon" :class="[icon, 'align-middle']"></i>
        <span v-if="$slots.default"><slot /></span>
    </button>
</template>

<script>
export default {
    name: 'AppButton',
    props: {
        type: {
            type: String,
            default: 'button'
        },
        btnClass: {
            type: String,
            default: 'btn-primary'
        },
        icon: {
            type: String,
            default: ''
        },
    },
};
</script>
