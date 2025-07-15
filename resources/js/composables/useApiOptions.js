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
      // Chuẩn hóa: nếu có data thì lấy data, không thì lấy luôn json
      const data = json.data || json;
      // Đảm bảo mỗi option có name và value
      options.value = Array.isArray(data)
        ? data.map(item => ({
            name: item.name ?? item.label ?? String(item.value),
            value: item.value ?? item.id ?? item,
          }))
        : [];
    } catch (e) {
      error.value = e;
    } finally {
      loading.value = false;
    }
  };

  onMounted(fetchOptions);

  return { options, loading, error, fetchOptions };
} 