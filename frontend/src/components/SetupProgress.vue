<template>
  <div class="setup-progress card">
      <div class="card-body">
          <h5 class="card-title d-flex justify-content-between align-items-center mb-4">
              <span>
                  <i class="ri-settings-4-line align-middle me-1"></i>
                  Progresso
              </span>
              <span class="badge bg-primary">{{ percentage }}%</span>
          </h5>

          <!-- Barra de Progresso -->
          <div class="progress mb-4" style="height: 8px;">
              <div 
                  class="progress-bar bg-primary" 
                  role="progressbar" 
                  :style="{ width: `${percentage}%` }" 
                  :aria-valuenow="percentage" 
                  aria-valuemin="0" 
                  aria-valuemax="100"
              ></div>
          </div>

          <!-- Estatísticas -->
          <div class="setup-stats">
              <div class="d-flex justify-content-between align-items-center mb-2">
                  <span class="text-muted">Etapas Concluídas</span>
                  <span class="fw-bold">{{ completed }}/{{ total }}</span>
              </div>
              
              <div v-if="estimatedTime" class="d-flex justify-content-between align-items-center text-muted small">
                  <span>Tempo estimado restante</span>
                  <span>{{ estimatedTime }}</span>
              </div>
          </div>

          <!-- Status -->
          <div class="setup-status mt-4">
              <div class="setup-status-item mb-2 d-flex align-items-center">
                  <i class="ri-checkbox-circle-fill text-success me-2"></i>
                  <span>Etapas concluídas</span>
                  <span class="ms-auto">{{ completed }}</span>
              </div>
              
              <div class="setup-status-item mb-2 d-flex align-items-center">
                  <i class="ri-time-line text-warning me-2"></i>
                  <span>Etapas pendentes</span>
                  <span class="ms-auto">{{ total - completed }}</span>
              </div>

              <div v-if="skippedSteps" class="setup-status-item d-flex align-items-center">
                  <i class="ri-skip-forward-mini-line text-info me-2"></i>
                  <span>Etapas puladas</span>
                  <span class="ms-auto">{{ skippedSteps }}</span>
              </div>
          </div>

          <!-- Dica -->
          <div class="setup-tip mt-4 pt-3 border-top">
              <small class="text-muted">
                  <i class="ri-information-line me-1"></i>
                  Complete todas as etapas obrigatórias para começar a usar o sistema.
              </small>
          </div>
      </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  percentage: {
      type: Number,
      required: true,
      default: 0
  },
  completed: {
      type: Number,
      required: true,
      default: 0
  },
  total: {
      type: Number,
      required: true,
      default: 0
  },
  skippedSteps: {
      type: Number,
      default: 0
  },
  estimatedTime: {
      type: String,
      default: ''
  }
})

// Computed para validar o percentual
const validPercentage = computed(() => {
  const percent = Math.min(Math.max(props.percentage, 0), 100)
  return Math.round(percent)
})
</script>

<style scoped>
.setup-progress {
  position: sticky;
  top: 1rem;
}

.progress {
  background-color: #e9ecef;
  border-radius: 4px;
  overflow: hidden;
}

.progress-bar {
  transition: width 0.6s ease;
}

.setup-status-item {
  padding: 0.5rem 0;
  color: #6c757d;
}

.setup-tip {
  font-size: 0.875rem;
  line-height: 1.4;
}

/* Animação suave para mudanças de progresso */
.progress-bar {
  transition: width 0.6s ease;
}

/* Cores personalizadas para os ícones de status */
.text-success {
  color: #0ab39c !important;
}

.text-warning {
  color: #f7b84b !important;
}

.text-info {
  color: #299cdb !important;
}

/* Estilo para o badge */
.badge.bg-primary {
  font-size: 0.875rem;
  padding: 0.35em 0.65em;
}

/* Responsividade */
@media (max-width: 768px) {
  .setup-progress {
      position: relative;
      top: 0;
      margin-top: 1rem;
  }
}
</style>