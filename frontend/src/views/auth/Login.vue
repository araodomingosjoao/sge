<template>
    <div class="row g-0">
        <AppQuoteCarousel />
        <div class="col-lg-6">
            <div class="p-lg-5 p-4">
                <div>
                    <h5 class="text-primary">Bem-vindo de volta!</h5>
                    <p class="text-muted">Faça login para continuar no sistema.</p>
                </div>
                <div class="mt-4">
                    <AppForm @event:submit="onLogin">
                        <AppFormInput
                            label="Email"
                            id="email"
                            v-model="email"
                            rules="required|email"
                        />
                        <AppFormInput
                            label="Senha"
                            id="password-input"
                            type="password"
                            v-model="password"
                            rules="required|min:6|max:20"
                        >
                            <!-- Botão de exibir senha -->
                            <AppButton
                                type="button"
                                icon="ri-eye-fill"
                                btn-class="position-absolute end-0 top-0 text-decoration-none text-muted"
                            />
                        </AppFormInput>
                        <AppFormCheckbox
                            id="auth-remember-check"
                            label="Lembrar de mim"
                        />
                        <div class="mt-4">
                            <AppButton type="submit" btn-class="w-100 btn-success" :disabled="isLoading">{{ isLoading ? 'Entrando...' : 'Entrar'}}</AppButton>
                        </div>
                    </AppForm>
                </div>
                <div class="mt-5 text-center">
                    <p class="mb-0">Não tem uma conta? <router-link to="/signup" class="fw-semibold text-primary text-decoration-underline">Inscreva-se</router-link></p>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { ref } from 'vue';
import { useAuth } from '@/composables/useAuth';
import router from '../../router'
import Swal from '../../utils/swal';
import AppForm from '../../components/UI/AppForm.vue';
import AppFormInput from '../../components/UI/AppFormInput.vue';
import AppFormCheckbox from '../../components/UI/AppFormCheckbox.vue';
import AppButton from '../../components/UI/AppButton.vue';
import AppQuoteCarousel from '../../components/UI/AppQuoteCarousel.vue';

export default {
    name: 'Login',
    components: {
        AppForm,
        AppFormInput,
        AppFormCheckbox,
        AppButton,
        AppQuoteCarousel,
    },
    setup() {
        const email = ref('');
        const password = ref('');
        const isLoading = ref(false);
        const { login } = useAuth();

        const onLogin = async () => {
            isLoading.value = true
            try {
                await login(email.value, password.value);
                router.push({ name: 'Dashboard' });
            } catch (error) {
                Swal.error({
                    title: "Erro ao fazer login",
                    text: error.message
                });
            } finally {
                isLoading.value = false
            }
        };

        return {
            email,
            password,
            onLogin,
            isLoading
        };
    },
};
</script>
