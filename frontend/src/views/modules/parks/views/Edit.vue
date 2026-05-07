<template>
    <div class="form-page">
        <div class="action-bar">
            <div class="left">
                <span class="back-icon" @click="goBack">
                    <i class="arrow"></i>
                </span>
                <span class="title">Cập nhật</span>
            </div>
        </div>
        <div class="action-bar">
        <div class="right">
            <button class="btn-submit" @click="handleSubmit">
            Lưu lại
            </button>
        </div>
        </div>

        <!-- FORM CONTENT -->
        <div class="form-card">

        <!-- ROW 1 -->
        <div class="grid-2">

            <div class="form-group">
                <label>Tên khu vui chơi</label>
                <input v-model="form.ParkName" />
            </div>

            <div class="form-group">
            <label>Địa điểm</label>
            <input v-model="form.Location" />
            </div>

        </div>

        <!-- ROW 2 -->
        <div class="grid-2">
            <div class="form-group">
            <label>Giờ mở cửa</label>
            <el-time-picker
                v-model="form.OpenTime"
                placeholder="Giờ mở cửa"
                format="HH:mm"
                value-format="HH:mm"
                style="width: 100%"
            />
            </div>

            <div class="form-group">
            <label>Giờ đóng cửa</label>
            <el-time-picker
                v-model="form.CloseTime"
                placeholder="Giờ đóng cửa"
                format="HH:mm"
                value-format="HH:mm"
                style="width: 100%"
            />
            </div>
        </div>

        <div class="grid-2">
           <div class="form-group">
                <label>Trạng thái</label>
                <select v-model="form.Status">
                    <option :value="1">Hoạt động</option>
                    <option :value="0">Ngừng hoạt động</option>
                </select>
            </div> 
        </div>
        <div class="form-group">
            <label>Mô tả</label>
            <textarea v-model="form.Description"></textarea>
        </div>
       
        <!-- IMAGE -->
        <div class="form-group">
            <label>Ảnh</label>

            <div class="file-display">
                <button class="btn-choose" @click="triggerFile">
                    Chọn tệp
                </button>

                <input
                    type="file"
                    ref="fileInput"
                    @change="handleFile"
                    hidden
                />

                <span>
                    {{ fileName || "Không có ảnh nào được chọn" }}
                </span>

                <button
                    v-if="preview"
                    class="btn-remove"
                    @click.stop="removeImage"
                >
                    Xóa ảnh
                </button>
            </div>

            <!-- PREVIEW -->
            <img v-if="preview" :src="preview" class="preview-large" />
        </div>

        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRouter, useRoute } from "vue-router";
import { useParkStore } from "../provider/store";

const router = useRouter();
const route = useRoute();
const store = useParkStore();

const form = ref({
  ParkName: "",
  Location: "",
  Description: "",
  OpenTime: "",
  CloseTime: "",
  Image: "",
  Status: 1,
  ImageFile: null
});

const preview = ref("");
const fileName = ref("");
const fileInput = ref(null);

// ✅ LOAD DATA DETAIL
onMounted(async () => {
  const id = route.params.id;

  if (!id) return;

  await store.getDetail(id);

  if (!store.detail) return;

  const data = store.detail;

  form.value.ParkName = data.ParkName || "";
  form.value.Location = data.Location || "";
  form.value.Description = data.Description || "";
  form.value.OpenTime = data.OpenTime?.slice(0,5) || "";
  form.value.CloseTime = data.CloseTime?.slice(0,5) || "";
  form.value.Status = data.Status;
  form.value.Image = data.Image;
  form.value.ImageFile = null;

  preview.value = getImage(data.Image);
});

// submit
const handleSubmit = async () => {
    if (!form.value.ParkName?.trim()) {
        alert("Vui lòng nhập tên khu vui chơi");
        return;
    }

    if (!form.value.Location?.trim()) {
        alert("Vui lòng nhập địa điểm");
        return;
    }

    if (!form.value.OpenTime || !form.value.CloseTime) {
        alert("Vui lòng chọn giờ");
        return;
    }

    if (form.value.OpenTime >= form.value.CloseTime) {
        alert("Giờ đóng cửa phải lớn hơn giờ mở cửa!");
        return;
    }

    const formData = new FormData();

    Object.keys(form.value).forEach(key => {
    if (key === "ImageFile" && !form.value.ImageFile) return;

    if (form.value[key] !== null && form.value[key] !== "") {
        formData.append(key, form.value[key]);
    }
    });

    try {
        await store.update(route.params.id, formData);
        router.push("/parks");
    } catch (e) {
        alert(e);
    }
};

// chọn file
const handleFile = (e) => {
    const file = e.target.files[0];
    if (!file) return;

    if (!file.type.startsWith("image/")) {
        alert("Chỉ được chọn ảnh");
        return;
    }

    if (file.size > 2 * 1024 * 1024) {
        alert("Ảnh tối đa 2MB");
        return;
    }

    preview.value = URL.createObjectURL(file);
    fileName.value = file.name;
    form.value.ImageFile = file;
};

// convert path
const getImage = (path) => {
  if (!path) return "";

  if (path.startsWith("http")) return path; // 👈 FIX

  return "http://localhost:8000" + path;
};

// remove ảnh
const removeImage = () => {
    preview.value = "";
    fileName.value = "";
    form.value.ImageFile = null;

    if (fileInput.value) {
        fileInput.value.value = "";
    }
};

const triggerFile = () => {
    fileInput.value.click();
};

const goBack = () => {
    router.push("/parks");
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
  margin-bottom: 15px;
}

.el-time-picker {
  width: 100%;
}

.form-group {
  display: flex;
  flex-direction: column;
}

.form-group label {
  margin-bottom: 6px;
  font-size: 14px;
  color: #555;
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

/* FILE */
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
  margin-left: 6px;
}

.btn-choose {
  margin-right: 6px;
}

.btn-remove {
  margin-left: auto;
  padding: 5px 10px;
  background: #e74c3c;
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
}

.preview-large {
  margin-top: 10px;
  width: 100%;
  max-height: 350px;
  object-fit: cover;
  border-radius: 8px;
}

.btn-submit {
  padding: 10px 18px;
  background: #00a65a;
  color: white;
  border: none;
  border-radius: 8px;
  cursor: pointer;
}

/* 🔥 mũi tên to + đậm hơn */
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

/* tạo mũi tên bằng CSS */
.arrow {
  display: block;
  width: 8px;
  height: 8px;
  border-left: 2px solid #fff;
  border-bottom: 2px solid #fff;

  transform: rotate(45deg); /* tạo mũi tên */
}

.back-icon:hover {
  background: #ddd;
}

/* 🔥 chữ đậm + rõ hơn */
.title {
  font-size: 22px;      /* 👈 to hơn */
  font-weight: 700;     /* 👈 đậm hẳn */
  color: #454242;
}

.left {
  display: flex;
  align-items: center;
  gap: 10px; /* khoảng cách giữa icon và chữ */
}
</style>