import Swal from "sweetalert2";

export default {
    show({ title = "Default Title", text = "Default text", icon = "info" }) {
        Swal.fire({
            title,
            text,
            icon,
            confirmButtonClass: "btn btn-primary w-xs mt-2",
            buttonsStyling: false,
            showCloseButton: true,
        });
    },

    success({
        title = "Success!",
        text = "Operation completed successfully!",
    }) {
        Swal.fire({
            html: `
                <div class="mt-3">
                    <lord-icon 
                        src="https://cdn.lordicon.com/lupuorrc.json" 
                        trigger="loop" colors="primary:#0ab39c,secondary:#405189" 
                        style="width: 120px; height: 120px;">
                    </lord-icon>
                    <div class="mt-4 pt-2 fs-15">
                        <h4>${title}</h4>
                        <p class="text-muted mx-4 mb-0">${text}.</p>
                    </div>
                </div>`,
        });
    },

    error({ title = "Oops...!", text = "Something went wrong. Please try again!" }) {
        Swal.fire({
            html: `
            <div class="mt-3">
                <lord-icon 
                    src="https://cdn.lordicon.com/tdrtiskw.json" 
                    trigger="loop" 
                    colors="primary:#f06548,secondary:#f7b84b" 
                    style="width:120px;height:120px">
                </lord-icon>
                <div class="mt-4 pt-2 fs-15">
                    <h4>${title}</h4>
                    <p class="text-muted mx-4 mb-0">${text}</p>
                </div>
            </div>`,
        });
    },
};
