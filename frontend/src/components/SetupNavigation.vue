<template>
    <div class="setup-navigation">
        <div
            class="nav flex-column custom-nav nav-pills"
            role="tablist"
            aria-orientation="vertical"
        >
            <button
                v-for="step in steps"
                :key="step.key"
                class="nav-link"
                :class="{
                    active: currentStep === step.key,
                    done: completedSteps.includes(step.key),
                    disabled: !isStepAvailable(step),
                }"
                role="tab"
                @click="handleStepClick(step)"
            >
                <span class="step-title me-2">
                    <!-- Ícone de sucesso quando completado -->
                    <i
                        v-if="completedSteps.includes(step.key)"
                        class="ri-check-circle-fill step-icon me-2 text-success"
                    ></i>
                    <!-- Ícone de progresso quando atual -->
                    <i
                        v-else-if="currentStep === step.key"
                        class="ri-arrow-right-circle-fill step-icon me-2 text-primary"
                    ></i>
                    <!-- Ícone padrão para pendentes -->
                    <i v-else class="ri-circle-line step-icon me-2"></i>
                    {{ step.name }}
                </span>

                <!-- Descrição da etapa -->
                <small class="d-block text-muted mt-1" v-if="step.description">
                    {{ step.description }}
                </small>

                <!-- Badge para etapas opcionais -->
                <span
                    v-if="!step.required"
                    class="badge bg-light text-muted float-end mt-1"
                >
                    Opcional
                </span>
            </button>
        </div>
    </div>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
    steps: {
        type: Array,
        required: true,
    },
    currentStep: {
        type: String,
        required: true,
    },
    completedSteps: {
        type: Array,
        required: true,
    },
});

const emit = defineEmits(["step-click"]);

const isStepAvailable = (step) => {
    // Se a etapa já foi completada, está disponível
    if (props.completedSteps.includes(step.key)) return true;

    // Encontra o índice da etapa atual e da etapa que queremos verificar
    const currentIndex = props.steps.findIndex(
        (s) => s.key === props.currentStep
    );
    const stepIndex = props.steps.findIndex((s) => s.key === step.key);

    // Só permite acessar a etapa atual ou a próxima
    return stepIndex <= currentIndex + 1;
};

const handleStepClick = (step) => {
    if (isStepAvailable(step)) {
        emit("step-click", step.key);
    }
};
</script>

<style scoped>
.setup-navigation {
    border-right: 1px solid #eee;
    height: 100%;
}

.custom-nav .nav-link {
    position: relative;
    padding: 1rem 1.5rem;
    color: #495057;
    background-color: transparent;
    border: 0;
    border-radius: 0.375rem;
    margin-bottom: 0.5rem;
    text-align: left;
    width: 100%;
    transition: all 0.3s ease;
}

.custom-nav .nav-link:hover:not(.disabled) {
    background-color: #f8f9fa;
}

.custom-nav .nav-link.active {
    background-color: #405189;
    color: #fff;
}

.custom-nav .nav-link.active small {
    color: rgba(255, 255, 255, 0.75) !important;
}

.custom-nav .nav-link.done {
    background-color: #f8f9fa;
}

.custom-nav .nav-link.disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.step-icon {
    font-size: 1.25rem;
    vertical-align: middle;
}

.text-success {
    color: #0ab39c !important;
}

.text-primary {
    color: #405189 !important;
}

/* Efeito de linha indicadora */
.nav-link::before {
    content: "";
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 3px;
    background-color: #405189;
    transform: scaleY(0);
    transition: transform 0.3s ease;
}

.nav-link.active::before {
    transform: scaleY(1);
}

/* Responsividade */
@media (max-width: 991.98px) {
    .setup-navigation {
        border-right: none;
        border-bottom: 1px solid #eee;
        margin-bottom: 1.5rem;
    }
}
</style>
