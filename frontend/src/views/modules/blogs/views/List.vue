<template>
  <div class="blog-page">

    <!-- HERO SLIDER -->
    <div class="hero-slider">

      <!-- LEFT -->
      <button
        class="nav-btn left"
        @click="prevSlide"
      >
        ❮
      </button>

      <!-- RIGHT -->
      <button
        class="nav-btn right"
        @click="nextSlide"
      >
        ❯
      </button>

      <transition name="fade" mode="out-in">

        <div
          class="hero-item"
          :key="currentBlog?.ID"
        >

          <img
            :src="currentBlog?.BannerImage"
            class="hero-image"
          />

          <div class="overlay">

            <div class="hero-content">

              <h1>
                {{ currentBlog?.Title }}
              </h1>

              <p>
                {{ currentBlog?.Summary }}
              </p>

              <button @click="view(currentBlog?.ID)">
                Xem chi tiết
              </button>

            </div>

          </div>

        </div>

      </transition>

    </div>

    <!-- BLOG GRID -->
    <div class="blog-container">

      <div class="section-title">
        Bài viết nổi bật
      </div>

      <div class="blog-grid">

        <div
          v-for="item in blogs"
          :key="item.ID"
          class="blog-card"
          @click="view(item.ID)"
        >

          <!-- IMAGE -->
          <div class="image-box">

            <img
              :src="item.BannerImage"
              class="blog-image"
            />

          </div>

          <!-- BODY -->
          <div class="blog-body">

            <div class="blog-tags">

              <span
                v-for="tag in formatTags(item.Tags)"
                :key="tag"
              >
                #{{ tag }}
              </span>

            </div>

            <h3>
              {{ item.Title }}
            </h3>

            <p>
              {{ item.Summary }}
            </p>

            <div
                type="button"
                class="read-more"
                @click.stop="view(item.ID)"
              >
                Đọc tiếp →
            </div>

          </div>

        </div>

      </div>

    </div>

  </div>
</template>

<script setup>
import {
  ref,
  onMounted,
  computed,
  onBeforeUnmount
} from "vue";

import { useRouter } from "vue-router";
import { useBlogStore } from "../provider/store";

const router = useRouter();

const blogStore = useBlogStore();

const blogs = ref([]);

const currentIndex = ref(0);

let interval = null;

onMounted(async () => {

  await loadData();

  startSlider();
});

onBeforeUnmount(() => {

  clearInterval(interval);
});

async function loadData() {

  await blogStore.fetchList();

  blogs.value = blogStore.list || [];
}

function startSlider() {

  interval = setInterval(() => {

    nextSlide();

  }, 5000);
}

function nextSlide() {

  if (!blogs.value.length) {
    return;
  }

  currentIndex.value++;

  if (currentIndex.value >= blogs.value.length) {

    currentIndex.value = 0;
  }
}

function prevSlide() {

  if (!blogs.value.length) {
    return;
  }

  currentIndex.value--;

  if (currentIndex.value < 0) {

    currentIndex.value =
      blogs.value.length - 1;
  }
}

const currentBlog = computed(() => {

  return blogs.value[currentIndex.value];
});

function view(id) {

  router.push({
    name: "blogs.detail",
    params: { id }
  });
}

function formatTags(tags) {

  if (!tags) {
    return [];
  }

  return tags
    .split(",")
    .map(x => x.trim())
    .filter(x => x);
}
</script>

<style scoped>
.blog-page {
  background: #f4f6f9;
  min-height: 100vh;
}

/* HERO */
.hero-slider {
  position: relative;

  width: 100%;
  height: 380px;

  overflow: hidden;

  z-index: 1;
}

.hero-item {
  width: 100%;
  height: 100%;

  position: relative;
}

.hero-image {
  width: 100%;
  height: 100%;

  object-fit: cover;
}

/* OVERLAY */
.overlay {
  position: absolute;
  inset: 0;

  background:
    linear-gradient(
      to top,
      rgba(0,0,0,0.75),
      rgba(0,0,0,0.25)
    );

  display: flex;
  align-items: center;
}

/* HERO CONTENT */
.hero-content {
  max-width: 800px;

  padding-left: 80px;

  color: white;
}

.hero-content h1 {
  font-size: 48px;
  margin-bottom: 20px;

  font-weight: 800;
}

.hero-content p {
  font-size: 17px;
  line-height: 1.8;

  margin-bottom: 30px;
}

.hero-content button {
  padding: 14px 28px;

  border: none;
  border-radius: 10px;

  background: #e74c3c;
  color: white;

  font-size: 16px;

  cursor: pointer;

  transition: 0.25s;
}

.hero-content button:hover {
  background: #c0392b;
}

/* NAV BUTTON */
.nav-btn {
  position: absolute;

  top: 50%;
  transform: translateY(-50%);

  width: 52px;
  height: 52px;

  border: none;
  border-radius: 50%;

  background: rgba(0,0,0,0.45);
  color: white;

  font-size: 30px;

  cursor: pointer;

  z-index: 10;

  transition: 0.3s;

  opacity: 0;
  visibility: hidden;
}

/* SHOW WHEN HOVER */
.hero-slider:hover .nav-btn {
  opacity: 1;
  visibility: visible;
}

.nav-btn:hover {
  background: rgba(0,0,0,0.7);
}

.nav-btn.left {
  left: 20px;
}

.nav-btn.right {
  right: 20px;
}

/* BLOG CONTAINER */
.blog-container {
  max-width: 1400px;

  margin: auto;

  padding: 60px 30px;
}

/* TITLE */
.section-title {
  font-size: 34px;
  font-weight: 700;

  margin-bottom: 35px;

  color: #222;
}

/* GRID */
.blog-grid {
  display: grid;

  grid-template-columns:
    repeat(auto-fill, minmax(350px, 1fr));

  gap: 30px;
}

/* CARD */
.blog-card {
  background: white;

  border-radius: 16px;

  overflow: hidden;

  cursor: pointer;

  box-shadow:
    0 4px 14px rgba(0,0,0,0.08);

  transition: 0.25s;
}

.blog-card:hover {
  transform: translateY(-6px);
}

/* IMAGE */
.image-box {
  overflow: hidden;

  height: 240px;
}

.blog-image {
  width: 100%;
  height: 100%;

  object-fit: cover;

  transition: 0.3s;
}

.blog-card:hover .blog-image {
  transform: scale(1.06);
}

/* BODY */
.blog-body {
  padding: 22px;
}

/* TAGS */
.blog-tags {
  display: flex;

  gap: 8px;
  flex-wrap: wrap;

  margin-bottom: 14px;
}

.blog-tags span {
  background: #eef2f7;

  padding: 6px 12px;

  border-radius: 20px;

  font-size: 13px;

  color: #555;
}

/* TITLE */
.blog-body h3 {
  font-size: 24px;

  margin-bottom: 14px;

  color: #222;
}

/* SUMMARY */
.blog-body p {
  color: #666;

  line-height: 1.8;

  margin-bottom: 18px;
}

/* READ MORE */
.read-more {
  color: #3498db;

  font-weight: 600;
}

/* ANIMATION */
.fade-enter-active,
.fade-leave-active {
  transition: opacity .6s;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

/* RESPONSIVE */
@media (max-width: 768px) {

  .hero-slider {
    height: 420px;
  }

  .hero-content {
    padding: 30px;
  }

  .hero-content h1 {
    font-size: 34px;
  }

  .hero-content p {
    font-size: 15px;
  }

  .nav-btn {
    width: 42px;
    height: 42px;

    font-size: 24px;
  }

  .blog-grid {
    grid-template-columns: 1fr;
  }
}
</style>