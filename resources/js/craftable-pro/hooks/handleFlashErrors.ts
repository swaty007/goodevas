import { computed, watch } from "vue";
import { useToast } from "@brackets/vue-toastification";
import { usePage } from "@inertiajs/vue3";
const toast = useToast();

export const handleFlashErrors = () => {
    const message_error = computed(() => usePage().props);
    watch(message_error, (val) => {
        if (val.flash?.message_error) {
            try {
                const json = JSON.parse(val.flash?.message_error);
                if (json.message) {
                    toast.error(json.message);
                } else {
                    throw new Error();
                }
            } catch (error) {
                toast.error(val.flash?.message_error);
            }
        }
    });
}
