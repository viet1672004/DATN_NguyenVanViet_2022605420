<template>
    <div class="form-page">
        <div class="action-bar">
            <div class="left">
                <span class="back-icon" @click="goBack">
                    <i class="arrow"></i>
                </span>
                <span class="title">Thêm mới</span>
            </div>
        </div>
        <div class="action-bar">
        <div class="right">
            <button class="btn-submit" @click="handleSubmit">
            Lưu lại
            </button>
        </div>
        </div>

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

        <div class="form-group">
            <label>Mô tả</label>
            <textarea v-model="form.Description"></textarea>
        </div>
       
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
                        v-if="fileName"
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
import { useRouter } from "vue-router";
import { useParkStore } from "../provider/store";

onMounted(() => {
  console.log("CREATE PAGE LOADED");
});

const router = useRouter();
const store = useParkStore();

const form = ref({
  ParkName: "",
  Location: "",
  Description: "",
  OpenTime: "",
  CloseTime: "",
  Image: "",
  Status: 1,
  ImageFile: null // ✅ thêm
});

const preview = ref("");
const fileName = ref("");

// submit
const handleSubmit = async () => {
    // 🔥 bắt buộc các field
    if (!form.value.ParkName?.trim()) {
        alert("Vui lòng nhập tên khu vui chơi");
        return;
    }

    if (!form.value.Location?.trim()) {
        alert("Vui lòng nhập địa điểm");
        return;
    }

    if (!form.value.OpenTime) {
        alert("Vui lòng chọn giờ mở cửa");
        return;
    }

    if (!form.value.CloseTime) {
        alert("Vui lòng chọn giờ đóng cửa");
        return;
    }

    if (form.value.OpenTime >= form.value.CloseTime) {
        alert("Giờ đóng cửa phải lớn hơn giờ mở cửa!");
        return;
    }

    if (!form.value.Image && !form.value.ImageFile) {
        alert("Vui lòng chọn ảnh");
        return;
    }

    form.value.Status = 1;

    const formData = new FormData();

    Object.keys(form.value).forEach(key => {
        if (form.value[key] !== null && form.value[key] !== "") {
            formData.append(key, form.value[key]);
        }
    });

    try {
        await store.create(formData);
        router.push("/parks");
    } catch (e) {
        alert(e);
    }
};

// ✅ NEW handle file (input file)
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

const getImage = (path) => {
  if (!path) return "";

  if (path.startsWith("http")) return path; // 👈 FIX

  return "http://localhost:8000" + path;
};

// ✅ remove ảnh
const removeImage = () => {
    preview.value = "";
    fileName.value = "";
    form.value.ImageFile = null;

    if (fileInput.value) {
        fileInput.value.value = "";
    }
};

const fileInput = ref(null);

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

/* 🔝 ACTION BAR */
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

/* 🔽 FORM */
.form-card {
  background: #fff;
  padding: 25px;
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

/* GRID */
.grid-2 {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
  margin-bottom: 15px;
}

.el-time-picker {
  width: 100%;
}

/* GROUP */
.form-group {
  display: flex;
  flex-direction: column;
}

.form-group label {
  margin-bottom: 6px;
  font-size: 14px;
  color: #555;
}

/* INPUT */
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

/* ✅ FILE BAR */
.file-name {
  font-size: 14px;
}

.file-bar {
  display: flex;
  align-items: center;
  gap: 10px;
}

/* BOX CHỨA TEXT + BUTTON */
/* FILE DISPLAY (1 BOX DUY NHẤT) */
.file-display {
  display: flex;
  align-items: center;

  border: 1px solid #dcdfe6;
  padding: 8px 12px;
  border-radius: 8px;
  background: #fff;
}

/* TEXT */
.file-display span {
  font-size: 14px;
  color: #333;

  flex: none; /* 👈 KHÔNG cho nó giãn */
}

/* BUTTON XÓA */
.btn-remove {
  padding: 5px 10px;
  background: #e74c3c;
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  flex-shrink: 0;
  margin-left: auto;
}

.btn-remove:hover {
  background: #c0392b;
}

/* BUTTON CHỌN FILE */
.btn-choose {
  margin-right: 8px; /* 👈 khoảng cách nhỏ gọn */
}

.btn-choose:hover {
  background: #eaeaea;
}

/* IMAGE */
.preview-large {
  margin-top: 10px;
  width: 100%;
  max-height: 350px;
  object-fit: cover;
  border-radius: 8px;
}

/* BUTTON */
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

/* RESPONSIVE */
@media (max-width: 768px) {
  .grid-2 {
    grid-template-columns: 1fr;
  }

  .action-bar {
    flex-direction: column;
    align-items: flex-start;
    gap: 10px;
  }
}

.left {
  display: flex;
  align-items: center;
  gap: 10px;
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
</style>