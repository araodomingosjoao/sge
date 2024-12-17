import { defineRule, configure } from 'vee-validate';
import { required, email, min, max } from '@vee-validate/rules';

defineRule('required', required);
defineRule('email', email);
defineRule('min', min);
defineRule('max', max);

// Mensagens em português
configure({
    generateMessage: (context) => {
        const messages = {
            required: `${context.field} é obrigatório`,
            email: 'Email inválido',
            min: `${context.field} deve ter no mínimo ${context.rule.params} caracteres`,
            min: `${context.field} deve ter no maximo ${context.rule.params} caracteres`,
        };
        return messages[context.rule.name];
    }
});