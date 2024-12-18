import { ref, onMounted } from "vue";
import { useUserStore } from "@/stores/userStore";
import { useSetupStore } from '@/stores/setupStore'
import { useLocationData } from "../shared/useLocationData";
import axios from "@/plugins/axios";

export function useSchoolInfoStep() {
    const userStore = useUserStore();
    const setupStore = useSetupStore();

    const formData = ref({
        id: "",
        school_name: "",
        type_education_id: null,
        category_id: null,
        registration_number: "",
        founded_year: "",
        website: "",
        school_phone: "",
        school_email: "",
        first_name: "",
        last_name: "",
        email: "",
        phone: "",
        state: "",
        city: "",
        address: "",
    });

    const { provinces, municipalities, handleProvinceChange } =
        useLocationData(formData);

    const options = ref({
        optionsCategory: [],
        optionsTypeEducation: [],
    });

    const fetchOptions = async () => {
        try {
            setupStore.setLoading(true)
            const { data } = await axios.get("/options");
            options.value = data.data;
        } catch (error) {
            console.error("Erro ao carregar opções:", error);
        } finally {
            setupStore.setLoading(false)
        }
    };

    const initializeFormData = () => {
        const { user } = userStore
        if (!user?.school) return

        Object.entries(formData.value).forEach(([key, _]) => {
            if (key in user.school) {
                formData.value[key] = user.school[key] || ''
            } else if (key in user) {
                formData.value[key] = user[key] || ''
            }
        })

        if (formData.value.state) handleProvinceChange()
    }

    const loadSavedData = async () => {
        console.log('teste: ');
        
        try {
            setupStore.setLoading(true)
            const savedData = await setupStore.getStepData('school_info')
            if (savedData) {
                formData.value = { ...formData.value, ...savedData }
                if (formData.value.state) {
                    handleProvinceChange()
                }
            } else {
                initializeFormData()
            }
        } finally {
            setupStore.setLoading(false)
        }
    };

    const handleSubmit = async () => {
        try {
            setupStore.setLoading(true)
            formData.value.state = provinces.value.find(province => province.id === parseInt(formData.value.state))?.name || ""
            const { data } = await axios.post(`school/${formData.value.id}`, formData.value);
            const user = data.data
            userStore.setUser({user});
            
            await setupStore.saveStepData("school_info", formData.value);
            await setupStore.moveToNextStep();
            return true;
        } catch (error) {
            console.error("Erro ao salvar:", error);
            return false;
        } finally {
            setupStore.setLoading(false)
        }
    };

    const initialize = async () => {
        setupStore.setLoading(true);
        try {
            await Promise.all([
                fetchOptions(),
                loadSavedData()
            ]);
        } finally {
            setupStore.setLoading(false);
        }
    };

    return {
        formData,
        options,
        provinces,
        municipalities,
        handleProvinceChange,
        handleSubmit,
        initialize
    };
}
