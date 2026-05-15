export default [

  {
    path: "/blogs",
    name: "blogs",
    component: () => import("../views/List.vue"),
  },

  {
    path: "/blogs/create",
    name: "blogs.create",
    component: () => import("../views/Create.vue"),
  },

  {
    path: "/blogs/detail/:id",
    name: "blogs.detail",
    component: () => import("../views/Detail.vue"),
  },

  {
    path: "/blogs/edit/:id",
    name: "blogs.edit",
    component: () => import("../views/Edit.vue"),
  },
];