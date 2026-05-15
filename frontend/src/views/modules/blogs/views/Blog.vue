<template>
  <div class="blogs-page">

    <!-- FILTER -->
    <div class="filter-box">

      <button
        class="btn-create"
        @click="goCreate"
      >
        + Thêm mới
      </button>

      <input
        v-model="keyword"
        type="text"
        placeholder="Tìm theo tiêu đề, Từ khóa"
        class="form-control"
        @keyup.enter="loadData"
      />

      <!-- STATUS -->
      <select
        v-model="status"
        class="form-control status-filter"
      >
        <option value="">
          Tất cả trạng thái
        </option>

        <option value="1">
          Hoạt động
        </option>

        <option value="0">
          Ngừng hoạt động
        </option>
      </select>

      <button
        class="btn-search"
        @click="loadData"
      >
        Tìm kiếm
      </button>

    </div>

    <!-- TABLE -->
    <div class="table-wrapper">

      <table class="table">

        <thead>
          <tr>
            <th width="100">ID</th>
            <th width="250">Ảnh đại diện</th>
            <th width="200">Tiêu đề</th>
            <th width="180">Từ khóa</th>
            <th width="150">Trạng thái</th>
            <th width="220">Thao tác</th>
          </tr>
        </thead>

        <tbody>

          <tr v-if="loading">
            <td colspan="6" class="text-center-align">
              Đang tải...
            </td>
          </tr>

          <tr v-else-if="!blogs.length">
            <td colspan="6" class="text-center-align">
              Không có dữ liệu
            </td>
          </tr>

          <tr
            v-for="item in blogs"
            :key="item.ID"
          >

            <td class="id">
              {{ item.ID }}
            </td>

            <td>
              <img
                v-if="item.BannerImage"
                :src="item.BannerImage"
                class="banner"
              />
            </td>

            <td
              class="title"
              @click="view(item.ID)"
            >
              {{ item.Title }}
            </td>

            <td>
              {{ item.Tags }}
            </td>

            <td>
              <span
                class="status-badge"
                :class="Number(item.Status) === 1
                  ? 'active'
                  : 'inactive'"
              >
                {{
                  Number(item.Status) === 1
                    ? 'Hoạt động'
                    : 'Ngừng hoạt động'
                }}
              </span>
            </td>

            <td>

              <div class="action-grid">

                <button
                  class="btn-view"
                  @click="view(item.ID)"
                >
                  Chi tiết
                </button>

                <button
                  class="btn-edit"
                  @click="edit(item.ID)"
                >
                  Sửa
                </button>

                <button
                  class="btn-delete"
                  @click="remove(item.ID)"
                >
                  Xóa
                </button>

              </div>

            </td>

          </tr>

        </tbody>

      </table>

    </div>

    <!-- POPUP DELETE -->
    <div
      v-if="showDeletePopup"
      class="popup-overlay"
    >
      <div class="popup-box">

        <div class="popup-header">
          Xác nhận xóa?
        </div>

        <div class="popup-body">
          Bạn có chắc chắn muốn xóa bài viết này không?
        </div>

        <div class="popup-footer">

          <button
            class="btn-popup-delete"
            @click="confirmDelete"
          >
            Xóa
          </button>

          <button
            class="btn-popup-cancel"
            @click="cancelDelete"
          >
            Hủy bỏ
          </button>

        </div>

      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRouter } from "vue-router";
import { useBlogStore } from "../provider/store";
import { useToast } from "vue-toastification";

const router = useRouter();
const toast = useToast();
const blogStore = useBlogStore();

const status = ref("1");
const blogs = ref([]);
const loading = ref(false);
const keyword = ref("");

/* POPUP DELETE */
const showDeletePopup = ref(false);
const deleteId = ref(null);

const loadData = async () => {

  try {

    loading.value = true;

    await blogStore.fetchList({
      keyword: keyword.value,
      Status: status.value,
      isAdmin: 1
    });

    blogs.value = blogStore.list || [];

  } catch (e) {

    console.error(e);

    blogs.value = [];

    toast.error(
      e?.response?.data?.message ||
      "Không tải được danh sách blog"
    );

  } finally {

    loading.value = false;
  }
};

const goCreate = () => {
  router.push("/blogs/create");
};

const view = (id) => {
  router.push(`/blogs/detail/${id}`);
};

const edit = (id) => {
  router.push(`/blogs/edit/${id}`);
};

/* MỞ POPUP */
const remove = (id) => {
  deleteId.value = id;
  showDeletePopup.value = true;
};

/* XÁC NHẬN XÓA */
const confirmDelete = async () => {

  try {

    await blogStore.deleteBlog(deleteId.value);

    toast.success("Xóa blog thành công.");

    showDeletePopup.value = false;

    await loadData();

  } catch (e) {

    console.error(e);

    toast.error("Xóa blog thất bại.");

  }
};

/* HỦY */
const cancelDelete = () => {
  showDeletePopup.value = false;
  deleteId.value = null;
};

onMounted(loadData);
</script>

<style scoped>
.blogs-page {
  padding: 24px;
  background: #f4f6f9;
  min-height: 100vh;
}

.filter-box {
  display: flex;
  align-items: center;
  gap: 12px;

  background: #fff;
  padding: 16px;
  border-radius: 12px;
  margin-bottom: 20px;

  box-shadow: 0 2px 6px rgba(0,0,0,0.05);
}

.form-control {
  height: 40px;
  padding: 0 12px;
  border-radius: 8px;
  border: 1px solid #dcdfe6;
  min-width: 260px;
}

.btn-create {
  background: #00a65a;
  color: #fff;
  padding: 10px 18px;
  border-radius: 8px;
  border: none;
  cursor: pointer;
}

.btn-search {
  background: #3c8dbc;
  color: #fff;
  padding: 10px 18px;
  border-radius: 8px;
  border: none;
  cursor: pointer;
}

.table-wrapper {
  background: #fff;
  border-radius: 14px;
  overflow: hidden;

  box-shadow: 0 4px 12px rgba(0,0,0,0.06);
}

.table {
  width: 100%;
  border-collapse: collapse;
  table-layout: fixed;
}

.table th {
  background: #f8fafc;
  padding: 14px;
  font-weight: 600;
  border-bottom: 1px solid #e5e7eb;
  color: #333;
  text-align: center;
  vertical-align: middle;
}

.table td {
  padding: 12px;
  border-bottom: 1px solid #f0f0f0;
  color: #333;
  text-align: center;
  vertical-align: middle;
}

.table tbody tr:hover {
  background: #f9fbfd;
}

.banner {
  width: 120px;
  height: 70px;
  object-fit: cover;
  border-radius: 8px;
}

.id {
  font-weight: 700;
}

.title {
  cursor: pointer;
  color: #3c8dbc;
  font-weight: 600;
}

.action-grid {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  flex-wrap: nowrap;
}

/* BUTTON CHUNG */
.btn-view,
.btn-edit,
.btn-delete {
  padding: 6px 12px;
  border: none;
  border-radius: 6px;
  cursor: pointer;

  color: #fff;
  font-size: 13px;
  font-weight: 500;

  white-space: nowrap;
}

/* CHI TIẾT */
.btn-view {
  background: #4999df;
}

.btn-view:hover {
  background: #5a6268;
}

/* SỬA */
.btn-edit {
  background: #ffc107;
  color: #000;
}

.btn-edit:hover {
  background: #e0a800;
}

/* XÓA */
.btn-delete {
  background: #e74c3c;
}

.btn-delete:hover {
  background: #c0392b;
}

.text-center-align {
  text-align: center;
  padding: 30px;
  color: #666;
}

.status-filter {
  min-width: 180px;
}

.status-badge {
  padding: 6px 12px;
  border-radius: 999px;
  font-size: 13px;
  font-weight: 600;
}

.status-badge.active {
  background: #d1fae5;
  color: #065f46;
}

.status-badge.inactive {
  background: #fee2e2;
  color: #991b1b;
}

/* OVERLAY */
.popup-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.45);

  display: flex;
  align-items: flex-start;
  justify-content: center;

  padding-top: 100px;

  z-index: 9999;
}

/* BOX */
.popup-box {
  width: 340px;
  background: #fff;
  border-radius: 4px;
  overflow: hidden;

  animation: popupFade .2s ease;
}

/* HEADER */
.popup-header {
  padding: 18px 20px;
  font-size: 16px;
  font-weight: 700;
  color: #444;
  border-bottom: 1px solid #eee;
}

/* BODY */
.popup-body {
  padding: 22px 20px;
  color: #444;
  font-size: 14px;
}

/* FOOTER */
.popup-footer {
  display: flex;
  justify-content: flex-end;
  gap: 10px;

  padding: 14px 20px;
  border-top: 1px solid #eee;
}

/* BUTTON DELETE */
.btn-popup-delete {
  min-width: 72px;
  height: 34px;

  border: none;
  border-radius: 6px;

  background: #e74c3c;
  color: #fff;

  cursor: pointer;
  font-weight: 600;
  font-size: 13px;
}

/* BUTTON CANCEL */
.btn-popup-cancel {
  min-width: 72px;
  height: 34px;

  border: 1px solid #dcdfe6;
  border-radius: 6px;

  background: #fff;
  color: #666;

  cursor: pointer;
  font-size: 13px;
  font-weight: 600;
}

.btn-popup-cancel:hover {
  background: #f5f5f5;
}

@keyframes popupFade {
  from {
    transform: scale(.95);
    opacity: 0;
  }

  to {
    transform: scale(1);
    opacity: 1;
  }
}
</style>