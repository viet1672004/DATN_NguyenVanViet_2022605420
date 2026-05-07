export default [
  {
    path: "/bookings",
    name: "bookings",
    component: () => import("../views/List.vue"),
  },
  {
    path: "/bookings/create",
    name: "bookings.create",
    component: () => import("../views/Create.vue"),
  },
  {
    path: "/bookings/:id",
    name: "bookings.detail",
    component: () => import("../views/Detail.vue"),
  },
  {
    path: "/bookings/edit/:id",
    name: "bookings.edit",
    component: () => import("../views/Edit.vue"),
  },
];