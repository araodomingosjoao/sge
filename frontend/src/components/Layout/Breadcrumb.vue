<template>
    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
        <h4 class="mb-sm-0">{{ title }}</h4>

        <div class="page-title-right">
            <ol class="breadcrumb m-0">
                <li v-for="(item, index) in breadcrumbItems" :key="index" class="breadcrumb-item">
                    <a v-if="!item.active" :href="item.link">{{ item.label }}</a>
                    <span v-else>{{ item.label }}</span>
                </li>
            </ol>
        </div>
    </div>
</template>

<script>
export default {
    name: 'Breadcrumb',
    props: {
        title: {
            type: String,
            required: true
        },
        breadcrumbItems: {
            type: Array,
            required: true
        }
    }
};
</script>
