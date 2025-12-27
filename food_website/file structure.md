food-website/
│
├── index.php
├── menu.php
├── about.php
├── contact.php
├── testimonial.php
├── cart.php
├── checkout.php
├── help.php
├── terms-of-use.php
├── return-policy.php
├── cookie.php
├── privacy-policy.php
├── login.php
├── register.php
├── logout.php
│
├── includes/
│   ├── config.php
│   ├── header.php
│   ├── footer.php
│   ├── addToCart.php
│   ├── removeCart.php
│   ├── clearCart.php
│   ├── functions.php
│   └── session.php
│
├── admin/
│   ├── index.php (dashboard)
│   ├── login.php
│   ├── logout.php
│   ├── manage-products.php
│   ├── add-product.php
│   ├── edit-product.php
│   ├── delete-product.php
│   ├── manage-categories.php
│   ├── manage-orders.php
│   ├── view-order.php
│   ├── manage-users.php
│   ├── manage-testimonials.php
│   ├── settings.php
│   └── includes/
│       ├── admin-header.php
│       ├── admin-footer.php
│       ├── admin-sidebar.php
│       └── admin-functions.php
│
├── user/
│   ├── index.php (dashboard)
│   ├── profile.php
│   ├── edit-profile.php
│   ├── orders.php
│   ├── order-details.php
│   ├── change-password.php
│   └── includes/
│       ├── user-header.php
│       ├── user-footer.php
│       └── user-sidebar.php
│
├── assets/
│   ├── css/
│   │   ├── style.css
│   │   ├── admin.css
│   │   └── user.css
│   ├── js/
│   │   ├── main.js
│   │   ├── cart.js
│   │   └── admin.js
│   ├── images/
│   │   ├── logo.png
│   │   ├── banner/
│   │   └── products/
│   └── fonts/
│
├── uploads/
│   ├── products/
│   └── testimonials/
│
└── payment/
    ├── paystack-callback.php
    ├── flutterwave-callback.php
    └── verify-payment.php