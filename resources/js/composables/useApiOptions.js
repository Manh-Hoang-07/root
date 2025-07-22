import { ref, onMounted } from 'vue';

export default function useApiOptions(endpoint) {
  const options = ref([]);
  const loading = ref(false);
  const error = ref(null);

  const fetchOptions = async () => {
    loading.value = true;
    error.value = null;
    try {
      const res = await fetch(endpoint);
      const json = await res.json();
      
      // Xử lý dữ liệu enum trả về
      if (typeof json === 'object' && !Array.isArray(json)) {
        // Nếu là object (key-value pairs), chuyển đổi thành mảng options
        options.value = Object.entries(json).map(([value, name]) => ({
          name,
          value: parseInt(value, 10) || value,
        }));
      } else if (Array.isArray(json)) {
        // Nếu là mảng, đảm bảo mỗi phần tử có name và value
        options.value = json.map(item => ({
          name: item.name ?? item.label ?? String(item.value),
          value: item.value ?? item.id ?? item,
        }));
      } else {
        options.value = [];
      }
    } catch (e) {
      console.error('Error fetching options:', e);
      error.value = e;
    } finally {
      loading.value = false;
    }
  };

  onMounted(fetchOptions);

  return { options, loading, error, fetchOptions };
} 