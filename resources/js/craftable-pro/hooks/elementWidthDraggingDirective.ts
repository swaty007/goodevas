import { ref } from 'vue';

const resizableDataStore = {};
export default {
    mounted(el: HTMLElement, binding) {
        const isDragging = ref(false);
        const startX = ref(0);
        const initialWidth = ref(0);

        el.classList.add('resizable')

        const storageKey = `resizable-elements-${window.location.pathname}`;
        if (!resizableDataStore[storageKey]) {
            resizableDataStore[storageKey] = JSON.parse(localStorage.getItem(storageKey) || '{}');
        }
        const savedData = resizableDataStore[storageKey]; // link to the storage
        const elementKey = el.innerText.replace(/\s+/g, '').toLowerCase();
        if (savedData[elementKey]) {
            el.style.width = `${savedData[elementKey]}px`;
            el.style.minWidth = `${savedData[elementKey]}px`;
        }
        const startDragging = (event) => {
            if (event.offsetX >= el.clientWidth - Math.max((el.clientWidth / 10), 10)) {
                isDragging.value = true;
                startX.value = event.clientX;
                initialWidth.value = el.clientWidth;

                document.addEventListener('mousemove', onDragging);
                document.addEventListener('mouseup', stopDragging);
            }
        };

        const onDragging = (event) => {
            if (isDragging.value) {
                const deltaX = event.clientX - startX.value;
                const newWidth = initialWidth.value + deltaX;
                el.style.minWidth = `${newWidth}px`;
                el.style.width = `${newWidth}px`;
            }
        };

        const stopDragging = () => {
            isDragging.value = false;
            document.removeEventListener('mousemove', onDragging);
            document.removeEventListener('mouseup', stopDragging);

            savedData[elementKey] = el.clientWidth;
            localStorage.setItem(storageKey, JSON.stringify(savedData));
        };

        el.addEventListener('mousedown', startDragging);

        el._cleanup = () => {
            document.removeEventListener('mousemove', onDragging);
            document.removeEventListener('mouseup', stopDragging);
        };
    },
    unmounted(el: HTMLElement) {
        if (el._cleanup) {
            el._cleanup();
        }
    }
};
