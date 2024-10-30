<template>
    <footer class="footer">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    &copy; {{ currentYear }} {{ companyName }}.
                </div>
                <div class="col-sm-6">
                    <div class="text-sm-end d-none d-sm-block">
                        {{ designText }}
                    </div>
                </div>
            </div>
        </div>
    </footer>
</template>

<script>
export default {
    name: 'Footer',
    props: {
        companyName: {
            type: String,
            default: 'Velzon'
        },
        designText: {
            type: String,
            default: 'Design & Develop by Themesbrand'
        }
    },
    data() {
        return {
            currentYear: new Date().getFullYear()
        };
    }
};
</script>
