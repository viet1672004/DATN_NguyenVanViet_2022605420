export default [
  {
    path: "/parks",
    name: "parks",
    component: () => import("../views/List.vue"),
  },
  {
    path: "/parks/create",
    name: "parks.create",
    component: () => import("../views/Create.vue"), 
  },
  {
    path: "/parks/:id",
    name: "parks.detail",
    component: () => import("../views/Detail.vue"),
  },
  {
    path: "/parks/edit/:id",
    name: "parks.edit",
    component: () => import("../views/Edit.vue"),
  },
];