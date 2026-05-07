export default [
  {
    path: "/payments",
    name: "payments",
    component: () => import("../views/List.vue"),
  },
  {
    path: "/payments/:id",
    name: "payments.detail",
    component: () => import("../views/Detail.vue"),
  },
  {
    path: 'pay-success',
    component: () => import('../views/PaySuccess.vue')
  },
  {
    path: 'pay-fail',
    component: () => import('../views/PayFail.vue')
  },
];