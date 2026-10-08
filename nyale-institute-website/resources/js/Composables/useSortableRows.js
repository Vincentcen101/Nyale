import { ref, watch, onMounted, onBeforeUnmount } from 'vue';
import { router } from '@inertiajs/vue3';
import Sortable from 'sortablejs';

/**
 * Drag-and-drop reordering for a dashboard table.
 * Put `ref="tbody"` on the <tbody>, `:data-id` on each row and a `.drag-handle`
 * element inside each row. The new order is sent to `reorderUrl` as { ids: [...] }.
 */
export function useSortableRows(source, reorderUrl) {
    // Local copy of the list so it can be reordered instantly.
    const rows = ref([...source()]);
    watch(source, (value) => { rows.value = [...value]; });

    const tbody = ref(null);
    let sortable = null;

    onMounted(() => {
        sortable = Sortable.create(tbody.value, {
            handle: '.drag-handle',
            draggable: 'tr[data-id]',
            animation: 150,
            ghostClass: 'opacity-40',
            onEnd: ({ item, from, oldIndex, newIndex, oldDraggableIndex, newDraggableIndex }) => {
                if (oldIndex === newIndex) return;
                // Put the row back where it was and let Vue re-render from the reordered array.
                from.removeChild(item);
                from.insertBefore(item, from.children[oldIndex] ?? null);

                const [moved] = rows.value.splice(oldDraggableIndex, 1);
                rows.value.splice(newDraggableIndex, 0, moved);

                router.patch(reorderUrl, { ids: rows.value.map((row) => row.id) }, {
                    preserveScroll: true,
                    preserveState: true,
                });
            },
        });
    });

    onBeforeUnmount(() => sortable?.destroy());

    return { rows, tbody };
}
