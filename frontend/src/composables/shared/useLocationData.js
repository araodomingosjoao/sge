import { ref, watch } from "vue";
import { PROVINCES, getMunicipalities } from "@/utils/locationData";

export function useLocationData(formData) {
    const provinces = ref(PROVINCES);
    const municipalities = ref([]);

    const formatMunicipalities = (towns) => {
        return towns.map((name) => ({
            id: name,
            name: name,
        }));
    };

    const handleProvinceChange = () => {
        const provinceId = formData.value.state;
        formData.value.city = "";

        if (provinceId) {
            const municipalitiesList = getMunicipalities(provinceId);
            municipalities.value = formatMunicipalities(municipalitiesList);
        } else {
            municipalities.value = [];
        }
    };

    watch(
        () => formData.value.state,
        () => {
            handleProvinceChange();
        }
    );

    return {
        provinces,
        municipalities,
        handleProvinceChange,
    };
}
