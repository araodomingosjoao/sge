import { ref, onMounted } from "vue";
import { useUserStore } from "@/stores/userStore";
import { useSetupStore } from '@/stores/setupStore'
import { useLocationData } from "../shared/useLocationData";
import axios from "@/plugins/axios";

export function useSchoolInfoStep() {
    const userStore = useUserStore();
    const setupStore = useSetupStore();

    const formData = ref({
        school_name: "",
        type_education_id: "",
        category_id: "",
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
            const { data } = await axios.get("/options");
            options.value = data.data;
        } catch (error) {
            console.error("Erro ao carregar opções:", error);
        }
    };

    const initializeFormData = () => {
        const { user } = userStore;
        if (user?.school) {
            formData.value.school_name = user.school.school_name || "";
            formData.value.type_education_id =
                user.school.type_education_id || "";
            formData.value.category_id = user.school.category_id || "";
            formData.value.registration_number =
                user.school.registration_number || "";
            formData.value.founded_year = user.school.founded_year || "";
            formData.value.website = user.school.website || "";
            formData.value.school_phone = user.school.phone || "";
            formData.value.school_email = user.school.email || "";
            formData.value.first_name = user.first_name || "";
            formData.value.last_name = user.last_name || "";
            formData.value.email = user.email || "";
            formData.value.phone = user.phone || "";
            formData.value.state = user.school.state || "";
            formData.value.city = user.school.city || "";
            formData.value.address = user.school.address || "";

            if (formData.value.state) {
                handleProvinceChange();
            }
        }
    };

    const loadSavedData = async () => {
        const savedData = await setupStore.getStepData('school_info')
        if (savedData) {
            formData.value = { ...formData.value, ...savedData }
            if (formData.value.state) {
                handleProvinceChange()
            }
        } else {
            initializeFormData()
        }
    };

    const handleSubmit = async () => {
        try {
            await setupStore.saveStepData("school_info", formData.value);
            await setupStore.moveToNextStep();
            return true;
        } catch (error) {
            console.error("Erro ao salvar:", error);
            return false;
        }
    };

    onMounted(() => {
        fetchOptions();
        loadSavedData();
    });

    return {
        formData,
        options,
        provinces,
        municipalities,
        handleProvinceChange,
        handleSubmit,
        loadSavedData,
    };
}
