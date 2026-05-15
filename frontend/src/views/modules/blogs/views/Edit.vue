<template>
  <div class="form-page">

    <!-- HEADER -->
    <div class="action-bar">
      <div class="left">

        <span
          class="back-icon"
          @click="goBack"
        >
          <i class="arrow"></i>
        </span>

        <span class="title">
          Cập nhật Blog
        </span>

      </div>
    </div>

    <!-- ACTION -->
    <div class="action-bar">
      <div class="right">

        <button
          class="btn-submit"
          @click="update"
          :disabled="loading"
        >
          {{ loading ? 'Đang lưu...' : 'Lưu lại' }}
        </button>

      </div>
    </div>

    <!-- FORM -->
    <div class="form-card">

      <!-- TITLE -->
      <div class="form-group">
        <label>Tiêu đề</label>

        <input
          v-model="form.Title"
          type="text"
        />
      </div>

      <!-- SUMMARY -->
      <div class="form-group">
        <label>Mô tả ngắn</label>

        <textarea
          v-model="form.Summary"
        />
      </div>

      <!-- CONTENT -->
      <div class="form-group">
        <label>Nội dung</label>

        <textarea
          v-model="form.Content"
          class="content"
        />
      </div>

      <!-- GRID -->
      <div class="grid-2">

        <!-- TAGS -->
        <div class="form-group">
          <label>Từ khóa</label>

          <input
            v-model="form.Tags"
            type="text"
          />
        </div>

        <!-- PARK -->
        <div class="form-group">
          <label>Khu vui chơi</label>

          <select v-model="form.ParkID">

            <option value="">
              -- Chọn khu vui chơi --
            </option>

            <option
              v-for="park in parks"
              :key="park.ID"
              :value="park.ID"
            >
              {{ park.ParkName }}
            </option>

          </select>
        </div>

      </div>

      <!-- STATUS -->
      <div class="form-group">
        <label>Trạng thái</label>

        <select v-model.number="form.Status">

          <option :value="1">
            Hoạt động
          </option>

          <option :value="0">
            Ngừng hoạt động
          </option>

        </select>
      </div>

      <!-- BANNER -->
      <div class="form-group">
        <label>Ảnh đại diện</label>

        <div class="file-display">

          <button
            type="button"
            class="btn-choose"
            @click="triggerFile"
          >
            Chọn tệp
          </button>

          <input
            type="file"
            ref="fileInput"
            accept="image/*"
            hidden
            @change="handleFile"
          />

          <span>
            {{ fileName || "Không có ảnh nào được chọn" }}
          </span>

          <button
            type="button"
            v-if="fileName"
            class="btn-remove"
            @click.stop="removeImage"
          >
            Xóa ảnh
          </button>

        </div>

        <!-- PREVIEW -->
        <img
          v-if="preview"
          :src="preview"
          class="preview-large"
        />
      </div>
    </div>
  </div>
</template>

<script setup>
import {
  reactive,
  ref,
  onMounted
} from "vue";

import {
  useRouter,
  useRoute
} from "vue-router";

import { useBlogStore } from "../provider/store";
import { useParkStore } from "@/views/modules/parks/provider/store";
import { useToast } from "vue-toastification";

const toast = useToast();
const parkStore = useParkStore();

const parks = ref([]);

const router = useRouter();

const route = useRoute();

const blogStore = useBlogStore();

const loading = ref(false);

const preview = ref("");

const fileName = ref("");

const fileInput = ref(null);

const newImage = ref(null);

async function loadParks() {

  try {

    await parkStore.getList();

    parks.value = parkStore.items || [];

  } catch (e) {

    console.error(e);
  }
}

const form = reactive({
  Title: '',
  BannerImage: '',
  Summary: '',
  Content: '',
  Tags: '',
  ParkID: '',
  Status: 1,
});

onMounted(async () => {

  await loadParks();

  await loadDetail();
});

const loadDetail = async () => {

  try {

    loading.value = true;

    const res = await blogStore.getDetail(
      route.params.id
    );

    form.Title = res.data.Title || '';

    form.BannerImage =
      res.data.BannerImage || '';

    form.Summary =
      res.data.Summary || '';

    form.Content =
      res.data.Content || '';

    form.Tags =
      res.data.Tags || '';

    form.ParkID =
      res.data.ParkID || '';

    form.Status =
      Number(res.data.Status);

    /*
      HIỂN THỊ ẢNH CŨ
    */
    preview.value =
      res.data.BannerImage || '';

    if (res.data.BannerImage) {

      fileName.value =
        res.data.BannerImage
          .split('/')
          .pop();
    }

  } catch (e) {

    console.error(e);

    toast.error("Không load được dữ liệu.");

  } finally {

    loading.value = false;
  }
};

/* CHỌN FILE */
const triggerFile = () => {

  fileInput.value.click();
};

/* HANDLE FILE */
const handleFile = (e) => {

  const file = e.target.files[0];

  if (!file) {
    return;
  }

  if (!file.type.startsWith("image/")) {

    toast.error("Chỉ được chọn ảnh.");

    return;
  }

  if (file.size > 2 * 1024 * 1024) {

    toast.error("Ảnh tối đa 2MB");

    return;
  }

  preview.value = URL.createObjectURL(file);

  fileName.value = file.name;

  newImage.value = file;
};

/* XÓA ẢNH */
const removeImage = () => {

  preview.value = "";

  fileName.value = "";

  form.BannerImage = "";

  newImage.value = null;

  if (fileInput.value) {

    fileInput.value.value = "";
  }
};

const update = async () => {

  if (!form.Title) {
    return toast.error("Tiêu đề không được để trống.");
  }

  if (!form.Content) {
    return toast.error("Nội dung không được để trống.");
  }

  if (!form.ParkID) {
    return toast.error("Vui lòng chọn khu vui chơi");
  }

  try {

    loading.value = true;

    const formData = new FormData();

    formData.append(
      "Title",
      form.Title
    );

    formData.append(
      "Summary",
      form.Summary
    );

    formData.append(
      "Content",
      form.Content
    );

    formData.append(
      "Tags",
      form.Tags
    );

    formData.append(
      "ParkID",
      form.ParkID
    );

    formData.append(
      "Status",
      form.Status
    );

    /*
      NẾU CHỌN ẢNH MỚI
    */
    if (newImage.value) {

      formData.set(
        "BannerImage",
        newImage.value
      );
    }

    await blogStore.updateBlog(
      route.params.id,
      formData
    );

    toast.success("Cập nhật thành công.")

    router.push("/blogs");

  } catch (e) {

    console.error(e);

    toast.error("Cập nhật thất bại.")

  } finally {

    loading.value = false;
  }
};

const goBack = () => {

  router.push("/blogs");
};
</script>

<style scoped>
.form-page {
  padding: 20px 30px;
  background: #f4f6f9;
  min-height: 100vh;
}

.action-bar {
  display: flex;
  justify-content: space-between;
  align-items: center;

  background: #fff;
  padding: 15px 20px;
  border-radius: 12px;

  margin-bottom: 20px;

  box-shadow: 0 2px 6px rgba(0,0,0,0.06);
}

.left {
  display: flex;
  align-items: center;
  gap: 10px;
}

.back-icon {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  background: #333;

  display: flex;
  align-items: center;
  justify-content: center;

  cursor: pointer;
}

.arrow {
  width: 8px;
  height: 8px;

  border-left: 2px solid #fff;
  border-bottom: 2px solid #fff;

  transform: rotate(45deg);
}

.back-icon:hover {
  background: #555;
}

.title {
  font-size: 22px;
  font-weight: 700;
  color: #454242;
}

.form-card {
  background: #fff;
  padding: 25px;
  border-radius: 12px;

  box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

.grid-2 {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
}

.form-group {
  display: flex;
  flex-direction: column;
  margin-bottom: 20px;
}

.form-group label {
  margin-bottom: 8px;
  color: #333;
  font-weight: 600;
}

input,
textarea,
select {
  padding: 10px 12px;
  border-radius: 8px;
  border: 1px solid #dcdfe6;
  font-size: 14px;
}

textarea {
  min-height: 120px;
}

.content {
  min-height: 320px;
}

.banner-box {
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.banner-preview {
  width: 100%;
  max-height: 400px;
  object-fit: contain;

  border-radius: 12px;
  border: 1px solid #ddd;
}

.banner-actions {
  display: flex;
  gap: 12px;
}

.btn-upload {
  padding: 10px 16px;
  border: none;
  border-radius: 8px;

  background: #3498db;
  color: white;

  cursor: pointer;
}

.btn-remove {
  padding: 5px 10px;
  background: #e74c3c;
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;

  margin-left: auto;
}

.btn-remove:hover {
  background: #c0392b;
}

.btn-submit {
  padding: 10px 18px;
  background: #00a65a;
  color: white;

  border: none;
  border-radius: 8px;

  cursor: pointer;
}

.btn-submit:hover {
  background: #008d4c;
}

.btn-submit:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

@media (max-width: 768px) {

  .grid-2 {
    grid-template-columns: 1fr;
  }

  .form-page {
    padding: 15px;
  }

  .banner-actions {
    flex-direction: column;
  }
}

.file-display {
  display: flex;
  align-items: center;

  border: 1px solid #dcdfe6;
  padding: 8px 12px;
  border-radius: 8px;
  background: #fff;
}

.file-display span {
  font-size: 14px;
  color: #333;
  flex: none;
}

.btn-choose {
  margin-right: 8px;
  padding: 8px 12px;
  border: 1px solid #ccc;
  border-radius: 6px;
  background: #f7f7f7;
  cursor: pointer;
}

.btn-choose:hover {
  background: #eaeaea;
}

/* PREVIEW */
.preview-large {
  margin-top: 10px;

  width: 100%;
  max-height: 350px;

  object-fit: cover;

  border-radius: 8px;
}
</style>