import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/views/modules/auths/provider/store'

/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/

import Login from '@/views/modules/auths/views/Login.vue'
import Register from '@/views/modules/auths/views/Register.vue'

/*
|--------------------------------------------------------------------------
| LAYOUT
|--------------------------------------------------------------------------
*/

import Layout from '@/layouts/Layout.vue'
import DashBoard from '@/views/DashBoard.vue'
import DashboardRevenuePage from '@/views/modules/dashboard/views/DashboardRevenuePage.vue'
import DashboardBookingsPage from '@/views/modules/dashboard/views/DashboardBookingsPage.vue'
import DashboardStatisticsPage from '@/views/modules/dashboard/views/DashboardStatisticsPage.vue'
import Profile from '@/views/modules/profiles/views/Profile.vue'

/*
|--------------------------------------------------------------------------
| BLOG
|--------------------------------------------------------------------------
*/

import Blogs from '@/views/modules/blogs/views/List.vue'
import BlogCreate from '@/views/modules/blogs/views/Create.vue'
import BlogEdit from '@/views/modules/blogs/views/Edit.vue'
import BlogDetail from '@/views/modules/blogs/views/Detail.vue'
import BlogPage from '@/views/modules/blogs/views/Blog.vue'

/*
|--------------------------------------------------------------------------
| PARK
|--------------------------------------------------------------------------
*/

import Parks from '@/views/modules/parks/views/List.vue'
import ParkCreate from '@/views/modules/parks/views/Create.vue'
import ParkEdit from '@/views/modules/parks/views/Edit.vue'
import ParkDetail from '@/views/modules/parks/views/Detail.vue'

/*
|--------------------------------------------------------------------------
| TICKET
|--------------------------------------------------------------------------
*/

import Tickets from '@/views/modules/tickets/views/List.vue'
import TicketCreate from '@/views/modules/tickets/views/Create.vue'
import TicketEdit from '@/views/modules/tickets/views/Edit.vue'
import TicketDetail from '@/views/modules/tickets/views/Detail.vue'

/*
|--------------------------------------------------------------------------
| BOOKING
|--------------------------------------------------------------------------
*/

import Bookings from '@/views/modules/bookings/views/List.vue'
import BookingCreate from '@/views/modules/bookings/views/Create.vue'
import BookingEdit from '@/views/modules/bookings/views/Edit.vue'
import BookingDetail from '@/views/modules/bookings/views/Detail.vue'

/*
|--------------------------------------------------------------------------
| PAYMENT
|--------------------------------------------------------------------------
*/

import Payments from '@/views/modules/payments/views/List.vue'
import PaymentDetail from '@/views/modules/payments/views/Detail.vue'
import PaymentPay from '@/views/modules/payments/views/Pay.vue'
import PaySuccess from '@/views/modules/payments/views/PaySuccess.vue'
import PayFail from '@/views/modules/payments/views/PayFail.vue'

/*
|--------------------------------------------------------------------------
| USER
|--------------------------------------------------------------------------
*/

import Users from '@/views/modules/users/views/List.vue'
import UserDetail from '@/views/modules/users/views/Detail.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),

  routes: [

    /*
    |--------------------------------------------------------------------------
    | ROOT
    |--------------------------------------------------------------------------
    */

    {
      path: '/',
      redirect: '/dashboard',
    },

    /*
    |--------------------------------------------------------------------------
    | AUTH
    |--------------------------------------------------------------------------
    */

    {
      path: '/login',
      name: 'login',
      component: Login,
      meta: {
        guest: true,
      },
    },

    {
      path: '/register',
      name: 'register',
      component: Register,
      meta: {
        guest: true,
      },
    },

    /*
    |--------------------------------------------------------------------------
    | APP
    |--------------------------------------------------------------------------
    */

    {
      path: '/',
      component: Layout,

      meta: {
        requiresAuth: true,
      },

      children: [

        /*
        |--------------------------------------------------------------------------
        | DASHBOARD
        |--------------------------------------------------------------------------
        */

        {
          path: 'dashboard',
          name: 'dashboard',
          component: DashBoard,
        },

        {
          path: 'dashboard/revenue',
          name: 'dashboard.revenue',
          component: DashboardRevenuePage,
        },

        {
          path: 'dashboard/bookings',
          name: 'dashboard.bookings',
          component: DashboardBookingsPage,
        },

        {
          path: 'dashboard/statistics',
          name: 'dashboard.statistics',
          component: DashboardStatisticsPage,
        },

        {
          path: 'profile',
          name: 'profile',
          component: Profile,
        },

        /*
        |--------------------------------------------------------------------------
        | BLOG
        |--------------------------------------------------------------------------
        */

        {
          path: 'blogs',
          name: 'blogs',
          component: Blogs,
        },

        {
          path: 'blog-page',
          name: 'blog.page',
          component: BlogPage,
        },

        {
          path: 'blogs/create',
          name: 'blogs.create',
          component: BlogCreate,

          meta: {
            role: 'admin',
          },
        },

        {
          path: 'blogs/edit/:id',
          name: 'blogs.edit',
          component: BlogEdit,

          meta: {
            role: 'admin',
          },
        },

        {
          path: "/blogs/detail/:id",
          name: 'blogs.detail',
          component: BlogDetail,
        },

        /*
        |--------------------------------------------------------------------------
        | PARK
        |--------------------------------------------------------------------------
        */

        {
          path: 'parks',
          name: 'parks',
          component: Parks,
        },

        {
          path: 'parks/create',
          name: 'parks.create',
          component: ParkCreate,

          meta: {
            role: 'admin',
          },
        },

        {
          path: 'parks/edit/:id',
          name: 'parks.edit',
          component: ParkEdit,

          meta: {
            role: 'admin',
          },
        },

        {
          path: 'parks/:id',
          name: 'parks.detail',
          component: ParkDetail,
        },

        /*
        |--------------------------------------------------------------------------
        | TICKET
        |--------------------------------------------------------------------------
        */

        {
          path: 'tickets',
          name: 'tickets',
          component: Tickets,
        },

        {
          path: 'tickets/create',
          name: 'tickets.create',
          component: TicketCreate,

          meta: {
            role: 'admin',
          },
        },

        {
          path: 'tickets/edit/:id',
          name: 'tickets.edit',
          component: TicketEdit,

          meta: {
            role: 'admin',
          },
        },

        {
          path: 'tickets/:id',
          name: 'tickets.detail',
          component: TicketDetail,
        },

        /*
        |--------------------------------------------------------------------------
        | BOOKING
        |--------------------------------------------------------------------------
        */

        {
          path: 'bookings',
          name: 'bookings',
          component: Bookings,
        },

        {
          path: 'bookings/create',
          name: 'bookings.create',
          component: BookingCreate,
        },

        {
          path: 'bookings/edit/:id',
          name: 'bookings.edit',
          component: BookingEdit,
        },

        {
          path: 'bookings/:id',
          name: 'bookings.detail',
          component: BookingDetail,
        },

        /*
        |--------------------------------------------------------------------------
        | PAYMENT
        |--------------------------------------------------------------------------
        */

        {
          path: 'payments',
          name: 'payments',
          component: Payments,
        },

        {
          path: 'payments/:id',
          name: 'payments.detail',
          component: PaymentDetail,
        },

        {
          path: 'payments/pay',
          name: 'payments.pay',
          component: PaymentPay,
        },

        /*
        |--------------------------------------------------------------------------
        | VNPAY
        |--------------------------------------------------------------------------
        */

        {
          path: 'pay-success',
          name: 'pay.success',
          component: PaySuccess,
        },

        {
          path: 'pay-fail',
          name: 'pay.fail',
          component: PayFail,
        },

        /*
        |--------------------------------------------------------------------------
        | USER
        |--------------------------------------------------------------------------
        */

        {
          path: 'users',
          name: 'users',
          component: Users,
          meta: { role: 'admin' },
        },

        {
          path: 'users/:id',
          name: 'users.detail',
          component: UserDetail,
          meta: { role: 'admin' },
        },

      ],
    },

    /*
    |--------------------------------------------------------------------------
    | 404
    |--------------------------------------------------------------------------
    */

    {
      path: '/:pathMatch(.*)*',
      redirect: '/dashboard',
    },
  ],
})

/*
|--------------------------------------------------------------------------
| ROUTER GUARD
|--------------------------------------------------------------------------
*/

router.beforeEach((to, from, next) => {

  const store = useAuthStore()

  const token = store.token

  const role = store.user?.role?.RoleName?.toLowerCase();

  /*
  |--------------------------------------------------------------------------
  | CHƯA LOGIN
  |--------------------------------------------------------------------------
  */

  if (
    to.meta.requiresAuth &&
    !token
  ) {
    return next('/login')
  }

  /*
  |--------------------------------------------------------------------------
  | LOGIN RỒI KHÔNG CHO QUAY LẠI LOGIN
  |--------------------------------------------------------------------------
  */

  if (
    to.meta.guest &&
    token
  ) {
    return next('/dashboard')
  }

  /*
  |--------------------------------------------------------------------------
  | ROLE
  |--------------------------------------------------------------------------
  */

  if (
    to.meta.role &&
    to.meta.role !== role
  ) {
    return next('/dashboard')
  }

  next()
})

export default router