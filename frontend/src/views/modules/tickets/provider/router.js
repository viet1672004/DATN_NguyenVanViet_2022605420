export default [
  {
    path: "/tickets",
    name: "tickets",
    component: () => import("./views/List.vue"),
  },
  {
    path: "/tickets/create",
    name: "tickets.create",
    component: () => import("./views/Create.vue"),
  },
  {
    path: "/tickets/:id",
    name: "tickets.detail",
    component: () => import("./views/Detail.vue"),
  },
  {
    path: "/tickets/edit/:id",
    name: "tickets.edit",
    component: () => import("./views/Edit.vue"),
  },
];