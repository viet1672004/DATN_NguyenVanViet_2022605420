import Layout from "@/layouts/Layout.vue";

export default [
  {
    path: "/profile",
    component: Layout,
    children: [
      {
        path: "",
        name: "Profile",
        component: () => import("./views/Profile.vue"),
        meta: {
          requiresAuth: true,
        }
      }
    ]
  }
];