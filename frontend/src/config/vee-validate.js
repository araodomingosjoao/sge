import { defineRule } from 'vee-validate';
import { all } from '@vee-validate/rules';


Object.entries(all).forEach(([name, rule]) => {
  defineRule(name, rule);
});
