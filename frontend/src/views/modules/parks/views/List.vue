<template>
  <div class="parks-page">
    <!-- SEARCH + FILTER -->
    <div class="filter-box">
      <button v-if="isAdmin" class="btn-create" @click="goCreate">
        + Thêm mới
      </button>

      <input
        type="text"
        placeholder="Tìm theo tên, địa điểm"
        class="form-control"
        @keyup.enter="loadData"
      />

      <select v-model="status" class="form-select">
        <option value="1">Hoạt động</option>
        <option value="0">Ngừng hoạt động</option>
        <option value="">Tất cả trạng thái</option>
      </select>

      <button class="btn-search" @click="loadData">
        Tìm kiếm
      </button>
    </div>

    <!-- TABLE -->
    <div class="table-wrapper">
      <table class="table">
        <thead>
          <tr>
            <th width="100">Ảnh</th>
            <th width="250">Tên khu vui chơi</th>
            <th width="200">Địa điểm</th>
            <th width="180">Giờ hoạt động</th>
            <th width="150">Trạng thái</th>
            <th width="150">Action</th>
          </tr>
        </thead>

        <tbody>
          <tr v-if="store.loading">
            <td colspan="6" class="text-center">Đang tải...</td>
          </tr>

          <tr v-else-if="!store.items.length">
            <td colspan="6" class="text-center">Không có dữ liệu</td>
          </tr>

          <tr v-for="item in store.items" :key="item.ID">
            
            <!-- IMAGE -->
            <td>
                <img
                    :src="getImage(item.Image)"
                    class="thumb"
                    @error="handleError"
                />
            </td>

            <!-- NAME -->
            <td class="name" @click="goDetail(item.ID)">
              {{ item.ParkName }}
            </td>

            <!-- LOCATION -->
            <td>{{ item.Location }}</td>

            <!-- TIME -->
            <td>
              {{ item.OpenTime?.slice(0,5) }} - {{ item.CloseTime?.slice(0,5) }}
            </td>

            <!-- STATUS -->
            <td>
              <span
                class="badge"
                :class="Number(item.Status) === 1 ? 'active' : 'inactive'"
                >
                {{ Number(item.Status) === 1 ? 'Hoạt động' : 'Ngừng hoạt động' }}
            </span>
            </td>

            <!-- ACTION -->
            <td>
              <template v-if="isAdmin">
                <button
                  class="btn-edit"
                  @click="goEdit(item)"
                >
                  Sửa
                </button>

                <button
                  class="btn-delete"
                  @click="handleDelete(item.ID)"
                >
                  Xóa
                </button>
              </template>
            </td>

          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from "vue";
import { useRouter } from "vue-router";
import { useParkStore } from "../provider/store";
import { useAuthStore } from "@/views/modules/auths/provider/store";
import { computed } from "vue";

const router = useRouter();
const authStore = useAuthStore();

const isAdmin = computed(() => {
  return (
    authStore.user?.role?.RoleName?.toLowerCase() === "admin"
  );
});

const isCustomer = computed(() => {
  return (
    authStore.user?.role?.RoleName?.toLowerCase() === "customer"
  );
});
const store = useParkStore();

const search = ref("");
const status = ref("1");
const getImage = (path) => {
  if (!path) return "";

  if (path.startsWith("http")) return path; // 👈 FIX

  return "http://localhost:8000" + path;
};

const handleError = (e) => {
  e.target.src = "/images/default.jpg";
};
const loadData = () => {
    store.getList({
        search: search.value,
        status: status.value === "" ? null : Number(status.value) 
  });
};

const goCreate = () => {
    router.push("/parks/create");
};

const goEdit = (item) => {
  console.log("ITEM:", item);
  console.log("EDIT ID:", item.ID);

  router.push(`/parks/edit/${item.ID}`);
};

const goDetail = (id) => {
    router.push(`/parks/${id}`);
};

const handleDelete = async (id) => {
    if (!confirm("Bạn có chắc muốn xóa?")) return;

    await store.delete(id);
    loadData();
};

onMounted(() => {
    loadData();
});
</script>

<style scoped>
.parks-page {
  padding: 20px;
  background: #f4f6f9;
  height: 100%;
  box-sizing: border-box;
}

/* BUTTON CREATE */
.btn-create {
  padding: 10px 16px;
  background: #00a65a;
  color: white;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 500;
}

.btn-create:hover {
  background: #008d4c;
}

/* FILTER BOX */
.filter-box {
  display: flex;
  gap: 10px;
  align-items: center;

  background: #fff;
  padding: 15px;
  border-radius: 12px;

  margin-bottom: 15px;
  box-shadow: 0 2px 6px rgba(0,0,0,0.06);
}

/* INPUT */
.form-control,
.form-select {
  padding: 10px 12px;
  border-radius: 8px;
  border: 1px solid #dcdfe6;
  min-width: 200px;
}

/* BUTTON SEARCH */
.btn-search {
  padding: 10px 16px;
  background: #3c8dbc;
  color: white;
  border: none;
  border-radius: 8px;
  cursor: pointer;
}

.btn-search:hover {
  background: #367fa9;
}

/* TABLE WRAPPER */
.table-wrapper {
  background: #fff;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

/* TABLE */
.table {
  width: 100%;
  border-collapse: collapse;
  table-layout: fixed;
}

/* HEADER TABLE */
.table th {
  background: #f8fafc;
  padding: 14px;
  font-weight: 600;
  border-bottom: 1px solid #e5e7eb;
  color: #333;
  text-align: center;
  vertical-align: middle;
}

/* BODY */
.table td {
  padding: 12px;
  border-bottom: 1px solid #f0f0f0;
  color: #333;
  text-align: center;
  vertical-align: middle; 
}

/* HOVER */
.table tr:hover {
  background: #f9fbfd;
}

/* IMAGE */
.thumb {
  width: 70px;
  height: 50px;
  object-fit: cover;
  border-radius: 6px;

  display: block;       
  margin: 0 auto;       
}

/* NAME */
.name {
  cursor: pointer;
  font-weight: 500;
  color: #3c8dbc;
  text-align: left;
}

/* BADGE */
.badge {
  display: inline-flex;
  justify-content: center;
  align-items: center;

  width: 130px;  
  height: 32px;

  border-radius: 20px;
  font-size: 13px;
  font-weight: 500;
}

.badge.active {
  background: #e6f7ee;
  color: #00a65a;
}

.badge.inactive {
  background: #e8d9ba;
  color: #bb6e16;
}

/* BUTTON */
.btn-edit {
  padding: 6px 10px;
  margin-right: 5px;
  background: #ffc107;
  border: none;
  border-radius: 6px;
  cursor: pointer;
}

.btn-delete {
  padding: 6px 10px;
  background: #dc3545;
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
}

/* ACTION CENTER */
.table td:last-child {
  text-align: center;
   white-space: nowrap;
}

/* FIX ROW HEIGHT ĐỀU */
.table tr {
  height: 70px;
}
</style>
