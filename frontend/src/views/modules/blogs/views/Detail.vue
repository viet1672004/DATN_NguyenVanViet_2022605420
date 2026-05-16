<template>
  <div class="blog-detail-page">

    <!-- HEADER -->
    <div class="page-header">

      <div class="left">

        <span
          class="back-icon"
          @click="goBack"
        >
          <i class="arrow"></i>
        </span>

        <span class="page-title">
          Chi tiết Blog
        </span>

      </div>

      <div
        v-if="isAdmin"
        class="actions"
      >

        <button
          class="btn-edit"
          @click="goEdit"
        >
          Chỉnh sửa
        </button>

      </div>

    </div>

    <!-- CONTENT -->
    <div
      v-if="blog"
      class="blog-card"
    >

      <!-- Banner -->
      <div class="banner-wrapper">

        <img
          v-if="blog.BannerImage"
          :src="blog.BannerImage"
          class="banner"
        />

      </div>

      <!-- Title -->
      <h1 class="title">
        {{ blog.Title }}
      </h1>

      <!-- Info -->
      <div class="info">  

        <span>
          🕒 Ngày tạo: {{ formatDate(blog.created_at) }}
        </span>

      </div>

      <!-- Summary -->
      <div
        v-if="blog.Summary"
        class="summary"
      >
        {{ blog.Summary }}
      </div>

      <!-- Tags -->
      <div
        v-if="blog.Tags"
        class="tags"
      >

        <span
          v-for="tag in tagList"
          :key="tag"
          class="tag"
        >
          #{{ tag }}
        </span>

      </div>

      <!-- Content -->
      <div class="content">
        {{ blog.Content }}
      </div>

      <div
        v-if="blog.park"
        class="booking-box"
      >

        <div class="booking-content">

          <!-- INFO -->
          <div class="booking-info">

            <div class="park-info-line">
              <span class="label">
                Khu vui chơi:
              </span>

              <span class="value">
                {{ blog.park.ParkName }}
              </span>

            </div>

          </div>

          <!-- ACTION -->
          <router-link
            :to="`/bookings/create?park_id=${blog.ParkID}`"
            class="booking-btn"
          >
            Đặt vé ngay
          </router-link>

        </div>

      </div>

    </div>

    <!-- LOADING -->
    <div
      v-else
      class="loading"
    >
      Đang tải bài viết...
    </div>

  </div>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import { useRoute, useRouter } from "vue-router";
import { useBlogStore } from "../provider/store";
import { useAuthStore } from "@/views/modules/auths/provider/store";

const route = useRoute();
const router = useRouter();

const blogStore = useBlogStore();
const authStore = useAuthStore();

const blog = ref(null);

const isAdmin = computed(() => {
  return authStore.user?.Role?.toLowerCase() === "admin";
});

const tagList = computed(() => {

  if (!blog.value?.Tags) {
    return [];
  }

  return blog.value.Tags
    .split(',')
    .map(x => x.trim())
    .filter(x => x);
});

onMounted(() => {

  loadDetail();
});

async function loadDetail() {

  const id = route.params.id;

  const res = await blogStore.getDetail(id);

  blog.value = res.data;
}

function goBack() {

  router.push('/blogs');
}

function goEdit() {

  router.push(`/blogs/edit/${blog.value.ID}`);
}

function formatDate(date) {

  if (!date) {
    return '';
  }

  const d = new Date(date);

  const time = d.toLocaleTimeString('vi-VN');

  const day = d.toLocaleDateString('vi-VN');

  return `${time} - ${day}`;
}
</script>

<style scoped>
.blog-detail-page {
  max-width: 1200px;
  margin: 0 auto;
  padding: 30px 20px 60px;
  background: #f4f7fb;
  min-height: 100vh;
}

/* =========================
   HEADER
========================= */
.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;

  background: #fff;
  padding: 16px 22px;
  border-radius: 16px;

  margin-bottom: 24px;

  box-shadow:
    0 4px 12px rgba(0, 0, 0, 0.06);
}

.left {
  display: flex;
  align-items: center;
  gap: 14px;
}

/* BACK BUTTON */
.back-icon {
  width: 38px;
  height: 38px;
  border-radius: 50%;

  background: #1e293b;

  display: flex;
  align-items: center;
  justify-content: center;

  cursor: pointer;
  transition: all 0.25s ease;
}

.back-icon:hover {
  background: #334155;
  transform: translateX(-2px);
}

.arrow {
  width: 9px;
  height: 9px;

  border-left: 2px solid #fff;
  border-bottom: 2px solid #fff;

  transform: rotate(45deg);
  margin-left: 2px;
}

.page-title {
  font-size: 28px;
  font-weight: 700;
  color: #0f172a;
}

/* ACTIONS */
.actions {
  display: flex;
  align-items: center;
  gap: 12px;
}

.btn-edit {
  border: none;
  outline: none;

  padding: 12px 22px;
  border-radius: 10px;

  cursor: pointer;

  font-size: 15px;
  font-weight: 600;

  color: white;
  background: #2563eb;

  transition: all 0.25s ease;
}

.btn-edit:hover {
  background: #1d4ed8;
  transform: translateY(-1px);
}

/* =========================
   CARD
========================= */
.blog-card {
  background: #ffffff;
  border-radius: 22px;
  overflow: hidden;

  transition: all 0.3s ease;

  box-shadow:
    0 10px 30px rgba(0, 0, 0, 0.06),
    0 2px 10px rgba(0, 0, 0, 0.04);
}

.blog-card:hover {
  transform: translateY(-2px);
}

/* =========================
   BANNER
========================= */
.banner-wrapper {
  width: 100%;
  height: clamp(260px, 45vw, 500px);

  overflow: hidden;
  background: #f1f5f9;
  position: relative;
}

.banner {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}

/* overlay cho ảnh đỡ chói */
.banner-wrapper::after {
  content: "";
  position: absolute;
  inset: 0;

  background:
    linear-gradient(
      to bottom,
      rgba(0, 0, 0, 0.08),
      rgba(0, 0, 0, 0.18)
    );
}

/* =========================
   BODY
========================= */
.title {
  padding: 35px 40px 14px;

  font-size: 42px;
  line-height: 1.3;
  font-weight: 800;

  color: #0f172a;

  margin: 0;
}

.info {
  display: flex;
  align-items: center;
  flex-wrap: wrap;

  gap: 18px;

  padding: 0 40px;

  color: #64748b;
  font-size: 15px;

  margin-bottom: 28px;
}

.info span {
  display: flex;
  align-items: center;
  gap: 6px;
}

/* =========================
   SUMMARY
========================= */
.summary {
  margin: 0 40px 32px;

  padding: 22px 24px;

  background: #f8fafc;

  border-left: 5px solid #2563eb;
  border-radius: 14px;

  font-size: 17px;
  line-height: 1.9;

  color: #334155;
}

/* =========================
   TAGS
========================= */
.tags {
  display: flex;
  flex-wrap: wrap;

  gap: 12px;

  padding: 0 40px 35px;
}

.tag {
  background: #eff6ff;
  color: #2563eb;

  padding: 10px 16px;
  border-radius: 999px;

  font-size: 14px;
  font-weight: 600;

  transition: 0.2s;
}

.tag:hover {
  background: #dbeafe;
}

/* =========================
   CONTENT
========================= */
.content {
  padding: 0 40px 10px;

  color: #334155;

  font-size: 17px;
  line-height: 1.95;

  white-space: pre-line;
}

/* Paragraph */
.content :deep(p) {
  margin-bottom: 20px;
}

/* Heading */
.content :deep(h1),
.content :deep(h2),
.content :deep(h3),
.content :deep(h4) {
  margin-top: 38px;
  margin-bottom: 18px;

  color: #0f172a;

  font-weight: 700;
  line-height: 1.4;
}

.content :deep(h1) {
  font-size: 34px;
}

.content :deep(h2) {
  font-size: 28px;
}

.content :deep(h3) {
  font-size: 24px;
}

/* Image */
.content :deep(img) {
  max-width: 100%;

  border-radius: 16px;

  margin: 28px 0;

  display: block;

  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
}

/* Iframe */
.content :deep(iframe) {
  width: 100%;
  height: 520px;

  border: none;

  margin: 30px 0;

  border-radius: 18px;
  overflow: hidden;
}

/* List */
.content :deep(ul),
.content :deep(ol) {
  padding-left: 24px;
  margin-bottom: 22px;
}

.content :deep(li) {
  margin-bottom: 10px;
}

/* Table */
.content :deep(table) {
  width: 100%;
  border-collapse: collapse;
  margin: 28px 0;
}

.content :deep(table th),
.content :deep(table td) {
  border: 1px solid #e2e8f0;
  padding: 14px;
  text-align: left;
}

.content :deep(table th) {
  background: #f8fafc;
}

/* =========================
   BOOKING BOX
========================= */
.booking-box {
  padding: 10px 40px 45px;
  margin-top: 20px;
}

.booking-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;

  text-decoration: none;

  background: linear-gradient(
    135deg,
    #ef4444,
    #dc2626
  );

  color: white;

  padding: 15px 28px;
  border-radius: 12px;

  font-weight: 700;
  font-size: 16px;

  transition: all 0.25s ease;

  box-shadow: 0 8px 20px rgba(239, 68, 68, 0.25);
}

.booking-btn:hover {
  transform: translateY(-2px);

  box-shadow:
    0 12px 25px rgba(239, 68, 68, 0.35);
}

/* =========================
   LOADING
========================= */
.loading {
  min-height: 60vh;

  display: flex;
  align-items: center;
  justify-content: center;

  font-size: 18px;
  color: #64748b;
  font-weight: 600;
}

/* =========================
   RESPONSIVE
========================= */
@media (max-width: 768px) {

  .blog-detail-page {
    padding: 20px 14px 40px;
  }

  .page-header {
    padding: 14px 16px;
    flex-direction: row;
    align-items: center;
  }

  .page-title {
    font-size: 22px;
  }

  .btn-edit {
    padding: 10px 16px;
    font-size: 14px;
    flex: 1;
  }

  .title {
    font-size: 28px;
    line-height: 1.4;

    padding: 24px 20px 10px;
  }

  .info,
  .tags,
  .content,
  .booking-box {
    padding-left: 20px;
    padding-right: 20px;
  }

  .summary {
    margin-left: 20px;
    margin-right: 20px;
  }

  .content {
    font-size: 16px;
    line-height: 1.8;
  }

  .content :deep(iframe) {
    height: 260px;
  }
}

/* =========================
   BOOKING BOX
========================= */
.booking-box {
  padding: 20px 40px 45px;
  margin-top: 10px;
}

.booking-content {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 30px;

  background: linear-gradient(
    135deg,
    #fff7f7,
    #fff1f1
  );

  border: 1px solid #fecaca;
  border-radius: 20px;

  padding: 28px 34px;

  box-shadow:
    0 6px 18px rgba(239, 68, 68, 0.08);
}

/* INFO */
.booking-info {
  flex: 1;
  min-width: 0;
}

.park-info-line {
  display: flex;
  align-items: center;
  gap: 12px;

  font-size: 18px;
  line-height: 1.7;
}

.label {
  font-size: 25px;
  font-weight: 700;
  color: #dc2626;

  white-space: nowrap;
}

.value {
  font-size: 25px;
  font-weight: 700;
  color: #0f172a;

  line-height: 1.5;
}

/* BUTTON */
.booking-btn {
  flex-shrink: 0;

  display: inline-flex;
  align-items: center;
  justify-content: center;

  min-width: 180px;
  height: 58px;

  padding: 0 26px;

  text-decoration: none;

  background: linear-gradient(
    135deg,
    #ef4444,
    #dc2626
  );

  color: #fff;

  border-radius: 16px;

  font-size: 18px;
  font-weight: 700;

  border: 2px solid #111827;

  transition: all 0.25s ease;

  box-shadow:
    0 10px 24px rgba(239, 68, 68, 0.18);
}

.booking-btn:hover {
  transform: translateY(-2px);

  box-shadow:
    0 14px 30px rgba(239, 68, 68, 0.28);
}

/* MOBILE */
@media (max-width: 768px) {

  .booking-box {
    padding: 10px 20px 35px;
  }

  .booking-content {
    flex-direction: column;
    align-items: flex-start;

    padding: 22px 20px;
    gap: 20px;
  }

  .park-info-line {
    flex-direction: column;
    align-items: flex-start;
    gap: 4px;
  }

  .value {
    font-size: 20px;
  }

  .label {
    font-size: 20px;
  }

  .booking-btn {
    width: 100%;
    min-width: unset;
  }
}
</style>