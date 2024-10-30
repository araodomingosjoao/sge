<template>
    <div class="modal fade zoomIn show" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" @click="$emit('close')" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center">
                        <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop"
                                   colors="primary:#f7b84b,secondary:#f06548"
                                   style="width:100px;height:100px"></lord-icon>
                        <h4>Are you sure?</h4>
                        <p>Are you sure you want to remove this notification?</p>
                    </div>
                    <div class="d-flex justify-content-center mt-4">
                        <button type="button" class="btn btn-light" @click="$emit('close')">Close</button>
                        <button type="button" class="btn btn-danger" @click="$emit('confirm')">Yes, Delete It!</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "NotificationModal"
};
</script>

<style scoped>
</style>
