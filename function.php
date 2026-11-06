<?php

if (!defined('WP_DEBUG')) {
    die('Direct access forbidden.');
}

add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');
});





// Menu Profile page
// 
// Add a custom endpoint for the profile page
function custom_add_profile_endpoint()
{
    add_rewrite_endpoint('profile', EP_ROOT | EP_PAGES);
}
add_action('init', 'custom_add_profile_endpoint');

// Add the endpoint to query vars
function custom_add_profile_query_vars($vars)
{
    $vars[] = 'profile';
    return $vars;
}
add_filter('query_vars', 'custom_add_profile_query_vars', 0);

// Enqueue custom styles and scripts
function enqueue_custom_styles_and_scripts()
{
    wp_enqueue_style('bootstrap-css', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css');
    wp_enqueue_script('jquery'); // Ensure jQuery is loaded
    wp_enqueue_script('bootstrap-js', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js', array('jquery'), null, true);
}
add_action('wp_enqueue_scripts', 'enqueue_custom_styles_and_scripts');



// Output the modal HTML in the footer
function add_login_modal()
{
    $u_param = isset($_GET['verify_email_u']) ? sanitize_text_field($_GET['verify_email_u']) : (isset($_POST['verify_email_u']) ? sanitize_text_field($_POST['verify_email_u']) : '');
    if (!empty($u_param)) {
        ?>
        <style>
            #custom-otp-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.8);
                z-index: 999999;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            #custom-otp-box {
                background: #fff;
                padding: 30px;
                border-radius: 8px;
                max-width: 400px;
                width: 90%;
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
                font-family: sans-serif;
            }

            #custom-otp-box h3 {
                margin-top: 0;
                margin-bottom: 20px;
                font-size: 24px;
                color: #333;
            }

            #custom-otp-box .alert {
                padding: 10px;
                margin-bottom: 20px;
                border-radius: 4px;
            }

            #custom-otp-box .alert-danger {
                background: #f8d7da;
                color: #721c24;
                border: 1px solid #f5c6cb;
            }

            #custom-otp-box .alert-info {
                background: #d1ecf1;
                color: #0c5460;
                border: 1px solid #bee5eb;
            }

            #custom-otp-box label {
                display: block;
                margin-bottom: 5px;
                font-weight: bold;
                color: #555;
            }

            #custom-otp-box input[type="text"] {
                width: 100%;
                padding: 10px;
                border: 1px solid #ccc;
                border-radius: 4px;
                margin-bottom: 20px;
                box-sizing: border-box;
            }

            #custom-otp-box button {
                width: 100%;
                padding: 10px;
                background: #007bff;
                color: #fff;
                border: none;
                border-radius: 4px;
                font-size: 16px;
                cursor: pointer;
            }

            #custom-otp-box button:hover {
                background: #0056b3;
            }
        </style>
        <div id="custom-otp-overlay">
            <div id="custom-otp-box">
                <h3><?php _e('Verify Email', 'textdomain'); ?></h3>
             
                <form method="post" action="">
                    <input type="hidden" name="verify_email_u" value="<?php echo esc_attr($u_param); ?>">
                    <p style="color: #666; margin-bottom: 15px;">We sent a 6-digit verification code to your email. Please enter
                        it below.</p>
                    <label for="verification_code"><?php _e('Verification Code', 'textdomain'); ?></label>
                    <input type="text" id="verification_code" name="verification_code" required>
                    <button type="submit" name="verify_email_submit" value="1"><?php _e('Verify', 'textdomain'); ?></button>
                </form>
            </div>
        </div>
        <?php
        return;
    }
    ?>
    <!-- Modal for Login Form -->
    <div class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-labelledby="loginModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="loginModalLabel"><?php _e('Login', 'textdomain'); ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="post" action="<?php echo esc_url(wp_login_url()); ?>">
                        <div class="form-group">
                            <label for="username"><?php _e('Username', 'textdomain'); ?></label>
                            <input type="text" class="form-control" id="username" name="log" required>
                        </div>
                        <div class="form-group">
                            <label for="password"><?php _e('Password', 'textdomain'); ?></label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password" name="pwd" required>
                                <div class="input-group-addon">
                                    <input type="checkbox" id="show-password" onclick="togglePassword()">
                                    <label for="show-password"><?php _e('Show', 'textdomain'); ?></label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group" id="btns">
                            <input type="hidden" name="redirect_to" value="<?php echo esc_url(home_url()); ?>" />
                            <button type="submit" class="btn btn-primary"><?php _e('Login', 'textdomain'); ?></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        function togglePassword() {
            var passwordField = document.getElementById("password");
            var showPasswordCheckbox = document.getElementById("show-password");

            if (showPasswordCheckbox.checked) {
                passwordField.type = "text"; // Show password
            } else {
                passwordField.type = "password"; // Hide password
            }
        }
    </script>
    <?php
}
add_action('wp_footer', 'add_login_modal');

// Add custom CSS to ensure the modal is centered
function custom_modal_css()
{
    ?>
    <style>
        .modal-dialog {
            margin: auto;
            /* Centers the modal */
        }

        .modal-content {
            max-width: 600px;
            width: 100%;
            padding: 25px 30px;
        }

        .modal-body {
            padding: 20px;
            /* Optional: Adjust padding for modal body */
        }

        .modal-body label {
            font-size: 18px;
        }

        .modal-dialog {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: calc(100% - 1rem);
        }

        .modal-header {
            border: 0px;
        }

        .dropdown-menu {
            z-index: 1000;
            /* Ensure the dropdown appears above other elements */
        }

        .dropdown-toggle::after {
            display: inline-block;
            margin-left: 1.255em;
            vertical-align: .255em;
            content: "";
            border-top: .3em solid;
            border-right: .3em solid transparent;
            border-bottom: 0;
            border-left: .3em solid transparent;
        }
    </style>
    <?php
}
add_action('wp_head', 'custom_modal_css');

// Redirect users after login based on role
function custom_login_redirect($redirect_to, $request, $user)
{
    // Check if user is logged in
    if (isset($user->roles) && is_array($user->roles)) {
        // Admin login
        if (in_array('administrator', $user->roles)) {
            return 'https://coconutmakers.com/add-product/';
        }
        // Staff login
        if (in_array('staff', $user->roles)) {
            return 'https://coconutmakers.com/today-delivery/';
        }
        // Regular user login
        if (in_array('subscriber', $user->roles)) {
            return 'https://coconutmakers.com/my-account/';
        }
    }
    return $redirect_to; // Fallback to default redirect
}
add_filter('login_redirect', 'custom_login_redirect', 10, 3);



// Registration form
add_shortcode('custom_registration_form', 'custom_woocommerce_registration_form');

function custom_woocommerce_registration_form()
{
    if (is_user_logged_in()) {
        return '<p>You are already logged in.</p>';
    }

    $apartments = get_option('custom_apartments', []);

    ob_start();

    // Process form submission
    if (isset($_POST['action']) && $_POST['action'] === 'custom_register_action') {
        if (!isset($_POST['woocommerce-register-nonce']) || !wp_verify_nonce($_POST['woocommerce-register-nonce'], 'woocommerce-register')) {
            echo '<div class="woocommerce-error" role="alert">Security check failed. Please refresh the page and try again.</div>';
        } else {
            $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
            $username = isset($_POST['username']) ? sanitize_user($_POST['username']) : '';
            $password = isset($_POST['password']) ? $_POST['password'] : '';
            $apartment = isset($_POST['apartment']) ? sanitize_text_field($_POST['apartment']) : '';

            $validation_error = new WP_Error();
            $validation_error = apply_filters('woocommerce_process_registration_errors', $validation_error, $username, $password, $email);

            if ($validation_error->get_error_messages()) {
                echo '<ul class="woocommerce-error" role="alert">';
                foreach ($validation_error->get_error_messages() as $message) {
                    echo '<li><strong>Error:</strong> ' . wp_kses_post($message) . '</li>';
                }
                echo '</ul>';
            } else {
                $customer_id = wc_create_new_customer($email, $username, $password);

                if (is_wp_error($customer_id)) {
                    echo '<ul class="woocommerce-error" role="alert"><li><strong>Error:</strong> ' . wp_kses_post($customer_id->get_error_message()) . '</li></ul>';
                } else {
                    // Success!
                    $myaccount_url = wc_get_page_permalink('myaccount');
                    if (!$myaccount_url) $myaccount_url = home_url();
                    
                    $verify_url = add_query_arg('verify_email_u', base64_encode($customer_id), $myaccount_url);
                    
                    echo '<p>Registration successful! Redirecting to verification page...</p>';
                    echo '<script>window.location.href = "' . esc_url_raw($verify_url) . '";</script>';
                    return ob_get_clean();
                }
            }
        }
    }

    // Display registration form
    wc_print_notices();
    ?>
    <form method="post" action="" class="woocommerce-form woocommerce-form-register" id="custom-registration-form">
        <p class="form-row form-row-wide">
            <label for="reg_username">Username <span class="required">*</span></label>
            <input type="text" class="input-text" name="username" id="reg_username"
                value="<?php echo esc_attr($_POST['username'] ?? ''); ?>" required />
        </p>
        <p class="form-row form-row-wide">
            <label for="reg_email">Email address <span class="required">*</span></label>
            <input type="email" class="input-text" name="email" id="reg_email"
                value="<?php echo esc_attr($_POST['email'] ?? ''); ?>" required />
        </p>
        <p class="form-row form-row-wide">
            <label for="reg_password">Password <span class="required">*</span></label>
            <input type="password" class="input-text" name="password" id="reg_password" required />
        </p>
        <p class="form-row form-row-wide">
            <label for="reg_apartment">Select Apartment <span class="required">*</span></label>
            <select name="apartment" id="reg_apartment" class="input-text" required>
                <option value="">-- Select Apartment --</option>
                <?php 
                $selected_apartment = isset($_POST['apartment']) ? sanitize_text_field($_POST['apartment']) : '';
                foreach ($apartments as $apartment): ?>
                    <option value="<?php echo esc_attr($apartment); ?>" <?php selected($selected_apartment, $apartment); ?>><?php echo esc_html($apartment); ?></option>
                <?php endforeach; ?>
            </select>
        </p>
        <p class="form-row">
            <input type="hidden" name="action" value="custom_register_action" />
            <input type="hidden" name="register" value="Register" />
            <?php wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' ); ?>
            <button type="submit" class="woocommerce-button button" name="register" value="Register">Register</button>
        </p>
    </form>
    <?php

    return ob_get_clean();
}
add_action('woocommerce_created_customer', 'custom_otp_after_registration', 10, 3);
function custom_otp_after_registration($customer_id, $new_customer_data, $password_generated)
{
    if (isset($_POST['apartment'])) {
        update_user_meta($customer_id, 'apartment', sanitize_text_field($_POST['apartment']));
    }

    // Email Verification Flow
    $verification_code = wp_rand(100000, 999999);
    update_user_meta($customer_id, '_email_verify_code', $verification_code);
    update_user_meta($customer_id, '_email_verify_code_time', time());
    update_user_meta($customer_id, '_is_email_verified', 'no');

    // Send email
    $subject = 'Email Verification Code';
    $message = 'Your verification code is: ' . $verification_code;
    wp_mail($new_customer_data['user_email'], $subject, $message);

    // Redirect to My Account page with verification param to bypass live site caching
    $myaccount_url = wc_get_page_permalink('myaccount');
    if (!$myaccount_url) {
        $myaccount_url = home_url();
    }
    $verify_url = add_query_arg('verify_email_u', base64_encode($customer_id), $myaccount_url);
    wp_redirect($verify_url);
    exit;
}

add_action('woocommerce_before_my_account', 'display_user_apartment');

function display_user_apartment()
{
    $user_id = get_current_user_id();
    $apartment = get_user_meta($user_id, 'apartment', true);

    if (!empty($apartment)) {
        echo '<p><strong>Apartment:</strong> ' . esc_html($apartment) . '</p>';
    }
}




// Create admin menu to add/edit apartments
add_action('admin_menu', 'custom_apartment_admin_menu');
function custom_apartment_admin_menu()
{
    add_menu_page(
        __('Apartments', 'woocommerce'),
        __('Apartments', 'woocommerce'),
        'manage_options',
        'custom-apartments',
        'custom_apartments_page'
    );
}

// Apartments admin page content
function custom_apartments_page()
{
    if (!current_user_can('manage_options')) {
        return;
    }

    if (isset($_POST['new_apartment']) && !empty($_POST['new_apartment'])) {
        $apartments = get_option('custom_apartments', []);
        $apartments[] = sanitize_text_field($_POST['new_apartment']);
        update_option('custom_apartments', $apartments);
    }

    if (isset($_POST['delete_apartment'])) {
        $apartments = get_option('custom_apartments', []);
        $key = array_search($_POST['delete_apartment'], $apartments);
        if ($key !== false) {
            unset($apartments[$key]);
            update_option('custom_apartments', $apartments);
        }
    }

    $apartments = get_option('custom_apartments', []);
    ?>
    <div class="wrap">
        <h1><?php _e('Manage Apartments', 'woocommerce'); ?></h1>
        <form method="post">
            <input type="text" name="new_apartment" placeholder="Apartment Name" required />
            <button type="submit" class="button button-primary"><?php _e('Add Apartment', 'woocommerce'); ?></button>
        </form>

        <h2><?php _e('Existing Apartments', 'woocommerce'); ?></h2>
        <ul>
            <?php foreach ($apartments as $apartment): ?>
                <li>
                    <?php echo esc_html($apartment); ?>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="delete_apartment" value="<?php echo esc_attr($apartment); ?>" />
                        <button type="submit" class="button button-link-delete"><?php _e('Delete', 'woocommerce'); ?></button>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php
}

// Redirect to My Account page after successful registration
add_filter('woocommerce_registration_redirect', 'custom_registration_redirect');
function custom_registration_redirect($redirect)
{
    return wc_get_page_permalink('myaccount');
}

// Hide Register menu item when user is logged in
add_filter('wp_nav_menu_items', 'hide_register_menu_item', 10, 2);
function hide_register_menu_item($items, $args)
{
    // Replace 'primary' with your menu location (e.g., 'main-menu' if different)
    if (is_user_logged_in() && $args->theme_location == 'primary') {
        // Change 'Register' to the exact label of your register menu item
        $items = str_replace('<li><a href="' . site_url('/register') . '" id="reg">Register</a></li>', '', $items);
    }
    return $items;
}
function restrict_add_to_cart_for_non_logged_in_users()
{
    if (!is_user_logged_in()) {
        wc_add_notice(__('You must be registered and logged in to order products.', 'woocommerce'), 'error');
        wp_redirect('https://coconutmakers.com/register/'); // Redirect to registration page
        exit;
    }
}
add_action('woocommerce_before_calculate_totals', 'restrict_add_to_cart_for_non_logged_in_users');



// Frontend Apartment
// 
// 
// Shortcode to display apartment management form on the front end
function custom_frontend_apartment_management()
{
    if (!current_user_can('manage_options')) {
        return '<p>' . __('You do not have permission to view this page.', 'woocommerce') . '</p>';
    }

    // Handle form submission to add a new apartment
    if (isset($_POST['frontend_new_apartment']) && !empty($_POST['frontend_new_apartment'])) {
        $apartments = get_option('custom_apartments', []);
        $apartments[] = sanitize_text_field($_POST['frontend_new_apartment']);
        update_option('custom_apartments', $apartments);
    }

    // Handle form submission to delete an apartment
    if (isset($_POST['frontend_delete_apartment'])) {
        $apartments = get_option('custom_apartments', []);
        $key = array_search($_POST['frontend_delete_apartment'], $apartments);
        if ($key !== false) {
            unset($apartments[$key]);
            update_option('custom_apartments', $apartments);
        }
    }

    // Retrieve the list of apartments
    $apartments = get_option('custom_apartments', []);

    ob_start();
    ?>
    <div class="custom-apartment-management">
        <h1><?php _e('Manage Apartments', 'woocommerce'); ?></h1>

        <!-- Form to add a new apartment -->
        <form method="post">
            <input type="text" name="frontend_new_apartment" placeholder="Apartment Name" required />
            <button type="submit" class="button button-primary"><?php _e('Add Apartment', 'woocommerce'); ?></button>
        </form>

        <!-- Display the list of existing apartments -->
        <h2><?php _e('Existing Apartments', 'woocommerce'); ?></h2>
        <ul>
            <?php foreach ($apartments as $apartment): ?>
                <li>
                    <?php echo esc_html($apartment); ?>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="frontend_delete_apartment" value="<?php echo esc_attr($apartment); ?>" />
                        <button type="submit" class="button button-link-delete"><?php _e('Delete', 'woocommerce'); ?></button>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <?php
    return ob_get_clean();
}
add_shortcode('frontend_apartment_management', 'custom_frontend_apartment_management');





// Assign
// 
// 
add_action('wp_ajax_assign_order_staff', 'assign_order_staff');
function assign_order_staff()
{
    check_ajax_referer('assign_order_staff_nonce', 'security');

    $order_id = intval($_POST['order_id']);
    $staff_id = intval($_POST['staff_id']);

    // Update the order with the assigned staff
    if ($order_id && $staff_id) {
        update_post_meta($order_id, 'assigned_staff', $staff_id);
        wp_send_json_success();
    } else {
        wp_send_json_error('Invalid order or staff ID.');
    }
}

add_action('wp_ajax_delete_order', 'delete_order');
function delete_order()
{
    check_ajax_referer('delete_order_nonce', 'security');

    $order_id = intval($_POST['order_id']);

    // Delete the order
    if ($order_id) {
        wp_delete_post($order_id, true); // True for force deletion
        wp_send_json_success();
    } else {
        wp_send_json_error('Invalid order ID.');
    }
}



// Order option
// 
// 
// Step 1: Register the new endpoint with the updated link
function custom_order_list_endpoint()
{
    add_rewrite_endpoint('my-orders', EP_ROOT | EP_PAGES);
}
add_action('init', 'custom_order_list_endpoint');

// Step 2: Add the new endpoint to the My Account menu with the updated link
function custom_add_order_list_link_my_account($items)
{
    $items['my-orders'] = 'My Orders';
    return $items;
}
add_filter('woocommerce_account_menu_items', 'custom_add_order_list_link_my_account');

// Step 3: Endpoint content function to display the orders with delivery details
function custom_order_list_content()
{
    // Get the current user ID
    $user_id = get_current_user_id();

    // Fetch orders for the current user only (Newest first)
    $customer_orders = wc_get_orders(array(
        'customer' => $user_id,
        'limit' => -1,
        'orderby' => 'date',
        'order' => 'DESC',
    ));

    // Display the table with the user's orders
    echo '<h2>Your Orders</h2>';
    echo '<table class="shop_table shop_table_responsive my_account_orders">
            <thead>
                <tr>
                    <th class="order-id"><span>Order ID</span></th>
                    <th class="order-date"><span>Date</span></th>
                    <th class="order-status"><span>Status</span></th>
                    <th class="order-total"><span>Total</span></th>
                    <th class="order-delivery"><span>Delivery Times</span></th>
                    <th class="order-delivery-date"><span>Delivery Date</span></th>
                    <th class="order-reschedule"><span>Action</span></th>
                </tr>
            </thead>
            <tbody>';

    if (!empty($customer_orders)) {
        foreach ($customer_orders as $order) {
            $order_id = $order->get_id();
            $order_date = wc_format_datetime($order->get_date_created());
            $order_status = wc_get_order_status_name($order->get_status());
            $order_total = $order->get_formatted_order_total();

            // Initialize delivery details
            $delivery_times = '';
            $delivery_date = '';

            // Get order items
            foreach ($order->get_items() as $item_id => $item) {
                // Retrieve delivery metadata
                $item_delivery_times = $item->get_meta('_delivery_times', true);
                $item_delivery_date = $item->get_meta('_delivery_time', true);

                if (is_array($item_delivery_times)) {
                    $delivery_times .= implode(', ', array_map('esc_html', $item_delivery_times)) . '<br>';
                } elseif (!empty($item_delivery_times)) {
                    $delivery_times .= esc_html($item_delivery_times) . '<br>';
                }

                if (is_array($item_delivery_date)) {
                    $delivery_date .= implode(', ', array_map('esc_html', $item_delivery_date)) . '<br>';
                } elseif (!empty($item_delivery_date)) {
                    $delivery_date .= esc_html($item_delivery_date) . '<br>';
                }
            }

            // Display the order row
            echo '<tr>
                    <td class="order-id">' . esc_html($order_id) . '</td>
                    <td class="order-date">' . esc_html($order_date) . '</td>
                    <td class="order-status">' . esc_html($order_status) . '</td>
                    <td class="order-total">' . wp_kses_post($order_total) . '</td>
                    <td class="order-delivery">' . wp_kses_post($delivery_times) . '</td>
                    <td class="order-delivery-date">' . wp_kses_post($delivery_date) . '</td>
                    <td class="order-reschedule"><button class="reschedule-button" data-order-id="' . esc_attr($order_id) . '">Reschedule</button></td>
                  </tr>';
        }
    } else {
        echo '<tr><td colspan="7">No orders found.</td></tr>';
    }

    echo '</tbody></table>';

    // Reschedule Modal
    echo '<div id="reschedule-modal" style="display:none; position:fixed; top:50%; left:50%; transform:translate(-50%, -50%); background-color:white; padding:20px; border:1px solid #ccc; z-index:1000;">
            <h2>Reschedule Delivery</h2>
            <form id="reschedule-form">
                <label for="new-delivery-date">New Delivery Date:</label>
                <input type="date" id="new-delivery-date" name="new_delivery_date" required>

                <label for="new-delivery-time">New Delivery Time:</label>
                <select id="new-delivery-time" name="new_delivery_time" required>
                    <option value="">Select Time</option>
                    <option value="5:00AM TO 6:00AM">5:00AM TO 6:00AM</option>
                    <option value="6:00AM TO 7:00AM">6:00AM TO 7:00AM</option>
                    <option value="7:00AM TO 8:00AM">7:00AM TO 8:00AM</option>
                    <option value="9:00AM TO 10:00AM">9:00AM TO 10:00AM</option>
                </select>

                <input type="hidden" id="order-id" name="order_id">

                <button type="submit">Submit</button>
                <button type="button" onclick="document.getElementById(\'reschedule-modal\').style.display=\'none\';">Cancel</button>
            </form>
        </div>';

    // Inline JavaScript for handling the reschedule functionality
    ?>
    <script>
        jQuery(document).ready(function ($) {
            // Open the reschedule modal when the button is clicked
            $('.reschedule-button').on('click', function () {
                var orderId = $(this).data('order-id');
                $('#order-id').val(orderId);
                $('#reschedule-modal').show();
            });

            // Handle form submission
            $('#reschedule-form').on('submit', function (e) {
                e.preventDefault();
                var formData = $(this).serialize();

                $.post('<?php echo admin_url('admin-ajax.php'); ?>', formData + '&action=reschedule_order', function (response) {
                    if (response.success) {
                        alert(response.data);
                        location.reload(); // Reload the page to see the updated order details
                    } else {
                        alert(response.data);
                    }
                });
            });
        });
    </script>
    <?php
}

// AJAX handler for rescheduling
function handle_reschedule_order()
{
    // Check if the request is valid
    if (isset($_POST['order_id']) && isset($_POST['new_delivery_date']) && isset($_POST['new_delivery_time'])) {
        $order_id = intval($_POST['order_id']);
        $new_delivery_date = sanitize_text_field($_POST['new_delivery_date']);
        $new_delivery_time = sanitize_text_field($_POST['new_delivery_time']);

        // Update order metadata
        $order = wc_get_order($order_id);
        if ($order) {
            $order->update_meta_data('_delivery_time', $new_delivery_time);
            $order->update_meta_data('_delivery_date', $new_delivery_date);
            $order->save();

            // Return success response
            wp_send_json_success('Delivery rescheduled successfully.');
        }
    }
    wp_send_json_error('Failed to reschedule delivery.');
}
add_action('wp_ajax_reschedule_order', 'handle_reschedule_order');

add_action('woocommerce_account_my-orders_endpoint', 'custom_order_list_content');


// Step 4: Flush rewrite rules on theme activation to register the new endpoint
function custom_flush_rewrite_rules()
{
    custom_order_list_endpoint();
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'custom_flush_rewrite_rules');



// Reschedule
// 
// 
// Add a custom endpoint for rescheduling
add_action('init', function () {
    add_rewrite_endpoint('reschedule', EP_ROOT | EP_PAGES);
});

// Add the endpoint to My Account menu
add_filter('woocommerce_account_menu_items', function ($items) {
    $items['reschedule'] = __('Reschedule', 'woocommerce');
    return $items;
});

// Content for the reschedule endpoint
add_action('woocommerce_account_reschedule_endpoint', function () {
    // Display the form for rescheduling
    reschedule_delivery_dates_form();
});
function reschedule_delivery_dates_form()
{
    // Check if the user is logged in
    if (!is_user_logged_in()) {
        echo '<p>' . __('You need to be logged in to reschedule your delivery.', 'woocommerce') . '</p>';
        return;
    }

    // Fetch the user's orders
    $customer_orders = wc_get_orders(array(
        'customer' => get_current_user_id(),
        'status' => 'completed', // Change as needed
        'limit' => -1,
    ));

    if (!empty($customer_orders)) {
        echo '<form method="post" action="">';
        echo '<h2>' . __('Reschedule Delivery', 'woocommerce') . '</h2>';
        echo '<select name="order_id" required>';
        echo '<option value="">' . __('Select an Order', 'woocommerce') . '</option>';

        foreach ($customer_orders as $order) {
            echo '<option value="' . esc_attr($order->get_id()) . '">' . esc_html($order->get_order_number()) . '</option>';
        }

        echo '</select>';
        echo '<div id="new_delivery_date_container">';
        echo '<label for="new_delivery_date">' . __('New Delivery Date', 'woocommerce') . '</label>';
        echo '<input type="date" name="new_delivery_date" required />';

        echo '<label for="new_delivery_time">' . __('New Delivery Time', 'woocommerce') . '</label>';
        echo '<select name="new_delivery_time" required>
            <option value="">Select Time</option>
            <option value="5:00AM TO 6:00AM">5:00AM TO 6:00AM</option>
            <option value="6:00AM TO 7:00AM">6:00AM TO 7:00AM</option>
            <option value="7:00AM TO 8:00AM">7:00AM TO 8:00AM</option>
            <option value="9:00AM TO 10:00AM">9:00AM TO 10:00AM</option>
        </select>';
        echo '</div>';
        echo '<button type="submit" name="reschedule_submit">' . __('Reschedule', 'woocommerce') . '</button>';
        echo '</form>';
    } else {
        echo '<p>' . __('No completed orders found.', 'woocommerce') . '</p>';
    }
}
// Process the reschedule form submission
add_action('init', function () {
    if (isset($_POST['reschedule_submit'])) {
        $order_id = intval($_POST['order_id']);
        $new_delivery_date = sanitize_text_field($_POST['new_delivery_date']);
        $new_delivery_time = sanitize_text_field($_POST['new_delivery_time']);

        if ($order_id > 0) {
            // Update the order with new delivery details (meta data)
            update_post_meta($order_id, 'delivery_date', $new_delivery_date);
            update_post_meta($order_id, 'delivery_time', $new_delivery_time);

            // Optionally, add a notice
            wc_add_notice(__('Delivery date and time have been rescheduled successfully.', 'woocommerce'), 'success');
        } else {
            wc_add_notice(__('Invalid order selected.', 'woocommerce'), 'error');
        }
    }
});



// ==============================================
// RESCHEDULE DELIVERY ENDPOINT & PAGES (CONVERTED FROM CANCEL ORDER)
// ==============================================

// 1. Register endpoint
function custom_reschedule_delivery_endpoint()
{
    add_rewrite_endpoint('reschedule-delivery', EP_ROOT | EP_PAGES);
}
add_action('init', 'custom_reschedule_delivery_endpoint');

// 2. Add query vars
function custom_reschedule_delivery_query_vars($vars)
{
    $vars[] = 'reschedule-delivery';
    return $vars;
}
add_filter('query_vars', 'custom_reschedule_delivery_query_vars', 0);

// 3. Add to WooCommerce My Account menu (removes cancel-order)
function custom_add_reschedule_delivery_link_my_account($items)
{
    if (isset($items['cancel-order'])) {
        unset($items['cancel-order']);
    }
    $items['reschedule-delivery'] = __('Reschedule Delivery', 'woocommerce');
    return $items;
}
add_filter('woocommerce_account_menu_items', 'custom_add_reschedule_delivery_link_my_account');

// 4. Redirect legacy URL `/my-account/cancel-order/` to `/my-account/reschedule-delivery/`
function custom_redirect_legacy_cancel_order()
{
    if (strpos($_SERVER['REQUEST_URI'], '/my-account/cancel-order') !== false) {
        wp_safe_redirect(wc_get_endpoint_url('reschedule-delivery', '', wc_get_page_permalink('myaccount')), 301);
        exit;
    }
}
add_action('template_redirect', 'custom_redirect_legacy_cancel_order');

// 5. Display content
function custom_reschedule_delivery_content()
{
    if (!is_user_logged_in()) {
        echo '<div class="woocommerce-error">' . esc_html__('You must be logged in to reschedule deliveries.', 'woocommerce') . '</div>';
        return;
    }

    $user_id = get_current_user_id();

    $customer_orders = wc_get_orders(array(
        'customer' => $user_id,
        'status' => array('processing', 'on-hold', 'completed'),
        'limit' => -1,
    ));

    echo '<h2>' . esc_html__('Reschedule Delivery', 'woocommerce') . '</h2>';
    echo '<p>' . esc_html__('Manage your upcoming subscription delivery dates, times, and shipping addresses below.', 'woocommerce') . '</p>';

    $has_delivery_orders = false;

    if (!empty($customer_orders)) {
        ?>
        <style>
            .reschedule-grid {
                display: grid;
                grid-template-columns: 1fr;
                gap: 24px;
                margin-top: 20px;
            }

            .reschedule-card {
                background: #ffffff;
                border: 1px solid #eef2f6;
                border-radius: 12px;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
                padding: 24px;
                transition: transform 0.2s ease, box-shadow 0.2s ease;
                position: relative;
            }

            .reschedule-card:hover {
                box-shadow: 0 6px 24px rgba(0, 0, 0, 0.08);
            }

            .reschedule-card-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                border-bottom: 2px solid #f8fafc;
                padding-bottom: 12px;
                margin-bottom: 16px;
            }

            .reschedule-card-header h3 {
                margin: 0;
                font-size: 1.25rem;
                color: #0f172a;
            }

            .order-status-badge {
                font-size: 0.75rem;
                font-weight: 600;
                text-transform: uppercase;
                padding: 4px 10px;
                border-radius: 9999px;
            }

            .order-status-completed {
                background: #dcfce7;
                color: #15803d;
            }

            .order-status-processing {
                background: #dbeafe;
                color: #1d4ed8;
            }

            .order-status-on-hold {
                background: #fef3c7;
                color: #b45309;
            }

            .reschedule-section-title {
                font-weight: 600;
                color: #475569;
                margin: 16px 0 8px;
                font-size: 0.95rem;
            }

            .dates-badges-container {
                display: flex;
                flex-wrap: wrap;
                gap: 8px;
                margin-bottom: 12px;
                min-height: 40px;
                padding: 8px;
                border: 1px dashed #cbd5e1;
                border-radius: 8px;
                background: #f8fafc;
            }

            .date-badge {
                display: inline-flex;
                align-items: center;
                background: #e2e8f0;
                color: #1e293b;
                font-size: 0.85rem;
                font-weight: 500;
                padding: 4px 10px;
                border-radius: 6px;
                transition: background 0.15s ease;
            }

            .date-badge.future-date {
                background: #0ea5e9;
                color: #ffffff;
            }

            .date-badge .remove-date-btn {
                background: none;
                border: none;
                color: inherit;
                cursor: pointer;
                font-size: 1.1rem;
                font-weight: 700;
                margin-left: 6px;
                padding: 0 2px;
                line-height: 1;
                opacity: 0.7;
                transition: opacity 0.15s ease;
            }

            .date-badge .remove-date-btn:hover {
                opacity: 1;
            }

            .date-badge.past-date {
                background: #cbd5e1;
                color: #64748b;
            }

            .date-badge.past-date .remove-date-btn {
                display: none;
            }

            .reschedule-controls {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 16px;
                margin-top: 16px;
            }

            .reschedule-control-group {
                display: flex;
                flex-direction: column;
            }

            .reschedule-control-group label {
                font-size: 0.85rem;
                font-weight: 500;
                color: #64748b;
                margin-bottom: 6px;
            }

            .reschedule-control-group input {
                padding: 8px 12px;
                border: 1px solid #cbd5e1;
                border-radius: 6px;
                font-size: 0.9rem;
                background: #ffffff;
                color: #0f172a;
            }

            .reschedule-save-btn {
                margin-top: 20px;
                width: 100%;
                padding: 10px 16px;
                background: #1e293b !important;
                color: #ffffff !important;
                border: none;
                border-radius: 6px;
                font-weight: 600;
                cursor: pointer;
                transition: background 0.15s ease;
            }

            .reschedule-save-btn:hover {
                background: #0f172a !important;
            }

            .reschedule-save-btn:disabled {
                background: #94a3b8 !important;
                cursor: not-allowed;
            }

            .reschedule-message {
                margin-top: 12px;
                font-size: 0.9rem;
                padding: 8px 12px;
                border-radius: 6px;
                display: none;
            }

            .reschedule-message.success {
                background: #dcfce7;
                color: #15803d;
                border: 1px solid #bbf7d0;
            }

            .reschedule-message.error {
                background: #fee2e2;
                color: #b91c1c;
                border: 1px solid #fca5a5;
            }
        </style>

        <div class="reschedule-grid">
            <?php foreach ($customer_orders as $order) {
                $order_id = $order->get_id();
                $order_date = wc_format_datetime($order->get_date_created());
                $status = $order->get_status();
                $status_name = wc_get_order_status_name($status);

                foreach ($order->get_items() as $item_id => $item) {
                    $delivery_times = $item->get_meta('_delivery_times', true);
                    if (empty($delivery_times) || !is_array($delivery_times)) {
                        $legacy_dates = $item->get_meta('_delivery_date', true);
                        if (is_array($legacy_dates)) {
                            $delivery_times = $legacy_dates;
                        } elseif (!empty($legacy_dates)) {
                            $delivery_times = array_map('trim', explode(',', $legacy_dates));
                        } else {
                            continue;
                        }
                    }

                    $has_delivery_orders = true;
                    $delivery_time = $item->get_meta('_delivery_time', true);
                    $product_name = $item->get_name();
                    $today_str = date('Y-m-d');
                    ?>
                    <div class="reschedule-card" id="reschedule-card-<?php echo esc_attr($item_id); ?>">
                        <div class="reschedule-card-header">
                            <h3>
                                Order #<?php echo esc_html($order->get_order_number()); ?>
                                <span
                                    style="font-size: 0.9rem; font-weight: normal; color: #64748b;">(<?php echo esc_html($product_name); ?>)</span>
                            </h3>
                            <span class="order-status-badge order-status-<?php echo esc_attr($status); ?>">
                                <?php echo esc_html($status_name); ?>
                            </span>
                        </div>

                        <p style="font-size: 0.85rem; color: #64748b; margin-top: -8px; margin-bottom: 16px;">
                            Ordered on: <?php echo esc_html($order_date); ?>
                        </p>

                        <!-- Address Section -->
                        <div class="reschedule-section-title">Delivery Address</div>
                        <div class="current-address-box" id="current-address-<?php echo esc_attr($item_id); ?>"
                            style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px; font-size: 0.85rem; margin-bottom: 12px; color: #334155; text-align: left;">
                            <strong><?php echo esc_html($order->get_shipping_first_name() . ' ' . $order->get_shipping_last_name()); ?></strong><br>
                            <?php echo esc_html($order->get_shipping_address_1()); ?>                <?php echo $order->get_shipping_address_2() ? ', ' . esc_html($order->get_shipping_address_2()) : ''; ?><br>
                            <?php
                            $order_area = $order->get_meta('_shipping_area', true);
                            $order_landmark = $order->get_meta('_shipping_landmark', true);
                            $order_phone = $order->get_meta('_shipping_phone', true);
                            $order_addr_type = $order->get_meta('_shipping_address_type', true);

                            if ($order_area)
                                echo esc_html($order_area) . '<br>';
                            if ($order_landmark)
                                echo 'Landmark: ' . esc_html($order_landmark) . '<br>';
                            echo esc_html($order->get_shipping_city() . ', ' . $order->get_shipping_state() . ' ' . $order->get_shipping_postcode()) . '<br>';
                            if ($order_phone)
                                echo 'Phone: ' . esc_html($order_phone) . '<br>';
                            if ($order_addr_type)
                                echo '<span class="address-card-tag" style="background:#475569; color:#fff; font-size:10px; padding:2px 6px; border-radius:4px; font-weight:700; text-transform:uppercase; display:inline-block; margin-top:5px;">' . esc_html($order_addr_type) . '</span>';
                            ?>
                        </div>

                        <div class="reschedule-control-group" style="margin-bottom: 12px;">
                            <label for="change-address-<?php echo esc_attr($item_id); ?>">Change Delivery Address</label>
                            <select id="change-address-<?php echo esc_attr($item_id); ?>" class="change-address-select"
                                data-item-id="<?php echo esc_attr($item_id); ?>"
                                style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem; background: #fff;">
                                <option value=""><?php esc_html_e('Keep current address', 'woocommerce'); ?></option>
                                <?php
                                $user_addresses = wc_get_saved_multiple_addresses($user_id);
                                $current_address_id = $order->get_meta('_delivery_address_id', true);
                                foreach ($user_addresses as $addr) {
                                    $selected_attr = ($current_address_id === $addr['id']) ? 'selected' : '';
                                    echo '<option value="' . esc_attr($addr['id']) . '" ' . $selected_attr . '>' . esc_html($addr['address_name'] . ' - ' . $addr['name'] . ' (' . $addr['city'] . ')') . '</option>';
                                }
                                ?>
                            </select>
                        </div>

                        <div class="reschedule-section-title">Delivery Dates Schedule</div>
                        <div class="dates-badges-container" id="dates-container-<?php echo esc_attr($item_id); ?>">
                            <?php foreach ($delivery_times as $date):
                                $is_future = ($date >= $today_str);
                                $formatted_badge_date = date_i18n(get_option('date_format'), strtotime($date));
                                ?>
                                <span class="date-badge <?php echo $is_future ? 'future-date' : 'past-date'; ?>"
                                    data-date="<?php echo esc_attr($date); ?>">
                                    <?php echo esc_html($formatted_badge_date); ?>
                                    <?php if ($is_future): ?>
                                        <button type="button" class="remove-date-btn"
                                            onclick="removeDateBadge(this, '<?php echo esc_attr($item_id); ?>')">&times;</button>
                                    <?php endif; ?>
                                </span>
                            <?php endforeach; ?>
                        </div>

                        <input type="hidden" id="dates-input-<?php echo esc_attr($item_id); ?>"
                            name="delivery_dates[<?php echo esc_attr($item_id); ?>]"
                            value="<?php echo esc_attr(implode(',', $delivery_times)); ?>">

                        <div class="reschedule-controls">
                            <div class="reschedule-control-group">
                                <label for="add-date-<?php echo esc_attr($item_id); ?>">Add New Date</label>
                                <input type="text" class="datepicker-input" id="add-date-<?php echo esc_attr($item_id); ?>"
                                    placeholder="Select a date..." readonly data-item-id="<?php echo esc_attr($item_id); ?>">
                            </div>
                            <div class="reschedule-control-group">
                                <label for="time-<?php echo esc_attr($item_id); ?>">Delivery Time</label>
                                <select id="time-<?php echo esc_attr($item_id); ?>"
                                    name="delivery_time[<?php echo esc_attr($item_id); ?>]">
                                    <option value="">Select Time</option>
                                    <option value="5:00AM TO 6:00AM" <?php selected($delivery_time, '5:00AM TO 6:00AM'); ?>>5:00AM TO
                                        6:00AM</option>
                                    <option value="6:00AM TO 7:00AM" <?php selected($delivery_time, '6:00AM TO 7:00AM'); ?>>6:00AM TO
                                        7:00AM</option>
                                    <option value="7:00AM TO 8:00AM" <?php selected($delivery_time, '7:00AM TO 8:00AM'); ?>>7:00AM TO
                                        8:00AM</option>
                                    <option value="9:00AM TO 10:00AM" <?php selected($delivery_time, '9:00AM TO 10:00AM'); ?>>9:00AM TO
                                        10:00AM</option>
                                </select>
                            </div>
                        </div>

                        <div class="reschedule-message" id="message-<?php echo esc_attr($item_id); ?>"></div>

                        <button type="button" class="reschedule-save-btn"
                            onclick="saveDeliverySchedule('<?php echo esc_attr($order_id); ?>', '<?php echo esc_attr($item_id); ?>')">
                            Save Schedule
                        </button>
                    </div>
                    <?php
                }
            } ?>
        </div>

        <script type="text/javascript">
            jQuery(document).ready(function ($) {
                // Initialize Datepicker
                $('.datepicker-input').each(function () {
                    var itemId = $(this).data('item-id');
                    var $input = $(this);

                    $input.datepicker({
                        dateFormat: 'yy-mm-dd',
                        minDate: 1, // Disable past dates and today
                        beforeShowDay: function (date) {
                            var string = jQuery.datepicker.formatDate('yy-mm-dd', date);
                            var hiddenVal = $('#dates-input-' + itemId).val();
                            var currentDates = hiddenVal ? hiddenVal.split(',') : [];
                            return [currentDates.indexOf(string) === -1];
                        },
                        onSelect: function (dateText) {
                            addNewDateBadge(itemId, dateText);
                            $input.val(''); // Reset input
                        }
                    });
                });
            });

            function addNewDateBadge(itemId, dateText) {
                var $ = jQuery;
                var hiddenInput = $('#dates-input-' + itemId);
                var currentDates = hiddenInput.val() ? hiddenInput.val().split(',') : [];

                if (currentDates.indexOf(dateText) === -1) {
                    currentDates.push(dateText);
                    currentDates.sort();
                    hiddenInput.val(currentDates.join(','));
                    renderDateBadges(itemId, currentDates);
                }
            }

            function removeDateBadge(button, itemId) {
                var $ = jQuery;
                var badge = $(button).parent();
                var dateToRemove = badge.data('date');
                var hiddenInput = $('#dates-input-' + itemId);
                var currentDates = hiddenInput.val().split(',');

                var index = currentDates.indexOf(dateToRemove);
                if (index > -1) {
                    currentDates.splice(index, 1);
                    hiddenInput.val(currentDates.join(','));
                    renderDateBadges(itemId, currentDates);
                }
            }

            function renderDateBadges(itemId, dates) {
                var $ = jQuery;
                var container = $('#dates-container-' + itemId);
                container.empty();

                if (dates.length === 0 || (dates.length === 1 && dates[0] === "")) {
                    container.append('<span style="color:#64748b; font-size:0.9rem;">No dates selected. Please add one.</span>');
                    return;
                }

                var todayStr = new Date().toISOString().slice(0, 10);

                dates.forEach(function (date) {
                    if (!date) return;
                    var isFuture = (date >= todayStr);

                    var dateObj = new Date(date + 'T00:00:00');
                    var formattedDate = date;
                    if (!isNaN(dateObj.getTime())) {
                        var options = { year: 'numeric', month: 'long', day: 'numeric' };
                        formattedDate = dateObj.toLocaleDateString(undefined, options);
                    }

                    var badgeClass = isFuture ? 'date-badge future-date' : 'date-badge past-date';
                    var closeBtn = isFuture ? ' <button type="button" class="remove-date-btn" onclick="removeDateBadge(this, \'' + itemId + '\')">&times;</button>' : '';

                    container.append(
                        '<span class="' + badgeClass + '" data-date="' + date + '">' +
                        formattedDate + closeBtn +
                        '</span>'
                    );
                });
            }

            function saveDeliverySchedule(orderId, itemId) {
                var $ = jQuery;
                var dates = $('#dates-input-' + itemId).val();
                var time = $('#time-' + itemId).val();
                var addressId = $('#change-address-' + itemId).val();
                var messageDiv = $('#message-' + itemId);
                var saveBtn = $('#reschedule-card-' + itemId + ' .reschedule-save-btn');

                messageDiv.hide().removeClass('success error');
                saveBtn.prop('disabled', true).text('Saving...');

                $.ajax({
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    type: 'POST',
                    data: {
                        action: 'reschedule_delivery_schedule',
                        nonce: '<?php echo wp_create_nonce("reschedule_delivery_nonce"); ?>',
                        order_id: orderId,
                        item_id: itemId,
                        delivery_dates: dates,
                        delivery_time: time,
                        delivery_address_id: addressId
                    },
                    success: function (response) {
                        saveBtn.prop('disabled', false).text('Save Schedule');
                        if (response.success) {
                            messageDiv.addClass('success').text(response.data).fadeIn();
                            setTimeout(function () {
                                location.reload();
                            }, 1500);
                        } else {
                            messageDiv.addClass('error').text(response.data).fadeIn();
                        }
                    },
                    error: function () {
                        saveBtn.prop('disabled', false).text('Save Schedule');
                        messageDiv.addClass('error').text('An error occurred. Please try again.').fadeIn();
                    }
                });
            }
        </script>
        <?php
    }

    if (!$has_delivery_orders) {
        echo '<div class="woocommerce-info">' . esc_html__('You do not have any active subscriptions with scheduled deliveries.', 'woocommerce') . '</div>';
    }
}
add_action('woocommerce_account_reschedule-delivery_endpoint', 'custom_reschedule_delivery_content');

// 6. Handle the AJAX request to update the delivery schedule
function handle_reschedule_delivery_schedule()
{
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'reschedule_delivery_nonce')) {
        wp_send_json_error(__('Security verification failed.', 'woocommerce'));
    }

    if (!is_user_logged_in()) {
        wp_send_json_error(__('Unauthorized access.', 'woocommerce'));
    }

    $order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;
    $item_id = isset($_POST['item_id']) ? intval($_POST['item_id']) : 0;
    $delivery_dates_raw = isset($_POST['delivery_dates']) ? sanitize_text_field($_POST['delivery_dates']) : '';
    $delivery_time = isset($_POST['delivery_time']) ? sanitize_text_field($_POST['delivery_time']) : '';
    $delivery_address_id = isset($_POST['delivery_address_id']) ? sanitize_text_field($_POST['delivery_address_id']) : '';

    if (!$order_id || !$item_id) {
        wp_send_json_error(__('Invalid request parameters.', 'woocommerce'));
    }

    $order = wc_get_order($order_id);
    if (!$order || $order->get_customer_id() !== get_current_user_id()) {
        wp_send_json_error(__('Order not found.', 'woocommerce'));
    }

    $item = $order->get_item($item_id);
    if (!$item) {
        wp_send_json_error(__('Order item not found.', 'woocommerce'));
    }

    // Parse into array
    $dates_array = array_filter(array_map('trim', explode(',', $delivery_dates_raw)));
    if (empty($dates_array)) {
        wp_send_json_error(__('A subscription must have at least one scheduled delivery date.', 'woocommerce'));
    }

    // Check that newly added dates are not in the past
    $delivery_times_old = $item->get_meta('_delivery_times', true);
    if (!is_array($delivery_times_old)) {
        $delivery_times_old = array();
    }

    $today_str = date('Y-m-d');
    foreach ($dates_array as $date) {
        if (!in_array($date, $delivery_times_old) && $date < $today_str) {
            wp_send_json_error(__('Newly added delivery dates must be in the future.', 'woocommerce'));
        }
    }

    // Save updated dates and time to item meta
    $item->update_meta_data('_delivery_times', $dates_array);
    $item->update_meta_data('_delivery_time', $delivery_time);

    // Save human-readable meta fields
    $formatted_dates = array();
    foreach ($dates_array as $date) {
        $timestamp = strtotime($date);
        if ($timestamp) {
            $formatted_dates[] = date_i18n(get_option('date_format'), $timestamp);
        } else {
            $formatted_dates[] = $date;
        }
    }
    $item->update_meta_data(__('Selected Dates', 'woocommerce'), implode(', ', $formatted_dates));

    $formatted_time = $delivery_time;
    $time_timestamp = strtotime($delivery_time);
    if ($time_timestamp) {
        $formatted_time = date_i18n(get_option('time_format'), $time_timestamp);
    }
    $item->update_meta_data(__('Delivery Time', 'woocommerce'), $formatted_time);

    // Save item
    $item->save();

    // Update order level meta _delivery_dates
    $order->update_meta_data('_delivery_dates', implode(', ', $dates_array));

    // Update address if requested
    $address_note = '';
    if (!empty($delivery_address_id)) {
        $addresses = wc_get_saved_multiple_addresses(get_current_user_id());
        $selected_addr = null;
        foreach ($addresses as $addr) {
            if ($addr['id'] === $delivery_address_id) {
                $selected_addr = $addr;
                break;
            }
        }

        if ($selected_addr) {
            $parts = explode(' ', trim($selected_addr['name']), 2);
            $first_name = isset($parts[0]) ? $parts[0] : '';
            $last_name = isset($parts[1]) ? $parts[1] : '';

            $order->set_shipping_first_name($first_name);
            $order->set_shipping_last_name($last_name);
            $order->set_shipping_address_1($selected_addr['address_1']);
            $order->set_shipping_address_2($selected_addr['address_2']);
            $order->set_shipping_city($selected_addr['city']);
            $order->set_shipping_postcode($selected_addr['postcode']);
            $order->set_shipping_state($selected_addr['state']);
            $order->set_shipping_country($selected_addr['country']);

            $order->update_meta_data('_shipping_phone', $selected_addr['phone']);
            $order->update_meta_data('_shipping_area', $selected_addr['area']);
            $order->update_meta_data('_shipping_landmark', $selected_addr['landmark']);
            $order->update_meta_data('_shipping_address_type', $selected_addr['address_type']);
            $order->update_meta_data('_delivery_address_id', $selected_addr['id']);

            $address_note = sprintf(
                __(' Shipping address updated to: %s.', 'woocommerce'),
                esc_html($selected_addr['address_type'] . ' - ' . $selected_addr['name'] . ' (' . $selected_addr['city'] . ')')
            );
        }
    }

    $order->save();

    // Add order note about the schedule change
    $note = sprintf(
        __('Delivery schedule updated by customer. New Dates: %s, New Time: %s.%s', 'woocommerce'),
        implode(', ', $formatted_dates),
        $formatted_time,
        $address_note
    );
    $order->add_order_note($note);

    wp_send_json_success(__('Delivery schedule updated successfully.', 'woocommerce'));
}
add_action('wp_ajax_reschedule_delivery_schedule', 'handle_reschedule_delivery_schedule');

// 7. Automatic rewrite rules flush transient check
add_action('init', function () {
    if (!get_transient('flush_rewrite_rules_addresses_v5')) {
        custom_reschedule_delivery_endpoint();
        custom_delivery_addresses_endpoint();
        flush_rewrite_rules();
        set_transient('flush_rewrite_rules_addresses_v5', true, DAY_IN_SECONDS);
    }
}, 99);








// Add a new endpoint for 'Manage Delivery Dates' and 'Delivery Calendar'
function add_delivery_dates_endpoint()
{
    add_rewrite_endpoint('manage-delivery-dates', EP_ROOT | EP_PAGES);
    add_rewrite_endpoint('delivery-calendar', EP_ROOT | EP_PAGES);
}
add_action('init', 'add_delivery_dates_endpoint');

// Add query vars for the endpoint
function add_delivery_dates_query_vars($vars)
{
    $vars[] = 'manage-delivery-dates';
    $vars[] = 'delivery-calendar';
    return $vars;
}
add_filter('query_vars', 'add_delivery_dates_query_vars', 0);

// Add the endpoint link to My Account menu
function add_delivery_dates_link_my_account($items)
{
    // Insert after dashboard or orders if we want, or just append
    $new_items = array();
    foreach ($items as $key => $value) {
        $new_items[$key] = $value;
        if ($key === 'orders') {
            $new_items['delivery-calendar'] = 'My Delivery Calendar';
            $new_items['manage-delivery-dates'] = 'Manage Delivery Dates';
        }
    }
    // Fallback if orders is missing
    if (!isset($new_items['delivery-calendar'])) {
        $new_items['delivery-calendar'] = 'My Delivery Calendar';
        $new_items['manage-delivery-dates'] = 'Manage Delivery Dates';
    }
    return $new_items;
}
add_filter('woocommerce_account_menu_items', 'add_delivery_dates_link_my_account');

// Display content for the Manage Delivery Dates endpoint
function manage_delivery_dates_content()
{
    wc_get_template('myaccount/manage-delivery-dates.php');
}
add_action('woocommerce_account_manage-delivery-dates_endpoint', 'manage_delivery_dates_content');

// Display content for the Delivery Calendar endpoint
function delivery_calendar_content()
{
    $template_path = get_stylesheet_directory() . '/customer-delivery-calendar.php';
    if (file_exists($template_path)) {
        include $template_path;
    }
}
add_action('woocommerce_account_delivery-calendar_endpoint', 'delivery_calendar_content');




function handle_delivery_date_update()
{
    if (isset($_POST['update_individual_delivery_dates'])) {
        $order_id = intval($_POST['update_individual_delivery_dates']);
        $order = wc_get_order($order_id);

        if (!$order)
            return;

        foreach ($_POST['date_action'] as $item_id => $actions) {
            $item = $order->get_item($item_id);
            if ($item) {
                $delivery_dates = $item->get_meta('_delivery_times') ?: [];
                $new_delivery_dates = [];
                $status_changed = false;

                foreach ($actions as $old_date => $action) {
                    if ($action === 'keep') {
                        $new_delivery_dates[] = $old_date;
                    } elseif ($action === 'skipped' || $action === 'cancelled') {
                        // Keep the date but update its status
                        $new_delivery_dates[] = $old_date;
                        $item->update_meta_data('_delivery_status_' . $old_date, $action);
                        $status_changed = true;
                    } elseif ($action === 'rescheduled') {
                        $new_date = sanitize_text_field($_POST['reschedule_date'][$item_id][$old_date]);
                        if (!empty($new_date)) {
                            // Update old date status to rescheduled (don't keep it in the active list of delivery_times unless required, but let's keep it for history)
                            $item->update_meta_data('_delivery_status_' . $old_date, 'rescheduled');

                            // Add new date
                            $new_delivery_dates[] = $old_date; // Keep old in history
                            if (!in_array($new_date, $new_delivery_dates)) {
                                $new_delivery_dates[] = $new_date;
                            }
                            $item->update_meta_data('_delivery_status_' . $new_date, 'pending');
                            $status_changed = true;
                        } else {
                            // If no new date provided, just keep
                            $new_delivery_dates[] = $old_date;
                        }
                    }
                }

                $new_delivery_dates = array_unique($new_delivery_dates);
                sort($new_delivery_dates);
                $item->update_meta_data('_delivery_times', $new_delivery_dates);

                $new_delivery_time = sanitize_text_field($_POST['new_delivery_time'][$item_id]);
                if (!empty($new_delivery_time)) {
                    $item->update_meta_data('_delivery_time', $new_delivery_time);
                }

                $item->save();
            }
        }

        $order->save();
        wc_add_notice('Delivery schedule updated successfully!', 'success');
        wp_redirect(wc_get_endpoint_url('manage-delivery-dates', '', wc_get_page_permalink('myaccount')));
        exit;
    }
}
add_action('template_redirect', 'handle_delivery_date_update');



// Manage subscription Reschedule
// 
// 
// Enqueue jQuery and the inline custom script
function enqueue_custom_scripts()
{
    wp_enqueue_script('jquery'); // Ensure jQuery is loaded
    wp_enqueue_style('jquery-ui-style', 'https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css'); // Load jQuery UI CSS
    wp_enqueue_script('jquery-ui-datepicker'); // Load jQuery UI Datepicker
}
add_action('wp_enqueue_scripts', 'enqueue_custom_scripts');

function display_user_subscription_details()
{
    $user_id = get_current_user_id();
    $customer_orders = wc_get_orders(array(
        'customer' => $user_id,
        'status' => array('completed', 'processing', 'on-hold')
    ));

    $disabled_dates = []; // Array to hold already selected dates

    // Wrap all orders in a grid container
    echo '<div class="orders-grid">';

    if (!empty($customer_orders)) {
        foreach ($customer_orders as $order) {
            echo '<div class="order-details-container">';
            echo '<h4>' . __('Order #', 'woocommerce') . $order->get_order_number() . '</h4>';
            echo '<p>' . __('Order Date:', 'woocommerce') . ' ' . wc_format_datetime($order->get_date_created()) . '</p>';

            // Loop through each item in the order
            foreach ($order->get_items() as $item_id => $item) {
                $subscription_type = $item->get_meta('_subscription_type', true);
                if (empty($subscription_type)) {
                    $subscription_type = $item->get_meta('Subscription Schedule', true);
                }
                $delivery_times = $item->get_meta('_delivery_times', true); // Array of delivery dates
                if (empty($delivery_times)) {
                    $selected_dates = $item->get_meta('Selected Dates', true);
                    if ($selected_dates) {
                        // Dates might be saved like 'June 19, 2026, June 20, 2026' or '2026-06-19, 2026-06-20'
                        $delivery_times = array_map('trim', explode(',', $selected_dates));
                    }
                }
                $delivery_time = $item->get_meta('_delivery_time', true);
                if (empty($delivery_time)) {
                    $delivery_time = $item->get_meta('Delivery Time', true);
                }

                // Collect disabled dates
                if ($delivery_times && is_array($delivery_times)) {
                    $disabled_dates = array_merge($disabled_dates, $delivery_times);
                }

                if ($subscription_type) {
                    echo '<p><strong>' . __('Subscription Type:', 'woocommerce') . '</strong> ' . esc_html($subscription_type) . '</p>';
                }

                echo '<button class="view-dates-btn" data-item-id="' . esc_attr($item_id) . '">' . __('View Dates', 'woocommerce') . '</button>';

                // Display Delivery Dates and Times with Edit Option
                if ($delivery_times && is_array($delivery_times)) {
                    echo '<div class="delivery-schedule" id="delivery-schedule-' . esc_attr($item_id) . '" style="display: none;">';
                    echo '<p><strong>' . __('Delivery Schedule:', 'woocommerce') . '</strong></p>';
                    echo '<ul>';
                    foreach ($delivery_times as $index => $date) {
                        echo '<li>';
                        echo esc_html($date) . ' at ' . esc_html($delivery_time);

                        // Button to edit date
                        echo ' <button class="edit-delivery-date-btn" data-item-id="' . esc_attr($item_id) . '" data-date-index="' . esc_attr($index) . '">Edit Date</button>';
                        echo '</li>';
                    }
                    echo '</ul>';
                    echo '</div>';
                } else {
                    echo '<p>' . __('No delivery dates selected.', 'woocommerce') . '</p>';
                }
            }
            echo '</div>'; // Close order-details-container
        }
    } else {
        echo '<p>' . __('No subscription orders found.', 'woocommerce') . '</p>';
    }

    echo '</div>'; // Close orders-grid

    // Add a container for the date picker modal
    echo '<div id="date-picker-modal" style="display:none; position:fixed; z-index:1000; top:0; left:0; right:0; bottom:0; background: rgba(0, 0, 0, 0.7);">
            <div style="background: #fff; padding: 20px; border-radius: 5px; width: 300px; margin: 100px auto; position: relative;">
                <h4 id="sel">' . __('Select a New Date', 'woocommerce') . '</h4>
                <input type="text" id="new-delivery-date" readonly style="width: 100%; margin-bottom: 10px;">
                <button id="save-new-date" style="margin-top: 10px;">' . __('Save Date', 'woocommerce') . '</button>
                <button id="close-modal" style="position: absolute; top: 10px; right: 10px;">&times;</button>
            </div>
          </div>';

    // Print JavaScript variable for disabled dates
    echo '<script type="text/javascript">var disabledDeliveryDates = ' . json_encode($disabled_dates) . ';</script>';
}

// AJAX function to update the delivery date
function update_delivery_date()
{
    // Check if necessary data is set
    if (isset($_POST['item_id'], $_POST['date_index'], $_POST['new_date'])) {
        $item_id = sanitize_text_field($_POST['item_id']);
        $date_index = (int) $_POST['date_index'];
        $new_date = sanitize_text_field($_POST['new_date']);

        // Get the order item
        $order_item = new WC_Order_Item_Product($item_id);

        // Get existing delivery dates
        $delivery_times = $order_item->get_meta('_delivery_times', true);

        // Update the specific date
        if (is_array($delivery_times) && isset($delivery_times[$date_index])) {
            $delivery_times[$date_index] = $new_date;

            // Save the updated dates
            $order_item->update_meta_data('_delivery_times', $delivery_times);
            $order_item->save();

            wp_send_json_success(['message' => __('Date updated successfully.', 'woocommerce')]);
        } else {
            wp_send_json_error(['message' => __('Invalid data.', 'woocommerce')]);
        }
    } else {
        wp_send_json_error(['message' => __('Missing data.', 'woocommerce')]);
    }
}

add_action('wp_ajax_update_delivery_date', 'update_delivery_date');
add_action('wp_ajax_nopriv_update_delivery_date', 'update_delivery_date');

function custom_inline_script()
{
    ?>
    <script type="text/javascript">
        jQuery(document).ready(function ($) {
            let currentItemId, currentDateIndex;


            // Toggle visibility of delivery schedule when "View Dates" is clicked
            $('.view-dates-btn').on('click', function () {
                let itemId = $(this).data('item-id');
                $('#delivery-schedule-' + itemId).toggle();
            });
            // Show the date picker modal when "Edit Date" is clicked
            $('.edit-delivery-date-btn').on('click', function () {
                currentItemId = $(this).data('item-id');
                currentDateIndex = $(this).data('date-index');

                // Initialize datepicker
                $('#new-delivery-date').datepicker({
                    dateFormat: 'yy-mm-dd',
                    beforeShowDay: function (date) {
                        let string = jQuery.datepicker.formatDate('yy-mm-dd', date);
                        // Disable today and past dates
                        let today = jQuery.datepicker.formatDate('yy-mm-dd', new Date());
                        return [string > today && disabledDeliveryDates.indexOf(string) === -1];
                    },
                    onSelect: function () {
                        $(this).focus(); // Keep input focused
                    }
                });

                $('#date-picker-modal').show();
            });

            // Hide modal and save new date
            $('#save-new-date').on('click', function () {
                let newDate = $('#new-delivery-date').val();

                if (newDate) {
                    $.ajax({
                        url: '<?php echo admin_url("admin-ajax.php"); ?>',
                        type: 'POST',
                        data: {
                            action: 'update_delivery_date',
                            item_id: currentItemId,
                            date_index: currentDateIndex,
                            new_date: newDate,
                        },
                        success: function (response) {
                            if (response.success) {
                                alert(response.data.message); // Display the success message
                            } else {
                                alert(response.data.message); // Display the error message
                            }
                            location.reload();
                        },
                        error: function () {
                            alert('Failed to update date.');
                        }
                    });
                }

                $('#date-picker-modal').hide();
            });

            // Close modal
            $('#close-modal').on('click', function () {
                $('#date-picker-modal').hide();
            });
        });
    </script>
    <?php
}
add_action('wp_footer', 'custom_inline_script');

// Add a custom My Account endpoint
function add_custom_my_account_endpoint()
{
    add_rewrite_endpoint('subscription-details', EP_ROOT | EP_PAGES);
}
add_action('init', 'add_custom_my_account_endpoint');

// Display content for the custom endpoint
function custom_my_account_content()
{
    display_user_subscription_details();
}
add_action('woocommerce_account_subscription-details_endpoint', 'custom_my_account_content');
function add_subscription_details_to_menu($items)
{
    $items['subscription-details'] = __('Subscription Details', 'woocommerce');
    return $items;
}
add_filter('woocommerce_account_menu_items', 'add_subscription_details_to_menu');


// Inline custom styles for order container
function enqueue_custom_styles()
{
    wp_enqueue_style('jquery-ui-style', 'https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css'); // Load jQuery UI CSS

    // Inline custom styles
    wp_add_inline_style('jquery-ui-style', '
        .order-details-container {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            background-color: #f9f9f9;
        }

        /* Other styles for buttons and modal */
        .view-dates-btn, .edit-delivery-date-btn, #save-new-date, #close-modal {
            background-color: #0073aa;
            color: #fff;
            border: none;
            padding: 8px 12px;
            cursor: pointer;
            border-radius: 4px;
            font-size: 14px;
        }

        .view-dates-btn:hover, .edit-delivery-date-btn:hover, #save-new-date:hover, #close-modal:hover {
            background-color: #005a87;
        }

        .delivery-schedule {
            border: 1px solid #ddd;
            padding: 15px;
            background-color: #f1f1f1;
            border-radius: 5px;
            margin-top: 10px;
        }
        
        /* Modal and input styling */
        #date-picker-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.7);
            z-index: 1000;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        #date-picker-modal > div {
            background: #fff;
            padding: 20px;
            width: 320px;
            border-radius: 8px;
            text-align: center;
            position: relative;
        }
		
		.orders-grid {
			display: flex;
			flex-wrap: wrap;
			gap: 20px;
		}


    ');
}
add_action('wp_enqueue_scripts', 'enqueue_custom_styles');




// Reschedule



// Checkout page Apartment settings 
// 
// 
// Add Apartment field to the billing section of the checkout page
add_filter('woocommerce_billing_fields', 'add_billing_apartment_field');
function add_billing_apartment_field($fields)
{
    // Retrieve saved apartments from options
    $apartments = get_option('custom_apartments', []);

    // Set up options for the apartment dropdown
    $field_options = ['' => __('Select Apartment', 'woocommerce')];
    foreach ($apartments as $apartment) {
        $field_options[$apartment] = $apartment;
    }

    // Add Apartment field to billing fields
    $fields['billing_apartment'] = [
        'type' => 'select',
        'label' => __('Apartment', 'woocommerce'),
        'required' => true,
        'class' => ['form-row-wide'],
        'options' => $field_options,
        'default' => get_user_meta(get_current_user_id(), 'apartment', true) // Prefill with user's saved apartment if available
    ];

    return $fields;
}

// Save Apartment selection to the order meta on checkout
add_action('woocommerce_checkout_update_order_meta', 'save_billing_apartment_to_order');
function save_billing_apartment_to_order($order_id)
{
    if (!empty($_POST['billing_apartment'])) {
        update_post_meta($order_id, '_billing_apartment', sanitize_text_field($_POST['billing_apartment']));
    }
}

// Display Apartment in the WooCommerce admin order details
add_action('woocommerce_admin_order_data_after_billing_address', 'display_billing_apartment_in_admin_order_meta', 10, 1);
function display_billing_apartment_in_admin_order_meta($order)
{
    $apartment = get_post_meta($order->get_id(), '_billing_apartment', true);
    if ($apartment) {
        echo '<p><strong>' . __('Apartment', 'woocommerce') . ':</strong> ' . esc_html($apartment) . '</p>';
    }
}

// Include Apartment field in order emails
add_filter('woocommerce_email_order_meta_keys', 'add_billing_apartment_to_email_order_meta_keys');
function add_billing_apartment_to_email_order_meta_keys($keys)
{
    $keys[] = '_billing_apartment';
    return $keys;
}

// Prefill the Apartment field in checkout for returning customers
add_filter('woocommerce_checkout_get_value', 'prefill_apartment_in_checkout', 10, 2);
function prefill_apartment_in_checkout($input, $key)
{
    if ($key === 'billing_apartment' && is_user_logged_in()) {
        $user_id = get_current_user_id();
        return get_user_meta($user_id, 'apartment', true);
    }
    return $input;
}


// Assign orders
// 
// 
add_action('wp_ajax_assign_order_to_staff', 'assign_order_to_staff');
function assign_order_to_staff()
{
    check_ajax_referer('assign_order_nonce', 'security');

    $order_id = intval($_POST['order_id']);
    $staff_id = intval($_POST['staff_id']);

    if ($order_id && $staff_id) {
        update_post_meta($order_id, '_assigned_staff', $staff_id);
        wp_send_json_success(array('message' => 'Order assigned to staff successfully.'));
    } else {
        wp_send_json_error(array('message' => 'Failed to assign order to staff.'));
    }
}


add_action('woocommerce_order_status_changed', 'clear_order_cache_on_status_change', 10, 1);

function clear_order_cache_on_status_change($order_id)
{
    if ($order_id) {
        wp_cache_delete($order_id, 'orders');
    }
}

// ---------------------------------------------------------------
// FIX: Auto-move orders from 'pending' to 'processing' after checkout.
// This ensures COD/subscription orders never stay stuck as "Pending"
// and display the correct status in My Orders and on the PDF invoice.
// ---------------------------------------------------------------
add_action('woocommerce_thankyou', 'custom_auto_process_pending_order', 5, 1);
function custom_auto_process_pending_order($order_id)
{
    if (!$order_id) return;

    $order = wc_get_order($order_id);
    if (!$order) return;

    // Only act when the order is still 'pending' - never downgrade already-processed orders
    if ($order->get_status() === 'pending') {
        $order->update_status('processing', __('Order automatically moved to processing after checkout.', 'woocommerce'));
    }
}



// Shortcode to Display the Subscription Form
function custom_subscription_form()
{
    ob_start();
    ?>
    <h3>Manage Subscription Options</h3>
    <form id="add_subscription_form" method="post">
        <label for="subscription_days">Enter Subscription Days:</label>
        <input type="number" id="subscription_days" name="subscription_days" min="1" required>

        <button type="submit" name="submit_subscription">Add Subscription</button>
    </form>

    <?php
    // Handle Form Submission
    if (isset($_POST['submit_subscription']) && !empty($_POST['subscription_days'])) {
        $subscription_days = intval($_POST['subscription_days']);

        if ($subscription_days > 0) {
            // Get existing subscriptions from the options table
            $existing_subscriptions = get_option('custom_subscription_days', []);

            // Add the new subscription to the list if it does not already exist
            if (!in_array($subscription_days, $existing_subscriptions)) {
                $existing_subscriptions[] = $subscription_days;
                update_option('custom_subscription_days', $existing_subscriptions);
                echo "<p style='color: green;'>Subscription for {$subscription_days} days added successfully!</p>";
            } else {
                echo "<p style='color: red;'>Subscription for {$subscription_days} days already exists.</p>";
            }
        }
    }

    // Display the list of existing subscriptions
    $subscriptions = get_option('custom_subscription_days', []);
    if (!empty($subscriptions)) {
        echo "<h4>Current Subscription Options:</h4><ul>";
        foreach ($subscriptions as $days) {
            echo "<li>{$days} Days</li>";
        }
        echo "</ul>";
    }

    return ob_get_clean();
}
add_shortcode('custom_subscription_form', 'custom_subscription_form');




// Register 'Course' Custom Post Type
function create_course_post_type()
{
    register_post_type('course', array(
        'labels' => array(
            'name' => 'Courses',
            'singular_name' => 'Course',
            'add_new_item' => 'Add New Course',
            'edit_item' => 'Edit Course',
        ),
        'public' => true,
        'has_archive' => true,
        'supports' => array('title', 'editor', 'thumbnail'),
        'show_in_rest' => true,
        'menu_icon' => 'dashicons-welcome-learn-more',
    ));
}
add_action('init', 'create_course_post_type');


// Mobile Bottom Menu
// 
// Enqueue Font Awesome CSS
function enqueue_font_awesome()
{
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css', array(), null);
}
add_action('wp_enqueue_scripts', 'enqueue_font_awesome');

// Enqueue CSS and JS for the bottom menu
function enqueue_bottom_menu_assets()
{
    wp_enqueue_style('bottom-menu-style', get_template_directory_uri() . '/bottom-menu.css');
    wp_enqueue_script('bottom-menu-script', get_template_directory_uri() . '/bottom-menu.js', array('jquery'), null, true);
}
add_action('wp_enqueue_scripts', 'enqueue_bottom_menu_assets');

function enqueue_font_awesome_direct()
{
    echo '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">';
}
add_action('wp_head', 'enqueue_font_awesome_direct');


// Add the bottom menu HTML
function add_bottom_menu()
{
    ?>
    <div class="bottom-menu">
        <a href="<?php echo site_url('/'); ?>" class="menu-item">
            <i class="fas fa-home"></i>
            <span>Home</span>
        </a>
        <a href="<?php echo site_url('/products/'); ?>" class="menu-item">
            <i class="fas fa-shopping-cart"></i>
            <span>Shop</span>
        </a>
        <a href="<?php echo site_url('/cart/'); ?>" class="menu-item">
            <i class="fas fa-credit-card"></i>
            <span>Cart</span>
        </a>
        <!--         <a href="<?php echo site_url('/wishlist/'); ?>" class="menu-item">
            <i class="fas fa-heart"></i>
            <span>Wishlist</span>
        </a> -->
        <a href="<?php echo site_url('/my-account/'); ?>" class="menu-item">
            <i class="fas fa-user"></i>
            <span>Account</span>
        </a>
    </div>
    <?php
}
add_action('wp_footer', 'add_bottom_menu');

// Add inline styles for the bottom menu
function add_bottom_menu_inline_styles()
{
    ?>
    <style>
        .bottom-menu {
            position: fixed;
            bottom: 0;
            width: 100%;
            background-color: #fff;
            border-top: 1px solid #ddd;
            display: flex;
            justify-content: space-around;
            box-shadow: 0 -2px 5px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }

        .bottom-menu .menu-item {
            flex: 1;
            text-align: center;
            padding: 10px 0;
            color: #333;
            font-size: 14px;
            text-decoration: none;
        }

        .bottom-menu .menu-item i {
            font-size: 20px;
            margin-bottom: 5px;
            display: block;
        }

        .bottom-menu .menu-item:hover {
            background-color: #f8f8f8;
        }
    </style>
    <?php
}
add_action('wp_head', 'add_bottom_menu_inline_styles');


// offer Percentage display
// 

function display_offer_badge()
{
    global $post;

    // Get the offer percentage
    $offer = get_post_meta($post->ID, '_product_offer', true);

    if (!empty($offer)) {
        echo '<span class="offer-badge"> ' . esc_html($offer) . '% OFF</span>';
    }
}
add_action('woocommerce_before_shop_loop_item_title', 'display_offer_badge', 10);
function display_stock_status()
{
    global $post;

    // Get the stock status
    $stock_status = get_post_meta($post->ID, '_stock_status', true);

    if ($stock_status == 'instock') {
        echo '<span class="stock-status in-stock">In Stock</span>';
    } elseif ($stock_status == 'outofstock') {
        echo '<span class="stock-status out-of-stock">Out of Stock</span>';
    }
}
add_action('woocommerce_after_shop_loop_item', 'display_stock_status', 10);






































function popup_after_successful_order()
{

    // Show popup only on WooCommerce order received page
    if (function_exists('is_order_received_page') && is_order_received_page()) {
        ?>
        <div id="orderPopup" style="
            display:none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.6);
            z-index: 999999;
        ">
            <div style="
                width: 90%;
                max-width: 350px;
                background: #fff;
                padding: 20px;
                border-radius: 12px;
                text-align: center;
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                font-family: Arial;
            ">

                <!-- ✅ Updated Logo -->
                <img src="https://coconutmakers.com/wp-content/uploads/2024/10/Main-logo-222.png" alt="Coconut Makers Logo"
                    style="max-width: 120px; margin-bottom: 12px;">

                <h2>Order Placed Successfully</h2>
                <p style="font-size: 16px;">
                    You'll receive the order <br>by tomorrow morning.
                </p>
                <button onclick="closeOrderPopup()" style="
                    padding: 10px 20px;
                    border: none;
                    background: #28a745;
                    color: #fff;
                    border-radius: 8px;
                    cursor: pointer;
                ">OK</button>
            </div>
        </div>

        <script>
            window.onload = function () {
                setTimeout(function () {
                    document.getElementById('orderPopup').style.display = 'block';
                }, 1500);
            };

            function closeOrderPopup() {
                document.getElementById('orderPopup').style.display = 'none';
            }
        </script>
        <?php
    }
}
add_action('wp_footer', 'popup_after_successful_order');

/**
 * WooCommerce Guest Add-to-Cart → Direct Checkout
 * Works with AJAX & Non-AJAX buttons
 */

// 1) Allow guest checkout
add_filter('woocommerce_enable_guest_checkout', '__return_true');
add_filter('woocommerce_require_login_for_checkout', '__return_false');

// 2) Auto redirect to checkout after add to cart (non-AJAX fallback)
add_filter('woocommerce_add_to_cart_redirect', function () {
    return wc_get_checkout_url();
});

// 3) Ensure WooCommerce session initialized for guests
add_action('init', function () {
    if (class_exists('WooCommerce') && function_exists('WC') && WC()->session === null) {
        try {
            WC()->initialize_session();
        } catch (Exception $e) {
            // fail silently
        }
    }
}, 5);

// 4) AJAX Add-to-Cart redirect to Checkout
add_action('wp_footer', function () {
    if (!is_admin() && class_exists('WooCommerce')) {
        ?>
        <script>
            jQuery(function ($) {
                $('body').on('added_to_cart', function () {
                    // redirect immediately to checkout
                    window.location.href = "<?php echo wc_get_checkout_url(); ?>";
                });
            });
        </script>
        <?php
    }
}, 999);

// 5) Optional: Fix add-to-cart button for simple products (loop/archive)
add_filter('woocommerce_loop_add_to_cart_link', function ($html, $product) {
    if ($product && $product->is_type('simple')) {
        if (strpos($html, 'add_to_cart_button') === false || strpos($html, 'data-product_id') === false) {
            $pid = $product->get_id();
            $url = esc_url($product->add_to_cart_url());
            $label = esc_html($product->add_to_cart_text());
            $classes = 'button add_to_cart_button ajax_add_to_cart';
            return sprintf(
                '<a href="%s" data-quantity="1" data-product_id="%d" class="%s" rel="nofollow">%s</a>',
                $url,
                $pid,
                esc_attr($classes),
                $label
            );
        }
    }
    return $html;
}, 10, 2);


function render_subscription_ui($product_id)
{

    $enable_delivery_date = get_post_meta($product_id, '_enable_delivery_date', true);
    if ($enable_delivery_date !== 'yes')
        return;

    ?>
    <div class="delivery-date-selector checkout-subscription">
        <!-- SUBSCRIPTION BUTTONS -->
        <button type="button" class="subscription-button" data-days="3">3 Day Subscription</button>
        <button type="button" class="subscription-button" data-days="7">7 Day Subscription</button>
        <button type="button" class="subscription-button" data-days="15">14+1 Day Subscription</button>
        <button type="button" class="subscription-button" data-days="30">28+2 Day Subscription</button>
        <button type="button" class="subscription-button" data-days="custom">Custom Subscription</button>

        <div id="delivery_months_buttons" style="display:none;"></div>

        <div id="calendar_container" style="display:none;">
            <label>Select Delivery Dates</label>
            <div id="calendar"></div>
        </div>

        <div id="time_picker_container" style="display:none;">
            <label>Select Time</label>
            <select id="delivery_time" name="delivery_time" required>
                <option value="">Select Time</option>
                <option value="5:00AM TO 6:00AM">5:00AM TO 6:00AM</option>
                <option value="6:00AM TO 7:00AM">6:00AM TO 7:00AM</option>
                <option value="7:00AM TO 8:00AM">7:00AM TO 8:00AM</option>
                <option value="9:00AM TO 10:00AM">9:00AM TO 10:00AM</option>
            </select>
        </div>

        <div id="selected_dates_container" style="display:none;">
            <h4>Selected Dates</h4>
            <div id="selected_dates"></div>
        </div>

        <input type="hidden" id="delivery_months" name="delivery_months">
    </div>
    <?php
}








// Commented out to prevent duplicate subscription picker on the checkout page
// add_action('woocommerce_after_order_notes', 'show_subscription_on_checkout');

function show_subscription_on_checkout($checkout)
{

    foreach (WC()->cart->get_cart() as $cart_item) {
        $product_id = $cart_item['product_id'];

        echo '<h3>Subscription Delivery Schedule</h3>';
        render_subscription_ui($product_id);

        break; // only once even if multiple items
    }
}

add_action('woocommerce_checkout_create_order', function ($order) {
    if (!empty($_POST['delivery_dates'])) {
        $order->update_meta_data('_delivery_dates', sanitize_text_field($_POST['delivery_dates']));
    }
});




/**
 * Redirect non-logged-in users to My Account page
 * when clicking Add to Cart (WooCommerce)
 */

/* Disable AJAX add to cart for guests */
add_action('wp_enqueue_scripts', 'cm_disable_ajax_cart_for_guests', 99);
function cm_disable_ajax_cart_for_guests()
{
    if (!is_user_logged_in()) {
        wp_dequeue_script('wc-add-to-cart');
        wp_deregister_script('wc-add-to-cart');
    }
}

/* Redirect guest users immediately */
add_filter('woocommerce_add_to_cart_validation', 'cm_redirect_guest_to_login', 10, 2);
function cm_redirect_guest_to_login($passed, $product_id)
{

    if (!is_user_logged_in()) {

        wp_safe_redirect(wc_get_page_permalink('myaccount'));
        exit; // IMPORTANT: stop execution
    }

    return $passed;
}



add_filter('woocommerce_checkout_get_value', 'reset_pincode_default', 10, 2);

function reset_pincode_default($value, $input)
{

    if ($input === 'billing_custom_pincode') {
        return ''; // ✅ force placeholder
    }

    return $value;
}

add_filter('woocommerce_billing_fields', 'custom_salon_checkout_fields');

function custom_salon_checkout_fields($fields)
{

    $fields['billing_custom_pincode'] = array(
        'type' => 'select',
        'label' => __('Kindly choose the appropriate pincode to proceed.'),
        'required' => true,
        'class' => array('form-row-wide'),
        'options' => array(
            '' => 'Select Pincode', // ✅ placeholder
            '560068' => '560068',
        ),
        'priority' => 110,
    );

    return $fields;
}

// ==============================
// REMOVE DEFAULT PINCODE
// ==============================
add_filter('woocommerce_billing_fields', 'remove_default_pincode', 20);

function remove_default_pincode($fields)
{
    unset($fields['billing_postcode']);
    return $fields;
}


// ==============================
// FIX ELEMENTOR DROPDOWN ISSUE
// ==============================
function fix_elementor_dropdown_issue()
{
    ?>

    <style>
        .cancel-date-form {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .cancel-date-form select {
            width: 150px;
            height: 38px;
            border-radius: 6px;
        }

        .cancel-date-button {
            background: #3b1f14;
            color: #fff;
            border: none;
            padding: 0 14px;
            height: 38px;
            border-radius: 6px;
            cursor: pointer;
        }

        .cancel-date-button:hover:not(:disabled) {
            background: #D32F2F;
        }

        .cancel-date-button.disabled {
            background: #ccc;
            cursor: not-allowed;
        }

        .no-dates {
            color: #999;
            font-size: 14px;
        }
    </style>

    <?php
}
add_action('wp_footer', 'fix_elementor_dropdown_issue', 100);

/* =========================================
   Responsive Homepage Popup Modal
   Add in functions.php
========================================= */

function custom_home_popup()
{
    ?>

    <!-- Popup -->


    <style>
        .popup-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.75);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 999999;
            opacity: 0;
            visibility: hidden;
            transition: .3s ease;
            padding: 20px;
        }

        .popup-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .popup-box {
            position: relative;
            width: 100%;
            max-width: 700px;
            height: 450px;
            border-radius: 20px;
            overflow: hidden;
            background: #fff;
            animation: popupZoom .4s ease;
        }

        .popup-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .popup-close {
            position: absolute;
            top: 10px;
            right: 10px;
            width: 40px;
            height: 40px;
            border: none;
            border-radius: 50%;
            background: #000;
            color: #fff;
            font-size: 24px;
            cursor: pointer;
            z-index: 10;
        }

        @keyframes popupZoom {
            from {
                transform: scale(.8);
                opacity: 0;
            }

            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        /* Tablet */
        @media(max-width:991px) {

            .popup-box {
                max-width: 600px;
                height: auto;
            }

        }

        /* Mobile */
        @media(max-width:767px) {

            .popup-overlay {
                padding: 15px;
            }

            .popup-box {
                height: auto;
                border-radius: 16px;
            }

            .popup-close {
                width: 35px;
                height: 35px;
                font-size: 20px;
            }

        }
    </style>

    <script>
        document.addEventListener("DOMContentLoaded", function () {

            // Homepage only
            if (
                window.location.pathname !== "/" &&
                window.location.pathname !== "/home/"
            ) {
                return;
            }

            const popup = document.getElementById("homePopup");
            const closeBtn = document.querySelector(".popup-close");

            const delay = 3000;
            const reopenTime = 60000;

            const lastShown = localStorage.getItem("popupTime");
            const now = new Date().getTime();

            if (!lastShown || (now - lastShown > reopenTime)) {

                setTimeout(function () {

                    popup.classList.add("active");

                    localStorage.setItem("popupTime", now);

                }, delay);

            }

            // Close Button
            closeBtn.addEventListener("click", function () {

                popup.classList.remove("active");

            });

            // Outside Click Close
            popup.addEventListener("click", function (e) {

                if (e.target === popup) {

                    popup.classList.remove("active");

                }

            });

        });
    </script>

    <?php
}

add_action('wp_footer', 'custom_home_popup');

/**
 * Redirect shop/archive add-to-cart button
 * to single product details page
 */

add_filter('woocommerce_loop_add_to_cart_link', 'custom_shop_button_to_details', 10, 2);

function custom_shop_button_to_details($button, $product)
{

    $product_link = get_permalink($product->get_id());

    $button = '<a href="' . esc_url($product_link) . '" class="button">
                    Buy Now
               </a>';

    return $button;
}





// Floating View Cart Button
add_action('wp_footer', 'custom_floating_cart_button');

function custom_floating_cart_button()
{

    // WooCommerce check
    if (!class_exists('WooCommerce')) {
        return;
    }

    // Show only when cart has items
    if (WC()->cart && WC()->cart->get_cart_contents_count() > 0) {
        ?>

        <a href="<?php echo wc_get_cart_url(); ?>" class="floating-cart-btn">
            🛒 View Cart
            <span class="cart-count">
                <?php echo WC()->cart->get_cart_contents_count(); ?>
            </span>
        </a>

        <style>
            .floating-cart-btn {
                position: fixed;
                bottom: 20px;
                right: 20px;
                background: #28a745;
                color: #fff !important;
                padding: 14px 22px;
                border-radius: 50px;
                text-decoration: none !important;
                font-size: 16px;
                font-weight: 600;
                z-index: 999999;
                box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
                transition: 0.3s ease;
                display: flex;
                align-items: center;
                gap: 8px;
                line-height: 1;
            }

            .floating-cart-btn:hover {
                transform: translateY(-2px);
                color: #fff !important;
            }

            .cart-count {
                background: #fff;
                color: #28a745;
                width: 22px;
                height: 22px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 13px;
                font-weight: 700;
            }

            /* Mobile Fix */
            @media(max-width:768px) {

                .floating-cart-btn {
                    bottom: 15px;
                    right: 15px;
                    left: 15px;
                    justify-content: center;
                    font-size: 15px;
                    padding: 14px 18px;
                    width: auto;
                }

            }
        </style>

        <?php
    }
}

/* =========================================
   WooCommerce Custom Quantity Buttons
========================================= */

add_action('wp_footer', 'custom_wc_qty_buttons');

function custom_wc_qty_buttons()
{

    if (!is_product() && !is_cart() && !is_shop())
        return;
    ?>

    <script>
        jQuery(function ($) {

            // Remove old buttons
            $('.qty-minus, .qty-plus').remove();

            // Add custom buttons
            $('.quantity').each(function () {

                // Aggressively remove any theme-added buttons, spinners, or arrows
                $(this).find('button:not(.qty-minus):not(.qty-plus), a:not(.qty-minus):not(.qty-plus), .plus:not(.qty-plus), .minus:not(.qty-minus), [class*="nav"], [class*="spin"], [class*="arrow"], [class*="btn"]:not(.qty-minus):not(.qty-plus), [class*="button"]:not(.qty-minus):not(.qty-plus)').remove();

                if ($(this).find('.qty-minus').length === 0) {
                    $(this).prepend('<button type="button" class="qty-minus">-</button>');
                }

                if ($(this).find('.qty-plus').length === 0) {
                    $(this).append('<button type="button" class="qty-plus">+</button>');
                }

            });

            // Minus Button
            $(document).on('click', '.qty-minus', function () {

                let qty = $(this).parent().find('.qty');
                let current = parseFloat(qty.val());
                let min = parseFloat(qty.attr('min')) || 1;
                let step = parseFloat(qty.attr('step')) || 1;

                if (current > min) {
                    qty.val(current - step).trigger('change');
                }

            });

            // Plus Button
            $(document).on('click', '.qty-plus', function () {

                let qty = $(this).parent().find('.qty');
                let current = parseFloat(qty.val());
                let max = parseFloat(qty.attr('max'));
                let step = parseFloat(qty.attr('step')) || 1;

                if (!max || current < max) {
                    qty.val(current + step).trigger('change');
                }

            });

        });
    </script>

    <style>
        /* Hide default WooCommerce arrows */
        input.qty::-webkit-outer-spin-button,
        input.qty::-webkit-inner-spin-button {
            -webkit-appearance: none !important;
            margin: 0 !important;
        }

        input.qty[type=number] {
            -moz-appearance: textfield !important;
        }

        .quantity {
            display: flex !important;
            align-items: center !important;
            flex-wrap: nowrap !important;
            width: fit-content !important;
            gap: 0;
        }

        /* Hide theme-added quantity buttons */
        .quantity .plus:not(.qty-plus),
        .quantity .minus:not(.qty-minus),
        .quantity [class*="nav"],
        .quantity [class*="spin"],
        .quantity [class*="arrow"],
        .quantity [class*="btn"]:not(.qty-minus):not(.qty-plus),
        .quantity [class*="button"]:not(.qty-minus):not(.qty-plus),
        .quantity>*:not(input):not(label):not(.qty-minus):not(.qty-plus):not(div) {
            display: none !important;
        }

        .quantity .qty {
            width: 60px !important;
            height: 40px !important;
            text-align: center !important;
            border: 1px solid #ddd !important;
            margin: 0 !important;
            border-left: 0 !important;
            border-right: 0 !important;
            border-radius: 0 !important;
            box-sizing: border-box !important;
            box-shadow: none !important;
            padding: 0 !important;
        }

        .qty-minus,
        .qty-plus {
            width: 40px;
            height: 40px;
            border: none;
            background: #3b2416;
            color: #fff;
            font-size: 24px;
            font-weight: bold;
            cursor: pointer;
            padding: 0;
            line-height: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            box-sizing: border-box !important;
        }

        .qty-minus {
            border-radius: 6px 0 0 6px !important;
        }

        .qty-plus {
            border-radius: 0 6px 6px 0 !important;
        }

        .qty-minus:hover,
        .qty-plus:hover {
            background: #000;
        }
    </style>

    <?php
}



























// ==========================================
// WOOCOMMERCE SUBSCRIPTION DELIVERY MODULE
// CLEAN + RESPONSIVE VERSION
// ==========================================


// ==========================================
// ADMIN PRODUCT CHECKBOX
// ==========================================

function add_delivery_checkbox_to_product()
{

    woocommerce_wp_checkbox(array(
        'id' => '_enable_delivery_date',
        'label' => __('Enable Subscription Delivery', 'woocommerce'),
        'description' => __('Enable subscription delivery date selection.', 'woocommerce')
    ));
}
add_action(
    'woocommerce_product_options_general_product_data',
    'add_delivery_checkbox_to_product'
);


function save_delivery_checkbox_to_product($post_id)
{

    $value = isset($_POST['_enable_delivery_date'])
        ? 'yes'
        : 'no';

    update_post_meta(
        $post_id,
        '_enable_delivery_date',
        $value
    );
}
add_action(
    'woocommerce_process_product_meta',
    'save_delivery_checkbox_to_product'
);


// ==========================================
// FRONTEND UI
// ==========================================

function display_delivery_date_picker()
{

    global $product;

    $enabled = 'no';
    $pre_selected_dates = [];
    $pre_selected_time = '';
    $pre_selected_plan = 'custom';

    $is_checkout = function_exists('is_checkout') && is_checkout() && !is_wc_endpoint_url('order-received');

    if ($is_checkout) {
        if (!WC()->cart)
            return;
        foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
            $_product = $cart_item['data'];
            if (!$_product)
                continue;

            $enabled = get_post_meta($_product->get_id(), '_enable_delivery_date', true);

            if ($enabled === 'yes') {
                if (isset($cart_item['delivery_dates'])) {
                    $dates_array = explode(',', $cart_item['delivery_dates']);
                    $pre_selected_dates = array_map('trim', $dates_array);
                }
                if (isset($cart_item['delivery_time'])) {
                    $pre_selected_time = $cart_item['delivery_time'];
                }
                if (isset($cart_item['subscription_plan'])) {
                    $pre_selected_plan = $cart_item['subscription_plan'];
                }
                break;
            }
        }
    } else {
        if (!$product)
            return;
        $enabled = get_post_meta($product->get_id(), '_enable_delivery_date', true);
    }

    if ($enabled !== 'yes')
        return;

    $wrapper_class = 'delivery-wrapper';
    if ($is_checkout) {
        $wrapper_class .= ' is-checkout-view';
    }

    ?>

    <div class="<?php echo esc_attr($wrapper_class); ?>">

        <h3 class="delivery-title">
            Subscription Delivery Schedule
        </h3>

        <!-- =========================
         SUBSCRIPTION BUTTONS
    ========================== -->

        <div class="subscription-buttons">

            <button type="button" class="subscription-btn" data-limit="3">
                3 Day
            </button>

            <button type="button" class="subscription-btn" data-limit="7">
                7 Day
            </button>

            <button type="button" class="subscription-btn" data-limit="15">
                14 + 1 Day
            </button>

            <button type="button" class="subscription-btn" data-limit="30">
                28 + 2 Day
            </button>

            <button type="button" class="subscription-btn" data-limit="custom">
                Custom
            </button>

        </div>


        <!-- =========================
         MONTH BUTTONS
    ========================== -->

        <div id="monthButtons" class="month-buttons">
        </div>


        <!-- =========================
         CALENDAR
    ========================== -->

        <div id="calendarWrapper" class="calendar-wrapper">

            <div id="calendar"></div>

        </div>


        <!-- =========================
         DELIVERY TIME
    ========================== -->

        <div id="timeWrapper" class="time-wrapper" style="display:none;">

            <label for="delivery_time">
                Delivery Time
            </label>

            <select id="delivery_time" name="delivery_time" <?php if ($is_checkout)
                echo 'disabled'; ?>>
                <option value="">Select Time</option>
                <option value="5:00AM TO 6:00AM" <?php selected($pre_selected_time, '5:00AM TO 6:00AM'); ?>>5:00AM TO
                    6:00AM</option>
                <option value="6:00AM TO 7:00AM" <?php selected($pre_selected_time, '6:00AM TO 7:00AM'); ?>>6:00AM TO
                    7:00AM</option>
                <option value="7:00AM TO 8:00AM" <?php selected($pre_selected_time, '7:00AM TO 8:00AM'); ?>>7:00AM TO
                    8:00AM</option>
                <option value="9:00AM TO 10:00AM" <?php selected($pre_selected_time, '9:00AM TO 10:00AM'); ?>>9:00AM TO
                    10:00AM</option>
            </select>
            <?php if ($is_checkout): ?>
                <input type="hidden" name="delivery_time" value="<?php echo esc_attr($pre_selected_time); ?>">
            <?php endif; ?>

        </div>


        <!-- =========================
         SELECTED DATES
    ========================== -->

        <div id="selectedDatesWrapper" class="selected-dates-wrapper" style="display:none;">

            <h4>Selected Dates</h4>

            <div id="selectedDates"></div>

        </div>


        <!-- HIDDEN INPUT -->

        <input type="hidden" name="delivery_dates" id="delivery_dates">

        <!-- HIDDEN INPUT FOR PLAN -->
        <input type="hidden" name="subscription_plan" id="subscription_plan"
            value="<?php echo esc_attr($pre_selected_plan); ?>">

    </div>


    <!-- ==========================================
     JAVASCRIPT
========================================== -->

    <script>

        document.addEventListener('DOMContentLoaded', function () {

            // =====================================
            // VARIABLES
            // =====================================

            const maxDays = {
                3: 3,
                7: 7,
                15: 15,
                30: 30,
                custom: null
            };

            const isCheckoutPage = <?php echo json_encode($is_checkout); ?>;
            let selectedPlan = <?php echo json_encode($pre_selected_plan); ?>;
            if (!selectedPlan) selectedPlan = 'custom';
            let selectedDates = <?php echo json_encode($pre_selected_dates); ?>;
            if (!selectedDates) selectedDates = [];

            const monthNames = [
                'January',
                'February',
                'March',
                'April',
                'May',
                'June',
                'July',
                'August',
                'September',
                'October',
                'November',
                'December'
            ];


            // =====================================
            // ELEMENTS
            // =====================================

            const subscriptionButtons =
                document.querySelectorAll('.subscription-btn');

            const monthButtonsContainer =
                document.getElementById('monthButtons');

            const calendarWrapper =
                document.getElementById('calendarWrapper');

            const calendar =
                document.getElementById('calendar');

            const selectedDatesWrapper =
                document.getElementById('selectedDatesWrapper');

            const selectedDatesContainer =
                document.getElementById('selectedDates');

            const hiddenInput =
                document.getElementById('delivery_dates');

            const timeWrapper =
                document.getElementById('timeWrapper');

            const timeInput = document.getElementById('delivery_time');
            if (timeInput) {
                timeInput.addEventListener('change', function () {
                    const today = new Date();
                    const y = today.getFullYear();
                    const m = String(today.getMonth() + 1).padStart(2, '0');
                    const d = String(today.getDate()).padStart(2, '0');
                    const todayStr = `${y}-${m}-${d}`;

                    if (selectedDates.includes(todayStr)) {
                        const hours = String(today.getHours()).padStart(2, '0');
                        const minutes = String(today.getMinutes()).padStart(2, '0');
                        const minTime = `${hours}:${minutes}`;
                        if (this.value && this.value < minTime) {
                            alert('Please select a future delivery time.');
                            this.value = minTime;
                        }
                    }
                });
            }


            // =====================================
            // SUBSCRIPTION BUTTON CLICK
            // =====================================

            subscriptionButtons.forEach(button => {

                button.addEventListener('click', function () {

                    subscriptionButtons.forEach(btn => {
                        btn.classList.remove('active');
                    });

                    this.classList.add('active');

                    selectedPlan = this.dataset.limit;
                    const planInput = document.getElementById('subscription_plan');
                    if (planInput) {
                        planInput.value = selectedPlan;
                    }

                    selectedDates = [];

                    editIndex = null;

                    updateSelectedDates();

                    loadMonths();

                });

            });


            // =====================================
            // LOAD MONTHS
            // =====================================

            function loadMonths() {

                monthButtonsContainer.innerHTML = '';

                let today = new Date();

                for (let i = 0; i < 3; i++) {

                    let date = new Date(
                        today.getFullYear(),
                        today.getMonth() + i,
                        1
                    );

                    let year = date.getFullYear();

                    let month = date.getMonth();

                    let button = document.createElement('button');

                    button.type = 'button';

                    button.className = 'month-btn';

                    button.innerText =
                        monthNames[month] + ' ' + year;

                    button.addEventListener('click', function () {

                        document.querySelectorAll('.month-btn')
                            .forEach(btn => {
                                btn.classList.remove('active');
                            });

                        this.classList.add('active');

                        showCalendar(year, month);

                    });

                    monthButtonsContainer.appendChild(button);
                }

                // Auto-select first month so the calendar is instantly visible
                let firstMonth = monthButtonsContainer.querySelector('.month-btn');
                if (firstMonth) firstMonth.click();
            }


            // =====================================
            // SHOW CALENDAR
            // =====================================

            function showCalendar(year, month) {

                calendar.innerHTML = '';

                calendarWrapper.style.display = 'block';

                const totalDays =
                    new Date(year, month + 1, 0).getDate();

                const today = new Date();

                for (let day = 1; day <= totalDays; day++) {

                    let currentDate =
                        new Date(year, month, day);

                    // Disable past dates

                    if (
                        currentDate <
                        new Date(
                            today.getFullYear(),
                            today.getMonth(),
                            today.getDate()
                        )
                    ) {
                        continue;
                    }

                    let formattedDate =
                        `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;

                    let button = document.createElement('button');

                    button.type = 'button';

                    button.className = 'calendar-date';

                    button.innerText = day;

                    // Selected class

                    if (selectedDates.includes(formattedDate)) {
                        button.classList.add('selected');
                    }

                    button.addEventListener('click', function () {

                        selectDate(formattedDate);

                        showCalendar(year, month);

                    });

                    calendar.appendChild(button);

                }

            }


            // =====================================
            // SELECT DATE
            // =====================================

            function selectDate(date) {

                let index = selectedDates.indexOf(date);

                // If date is already selected, deselect it (toggle)
                if (index !== -1) {
                    selectedDates.splice(index, 1);
                } else {
                    // Limit check
                    if (
                        selectedPlan !== 'custom' &&
                        selectedDates.length >= maxDays[selectedPlan]
                    ) {
                        alert('Maximum selected dates reached');
                        return;
                    }
                    selectedDates.push(date);
                }

                updateSelectedDates();

            }


            // =====================================
            // UPDATE SELECTED DATES
            // =====================================

            function updateSelectedDates() {

                selectedDatesContainer.innerHTML = '';

                selectedDates.forEach((date, index) => {

                    let item = document.createElement('div');

                    item.className = 'selected-date-item';

                    // format YYYY-MM-DD to a nice localized date format
                    let displayDate = date;
                    const dateParts = date.split('-');
                    if (dateParts.length === 3) {
                        const year = parseInt(dateParts[0], 10);
                        const month = parseInt(dateParts[1], 10) - 1;
                        const day = parseInt(dateParts[2], 10);
                        const dObj = new Date(year, month, day);
                        if (!isNaN(dObj.getTime())) {
                            displayDate = dObj.toLocaleDateString(undefined, {
                                weekday: 'short',
                                year: 'numeric',
                                month: 'short',
                                day: 'numeric'
                            });
                        }
                    }

                    item.innerHTML = `

                <span>${displayDate}</span>

                <div class="date-actions">

                    <button type="button"
                            class="remove-btn"
                            data-index="${index}">
                        ✕
                    </button>

                </div>

            `;

                    selectedDatesContainer.appendChild(item);

                });


                // ==========================
                // REMOVE BUTTON
                // ==========================

                document.querySelectorAll('.remove-btn')
                    .forEach(btn => {

                        btn.addEventListener('click', function () {

                            let index = this.dataset.index;

                            selectedDates.splice(index, 1);

                            updateSelectedDates();

                        });

                    });


                // Hidden Input

                hiddenInput.value =
                    JSON.stringify(selectedDates);


                // Show/Hide

                selectedDatesWrapper.style.display =
                    selectedDates.length
                        ? 'block'
                        : 'none';

                timeWrapper.style.display =
                    selectedDates.length
                        ? 'block'
                        : 'none';

                // Handle time picker restriction for today
                if (timeInput) {
                    const today = new Date();
                    const y = today.getFullYear();
                    const m = String(today.getMonth() + 1).padStart(2, '0');
                    const d = String(today.getDate()).padStart(2, '0');
                    const todayStr = `${y}-${m}-${d}`;

                    if (selectedDates.includes(todayStr)) {
                        const hours = String(today.getHours()).padStart(2, '0');
                        const minutes = String(today.getMinutes()).padStart(2, '0');
                        const minTime = `${hours}:${minutes}`;
                        timeInput.setAttribute('min', minTime);

                        // If current value is less than minTime, update it
                        if (timeInput.value && timeInput.value < minTime) {
                            timeInput.value = minTime;
                        }
                    } else {
                        timeInput.removeAttribute('min');
                    }
                }

            }

            // Initialize module automatically on load
            const activeBtn = document.querySelector(`[data-limit="${selectedPlan}"]`);

            if (selectedDates && selectedDates.length > 0) {
                if (activeBtn) activeBtn.classList.add('active');
                updateSelectedDates();
                if (!isCheckoutPage) {
                    loadMonths();
                }
            } else {
                if (activeBtn) {
                    if (!isCheckoutPage) {
                        activeBtn.click();
                    } else {
                        activeBtn.classList.add('active');
                        updateSelectedDates();
                    }
                } else {
                    const customBtn = document.querySelector('[data-limit="custom"]');
                    if (customBtn) {
                        if (!isCheckoutPage) {
                            customBtn.click();
                        } else {
                            customBtn.classList.add('active');
                            updateSelectedDates();
                        }
                    }
                }
            }

            // Validate form on submit to ensure correct number of days
            const cartForm = document.querySelector('form.cart');
            if (cartForm) {
                cartForm.addEventListener('submit', function (e) {
                    const enabled = document.getElementById('delivery_dates');
                    if (!enabled) return;

                    if (selectedPlan !== 'custom') {
                        let required = maxDays[selectedPlan];
                        if (selectedDates.length < required) {
                            e.preventDefault();
                            alert('Please select exactly ' + required + ' dates on the calendar for your ' + selectedPlan + ' Day plan.');
                        }
                    } else {
                        if (selectedDates.length === 0) {
                            e.preventDefault();
                            alert('Please select at least 1 delivery date.');
                        }
                    }
                });
            }

        });

    </script>



    <!-- ==========================================
     CSS
========================================== -->

    <style>
        /* =========================
       MAIN WRAPPER
    ========================= */

        .delivery-wrapper {
            margin-top: 25px;
        }

        /* =========================
       CHECKOUT STATE VIEW
    ========================= */

        .is-checkout-view #monthButtons,
        .is-checkout-view #calendarWrapper,
        .is-checkout-view .remove-btn {
            display: none !important;
        }

        .is-checkout-view .subscription-btn {
            pointer-events: none;
            opacity: 0.8;
        }

        .is-checkout-view #delivery_time {
            pointer-events: none;
            background-color: #f7f7f7;
        }


        /* =========================
       TITLE
    ========================= */

        .delivery-title {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 20px;
            color: #1f2d2b;
        }


        /* =========================
       BUTTON GROUPS
    ========================= */

        .subscription-buttons,
        .month-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 20px;
        }


        /* =========================
       COMMON BUTTON STYLE
    ========================= */

        .subscription-btn,
        .month-btn,
        .calendar-date {
            border: none;
            outline: none;
            cursor: pointer;
            transition: 0.3s ease;
            font-weight: 600;
            border-radius: 10px;
        }


        /* =========================
       SUBSCRIPTION BUTTONS
    ========================= */

        .subscription-btn,
        .month-btn {
            background: #3b1f12;
            color: #fff;
            padding: 14px 24px;
            font-size: 18px;
            line-height: 1;
        }

        .subscription-btn:hover,
        .month-btn:hover {
            background: #fe8806 !important;
        }

        .subscription-btn.active,
        .month-btn.active {
            background: #fe8806 !important;
            color: #fff !important;
        }


        /* =========================
       CALENDAR
    ========================= */

        .calendar-wrapper {
            margin-top: 10px;
            margin-bottom: 25px;
        }

        #calendar {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(65px, 1fr));
            gap: 12px;
            max-width: 700px;
        }


        /* =========================
       DATE BUTTON
    ========================= */

        .calendar-date {
            width: 65px;
            height: 65px;
            background: #3b1f12;
            color: #fff;
            font-size: 20px;

            display: flex;
            align-items: center;
            justify-content: center;

            padding: 0;
        }

        .calendar-date:hover {
            background: #fe8806 !important;
        }

        .calendar-date.selected {
            background: #fe8806 !important;
            color: #fff !important;
        }


        /* =========================
       TIME PICKER
    ========================= */

        .time-wrapper {
            margin-bottom: 30px;
        }

        .time-wrapper label {
            display: block;
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 10px;
            color: #1f2d2b;
        }

        .time-wrapper input {
            width: 220px;
            height: 50px;
            padding: 10px 15px;
            border: 1px solid #d9d9d9;
            border-radius: 8px;
            font-size: 16px;
        }


        /* =========================
       SELECTED DATES
    ========================= */

        .selected-dates-wrapper h4 {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 20px;
            color: #1f2d2b;
        }

        .selected-date-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #f7f7f7;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 15px;
        }

        .selected-date-item span {
            font-size: 18px;
            font-weight: 600;
            color: #5c6670;
        }


        /* =========================
       ACTION BUTTONS
    ========================= */

        .date-actions {
            display: flex;
            gap: 10px;
        }

        .remove-btn {
            width: 40px;
            height: 40px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 18px;
            background: #3b1f12;
            color: #fff;
            transition: 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
        }

        .remove-btn:hover {
            background: #d11a2a !important;
        }


        /* =========================
       MOBILE RESPONSIVE
    ========================= */

        @media(max-width:768px) {

            .delivery-title {
                font-size: 22px;
            }

            .subscription-btn,
            .month-btn {
                width: 100%;
                justify-content: center;
            }

            #calendar {
                grid-template-columns: repeat(4, 1fr);
            }

            .calendar-date {
                width: 100%;
                height: 55px;
                font-size: 18px;
            }

            .selected-date-item {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
            }

            .date-actions {
                width: 100%;
            }

            .remove-btn {
                flex: 1;
            }

        }
    </style>

    <?php

}
add_action('woocommerce_before_add_to_cart_button', 'display_delivery_date_picker');
// Checkout hook to display schedule on checkout page
add_action('woocommerce_after_order_notes', 'display_delivery_date_picker');


// ==========================================
// 1. SAVE DELIVERY DATA TO CART
// ==========================================
function save_delivery_data_to_cart($cart_item_data, $product_id, $variation_id)
{
    if (isset($_POST['delivery_dates']) && !empty($_POST['delivery_dates'])) {
        // Decode the JSON dates
        $dates = json_decode(stripslashes($_POST['delivery_dates']), true);
        if (is_array($dates) && count($dates) > 0) {
            $cart_item_data['delivery_dates'] = implode(', ', $dates);
        }
    }

    if (isset($_POST['delivery_time']) && !empty($_POST['delivery_time'])) {
        $cart_item_data['delivery_time'] = sanitize_text_field($_POST['delivery_time']);
    }

    if (isset($_POST['subscription_plan']) && !empty($_POST['subscription_plan'])) {
        $cart_item_data['subscription_plan'] = sanitize_text_field($_POST['subscription_plan']);
    }

    return $cart_item_data;
}
add_filter('woocommerce_add_cart_item_data', 'save_delivery_data_to_cart', 10, 3);


// ==========================================
// 2. DISPLAY DATA IN CART AND CHECKOUT
// ==========================================
function display_delivery_data_in_cart($item_data, $cart_item)
{
    if (isset($cart_item['subscription_plan']) && !empty($cart_item['subscription_plan'])) {
        $plan_labels = array(
            '3' => __('3 Day Subscription', 'woocommerce'),
            '7' => __('7 Day Subscription', 'woocommerce'),
            '15' => __('14 + 1 Day Subscription', 'woocommerce'),
            '30' => __('28 + 2 Day Subscription', 'woocommerce'),
            'custom' => __('Custom Subscription', 'woocommerce')
        );
        $plan_key = $cart_item['subscription_plan'];
        $plan_label = isset($plan_labels[$plan_key]) ? $plan_labels[$plan_key] : ucfirst($plan_key) . ' Day Subscription';

        $item_data[] = array(
            'key' => __('Subscription Schedule', 'woocommerce'),
            'value' => $plan_label
        );
    }

    if (isset($cart_item['delivery_dates']) && !empty($cart_item['delivery_dates'])) {
        $dates = explode(',', $cart_item['delivery_dates']);
        $formatted_dates = array();
        foreach ($dates as $date) {
            $date = trim($date);
            if (!empty($date)) {
                $timestamp = strtotime($date);
                if ($timestamp) {
                    $formatted_dates[] = date_i18n(get_option('date_format'), $timestamp);
                } else {
                    $formatted_dates[] = $date;
                }
            }
        }
        $item_data[] = array(
            'key' => __('Selected Dates', 'woocommerce'),
            'value' => implode(', ', $formatted_dates)
        );
    }

    if (isset($cart_item['delivery_time']) && !empty($cart_item['delivery_time'])) {
        $time_val = wc_clean($cart_item['delivery_time']);
        $timestamp = strtotime($time_val);
        if ($timestamp) {
            $formatted_time = date_i18n(get_option('time_format'), $timestamp);
        } else {
            $formatted_time = $time_val;
        }
        $item_data[] = array(
            'key' => __('Delivery Time', 'woocommerce'),
            'value' => $formatted_time
        );
    }

    return $item_data;
}
add_filter('woocommerce_get_item_data', 'display_delivery_data_in_cart', 10, 2);


// ==========================================
// 3. SAVE DATA TO ORDER ITEMS
// ==========================================
function save_delivery_data_to_order_items($item, $cart_item_key, $values, $order)
{

    $product_id = $item->get_product_id();
    $enabled = get_post_meta($product_id, '_enable_delivery_date', true);
    if ($enabled !== 'yes') {
        return;
    }
    if (isset($_POST['subscription_plan']) && !empty($_POST['subscription_plan'])) {
        $plan_labels = array(
            '3' => __('3 Day Subscription', 'woocommerce'),
            '7' => __('7 Day Subscription', 'woocommerce'),
            '15' => __('14 + 1 Day Subscription', 'woocommerce'),
            '30' => __('28 + 2 Day Subscription', 'woocommerce'),
            'custom' => __('Custom Subscription', 'woocommerce')
        );
        $plan_key = sanitize_text_field($_POST['subscription_plan']);
        $plan_label = isset($plan_labels[$plan_key]) ? $plan_labels[$plan_key] : ucfirst($plan_key) . ' Day Subscription';

        $item->add_meta_data(__('Subscription Schedule', 'woocommerce'), $plan_label, true);
    } elseif (isset($values['subscription_plan']) && !empty($values['subscription_plan'])) {
        $plan_labels = array(
            '3' => __('3 Day Subscription', 'woocommerce'),
            '7' => __('7 Day Subscription', 'woocommerce'),
            '15' => __('14 + 1 Day Subscription', 'woocommerce'),
            '30' => __('28 + 2 Day Subscription', 'woocommerce'),
            'custom' => __('Custom Subscription', 'woocommerce')
        );
        $plan_key = $values['subscription_plan'];
        $plan_label = isset($plan_labels[$plan_key]) ? $plan_labels[$plan_key] : ucfirst($plan_key) . ' Day Subscription';

        $item->add_meta_data(__('Subscription Schedule', 'woocommerce'), $plan_label, true);
    }

    // Check if edited directly on checkout page
    if (isset($_POST['delivery_dates']) && !empty($_POST['delivery_dates'])) {
        $dates = json_decode(stripslashes($_POST['delivery_dates']), true);
        if (is_array($dates) && count($dates) > 0) {
            $formatted_dates = array();
            foreach ($dates as $date) {
                $timestamp = strtotime($date);
                if ($timestamp) {
                    $formatted_dates[] = date_i18n(get_option('date_format'), $timestamp);
                } else {
                    $formatted_dates[] = $date;
                }
            }
            $item->add_meta_data(__('Selected Dates', 'woocommerce'), implode(', ', $formatted_dates), true);
            $item->add_meta_data('_delivery_times', $dates, true);
            // FIX: Set initial delivery status for each date so admin page won't show 'pending'
            foreach ($dates as $d) {
                $item->add_meta_data('_delivery_status_' . $d, 'confirmed', true);
            }
        }
    } elseif (isset($values['delivery_dates']) && !empty($values['delivery_dates'])) {
        $dates = explode(',', $values['delivery_dates']);
        $formatted_dates = array();
        foreach ($dates as $date) {
            $date = trim($date);
            if (!empty($date)) {
                $timestamp = strtotime($date);
                if ($timestamp) {
                    $formatted_dates[] = date_i18n(get_option('date_format'), $timestamp);
                } else {
                    $formatted_dates[] = $date;
                }
            }
        }
        $item->add_meta_data(__('Selected Dates', 'woocommerce'), implode(', ', $formatted_dates), true);
        $item->add_meta_data('_delivery_times', array_map('trim', $dates), true);
        // FIX: Set initial delivery status for each date so admin page won't show 'pending'
        foreach ($dates as $d) {
            $d = trim($d);
            $item->add_meta_data('_delivery_status_' . $d, 'confirmed', true);
        }
    }

    if (isset($_POST['delivery_time']) && !empty($_POST['delivery_time'])) {
        $time_val = sanitize_text_field($_POST['delivery_time']);
        $timestamp = strtotime($time_val);
        if ($timestamp) {
            $formatted_time = date_i18n(get_option('time_format'), $timestamp);
        } else {
            $formatted_time = $time_val;
        }
        $item->add_meta_data(__('Delivery Time', 'woocommerce'), $formatted_time, true);
        $item->add_meta_data('_delivery_time', $time_val, true);
    } elseif (isset($values['delivery_time']) && !empty($values['delivery_time'])) {
        $time_val = $values['delivery_time'];
        $timestamp = strtotime($time_val);
        if ($timestamp) {
            $formatted_time = date_i18n(get_option('time_format'), $timestamp);
        } else {
            $formatted_time = $time_val;
        }
        $item->add_meta_data(__('Delivery Time', 'woocommerce'), $formatted_time, true);
        $item->add_meta_data('_delivery_time', $time_val, true);
    }
}
add_action('woocommerce_checkout_create_order_line_item', 'save_delivery_data_to_order_items', 10, 4);


// ==========================================
// 4. CALCULATE DYNAMIC PRICE BASED ON DAYS
// ==========================================
function calculate_dynamic_subscription_price($cart)
{
    if (is_admin() && !defined('DOING_AJAX'))
        return;
    if (did_action('woocommerce_before_calculate_totals') >= 2)
        return;

    foreach ($cart->get_cart() as $cart_item_key => $cart_item) {
        if (isset($cart_item['delivery_dates']) && !empty($cart_item['delivery_dates'])) {

            // Count how many dates the user selected
            $dates = explode(',', $cart_item['delivery_dates']);
            $num_days = count(array_filter(array_map('trim', $dates)));

            if ($num_days > 0) {
                // Get the original product price from the database
                $product_id = !empty($cart_item['variation_id']) ? $cart_item['variation_id'] : $cart_item['product_id'];
                $original_product = wc_get_product($product_id);
                if (!$original_product)
                    continue;

                $base_price = $original_product->get_price();

                // Multiply price by the number of days selected
                $new_price = floatval($base_price) * $num_days;

                // Set the new cart item price
                $cart_item['data']->set_price($new_price);
            }
        }
    }
}
add_action('woocommerce_before_calculate_totals', 'calculate_dynamic_subscription_price', 10);

// =========================================================================
// WOOCOMMERCE MULTIPLE DELIVERY ADDRESS SYSTEM
// =========================================================================

// 1. Register endpoint and query vars
function custom_delivery_addresses_endpoint()
{
    add_rewrite_endpoint('delivery-addresses', EP_ROOT | EP_PAGES);
}
add_action('init', 'custom_delivery_addresses_endpoint');

function custom_delivery_addresses_query_vars($vars)
{
    $vars[] = 'delivery-addresses';
    return $vars;
}
add_filter('query_vars', 'custom_delivery_addresses_query_vars', 0);

// 2. Menu Reordering (Priority 999 to enforce exact structure)
function custom_reorder_my_account_menu($items)
{
    $new_items = array();

    if (isset($items['dashboard'])) {
        $new_items['dashboard'] = $items['dashboard'];
    }
    if (isset($items['orders'])) {
        $new_items['orders'] = $items['orders'];
    }
    if (isset($items['downloads'])) {
        $new_items['downloads'] = $items['downloads'];
    }
    if (isset($items['edit-account'])) {
        $new_items['edit-account'] = $items['edit-account'];
    }

    $new_items['subscription-details'] = __('Subscription Details', 'woocommerce');

    if (isset($items['customer-logout'])) {
        $new_items['customer-logout'] = $items['customer-logout'];
    }

    $new_items['delivery-addresses'] = __('Delivery Addresses', 'woocommerce');
    $new_items['reschedule-delivery'] = __('Reschedule Delivery', 'woocommerce');

    return $new_items;
}
add_filter('woocommerce_account_menu_items', 'custom_reorder_my_account_menu', 999);

// 3. User Meta CRUD functions
function wc_get_saved_multiple_addresses($user_id)
{
    if (!$user_id) {
        return array();
    }
    $addresses = get_user_meta($user_id, 'wc_multiple_addresses', true);

    // Check if addresses need to be migrated from legacy format
    $needs_migration = false;
    if (is_array($addresses)) {
        foreach ($addresses as $key => $addr) {
            if (!is_array($addr) || empty($addr['id'])) {
                $needs_migration = true;
                break;
            }
        }
    } else {
        $needs_migration = true;
    }

    if ($needs_migration) {
        $legacy = get_user_meta($user_id, 'multi_shipping_addresses', true);
        $migrated = array();

        if (is_array($legacy)) {
            $is_first = true;
            foreach ($legacy as $key => $item) {
                if (!is_array($item))
                    continue;

                $addr_type = 'Other';
                $key_lower = strtolower($key);
                if (strpos($key_lower, 'home') !== false || (isset($item['label']) && strpos(strtolower($item['label']), 'home') !== false)) {
                    $addr_type = 'Home';
                } elseif (strpos($key_lower, 'office') !== false || (isset($item['label']) && strpos(strtolower($item['label']), 'office') !== false)) {
                    $addr_type = 'Office';
                }

                $first_name = isset($item['first_name']) ? $item['first_name'] : '';
                $last_name = isset($item['last_name']) ? $item['last_name'] : '';
                $fullname = trim($first_name . ' ' . $last_name);
                if (empty($fullname) && isset($item['name'])) {
                    $fullname = $item['name'];
                }

                $migrated[] = array(
                    'id' => 'addr_' . uniqid() . '_' . rand(1000, 9999),
                    'address_name' => isset($item['label']) ? $item['label'] : ucfirst($key),
                    'name' => $fullname,
                    'phone' => isset($item['phone']) ? $item['phone'] : '',
                    'address_1' => isset($item['address_1']) ? $item['address_1'] : '',
                    'address_2' => isset($item['address_2']) ? $item['address_2'] : '',
                    'area' => isset($item['area']) ? $item['area'] : '',
                    'city' => isset($item['city']) ? $item['city'] : '',
                    'state' => isset($item['state']) ? $item['state'] : '',
                    'postcode' => isset($item['postcode']) ? $item['postcode'] : '',
                    'country' => isset($item['country']) ? $item['country'] : 'IN',
                    'landmark' => isset($item['landmark']) ? $item['landmark'] : '',
                    'address_type' => $addr_type,
                    'is_default' => $is_first,
                );
                $is_first = false;
            }
        }

        update_user_meta($user_id, 'wc_multiple_addresses', $migrated);
        $addresses = $migrated;
    }

    // Always return a clean array, ensuring defensive defaults for all keys
    $clean_addresses = array();
    if (is_array($addresses)) {
        foreach ($addresses as $addr) {
            if (!is_array($addr))
                continue;
            $clean_addresses[] = array(
                'id' => isset($addr['id']) ? $addr['id'] : 'addr_' . uniqid(),
                'address_name' => isset($addr['address_name']) ? $addr['address_name'] : 'Address',
                'name' => isset($addr['name']) ? $addr['name'] : '',
                'phone' => isset($addr['phone']) ? $addr['phone'] : '',
                'address_1' => isset($addr['address_1']) ? $addr['address_1'] : '',
                'address_2' => isset($addr['address_2']) ? $addr['address_2'] : '',
                'area' => isset($addr['area']) ? $addr['area'] : '',
                'city' => isset($addr['city']) ? $addr['city'] : '',
                'state' => isset($addr['state']) ? $addr['state'] : '',
                'postcode' => isset($addr['postcode']) ? $addr['postcode'] : '',
                'country' => isset($addr['country']) ? $addr['country'] : 'IN',
                'landmark' => isset($addr['landmark']) ? $addr['landmark'] : '',
                'address_type' => isset($addr['address_type']) ? $addr['address_type'] : 'Home',
                'is_default' => !empty($addr['is_default']),
            );
        }
    }

    return $clean_addresses;
}

function wc_save_user_multiple_address($user_id, $address_data)
{
    if (!$user_id || !is_array($address_data)) {
        return false;
    }
    $addresses = wc_get_saved_multiple_addresses($user_id);

    $is_new = empty($address_data['id']);
    if ($is_new) {
        $address_data['id'] = 'addr_' . uniqid() . '_' . time();
    }

    // Default flag handling
    if (!empty($address_data['is_default'])) {
        foreach ($addresses as &$addr) {
            $addr['is_default'] = false;
        }
    } else {
        if (empty($addresses)) {
            $address_data['is_default'] = true;
        } else {
            $address_data['is_default'] = false;
        }
    }

    $found = false;
    if (!$is_new) {
        foreach ($addresses as &$addr) {
            if ($addr['id'] === $address_data['id']) {
                $addr = array_merge($addr, $address_data);
                $found = true;
                break;
            }
        }
    }

    if ($is_new || !$found) {
        $addresses[] = $address_data;
    }

    return update_user_meta($user_id, 'wc_multiple_addresses', $addresses);
}

function wc_delete_user_multiple_address($user_id, $address_id)
{
    if (!$user_id || !$address_id) {
        return false;
    }
    $addresses = wc_get_saved_multiple_addresses($user_id);
    $filtered = array();
    $was_default = false;

    foreach ($addresses as $addr) {
        if ($addr['id'] === $address_id) {
            if (!empty($addr['is_default'])) {
                $was_default = true;
            }
            continue;
        }
        $filtered[] = $addr;
    }

    if ($was_default && !empty($filtered)) {
        $filtered[0]['is_default'] = true;
    }

    return update_user_meta($user_id, 'wc_multiple_addresses', $filtered);
}

function wc_set_default_user_multiple_address($user_id, $address_id)
{
    if (!$user_id || !$address_id) {
        return false;
    }
    $addresses = wc_get_saved_multiple_addresses($user_id);
    $found = false;
    foreach ($addresses as &$addr) {
        if ($addr['id'] === $address_id) {
            $addr['is_default'] = true;
            $found = true;
        } else {
            $addr['is_default'] = false;
        }
    }
    if ($found) {
        return update_user_meta($user_id, 'wc_multiple_addresses', $addresses);
    }
    return false;
}

// 4. AJAX CRUD Handler
function handle_wc_delivery_address_crud()
{
    check_ajax_referer('wc_address_crud_nonce', 'nonce');

    if (!is_user_logged_in()) {
        wp_send_json_error(__('Unauthorized action.', 'woocommerce'));
    }

    $user_id = get_current_user_id();
    $action_type = isset($_POST['action_type']) ? sanitize_text_field($_POST['action_type']) : '';

    if ($action_type === 'delete') {
        $address_id = isset($_POST['address_id']) ? sanitize_text_field($_POST['address_id']) : '';
        if (empty($address_id)) {
            wp_send_json_error(__('Missing address ID.', 'woocommerce'));
        }
        if (wc_delete_user_multiple_address($user_id, $address_id)) {
            wp_send_json_success(__('Address deleted successfully.', 'woocommerce'));
        } else {
            wp_send_json_error(__('Could not delete address.', 'woocommerce'));
        }
    }

    if ($action_type === 'set_default') {
        $address_id = isset($_POST['address_id']) ? sanitize_text_field($_POST['address_id']) : '';
        if (empty($address_id)) {
            wp_send_json_error(__('Missing address ID.', 'woocommerce'));
        }
        if (wc_set_default_user_multiple_address($user_id, $address_id)) {
            wp_send_json_success(__('Default address updated.', 'woocommerce'));
        } else {
            wp_send_json_error(__('Could not update default address.', 'woocommerce'));
        }
    }

    if ($action_type === 'save') {
        $address_id = isset($_POST['address_id']) ? sanitize_text_field($_POST['address_id']) : '';
        $name = isset($_POST['name']) ? sanitize_text_field($_POST['name']) : '';
        $phone = isset($_POST['phone']) ? sanitize_text_field($_POST['phone']) : '';
        $address_1 = isset($_POST['address_1']) ? sanitize_text_field($_POST['address_1']) : '';
        $address_2 = isset($_POST['address_2']) ? sanitize_text_field($_POST['address_2']) : '';
        $area = isset($_POST['area']) ? sanitize_text_field($_POST['area']) : '';
        $landmark = isset($_POST['landmark']) ? sanitize_text_field($_POST['landmark']) : '';
        $city = isset($_POST['city']) ? sanitize_text_field($_POST['city']) : '';
        $state = isset($_POST['state']) ? sanitize_text_field($_POST['state']) : '';
        $postcode = isset($_POST['postcode']) ? sanitize_text_field($_POST['postcode']) : '';
        $address_type = isset($_POST['address_type']) ? sanitize_text_field($_POST['address_type']) : 'Home';
        $is_default = (isset($_POST['is_default']) && $_POST['is_default'] == '1');

        if (empty($name) || empty($phone) || empty($address_1) || empty($area) || empty($city) || empty($state) || empty($postcode)) {
            wp_send_json_error(__('Please fill in all required fields.', 'woocommerce'));
        }

        if (!preg_match('/^[0-9]{10}$/', $phone)) {
            wp_send_json_error(__('Please enter a valid 10-digit mobile number.', 'woocommerce'));
        }

        if (!preg_match('/^[0-9]{6}$/', $postcode)) {
            wp_send_json_error(__('Please enter a valid 6-digit postcode.', 'woocommerce'));
        }

        $address_data = array(
            'id' => $address_id ?: 'addr_' . uniqid() . '_' . time(),
            'address_name' => $address_type . ' (' . $city . ')',
            'name' => $name,
            'phone' => $phone,
            'address_1' => $address_1,
            'address_2' => $address_2,
            'area' => $area,
            'city' => $city,
            'state' => $state,
            'postcode' => $postcode,
            'country' => 'IN',
            'landmark' => $landmark,
            'address_type' => $address_type,
            'is_default' => $is_default,
        );

        if (wc_save_user_multiple_address($user_id, $address_data)) {
            wp_send_json_success(__('Address saved successfully.', 'woocommerce'));
        } else {
            wp_send_json_error(__('Could not save address.', 'woocommerce'));
        }
    }

    wp_send_json_error(__('Invalid action.', 'woocommerce'));
}
add_action('wp_ajax_wc_delivery_address_crud', 'handle_wc_delivery_address_crud');

// 5. My Account Endpoint Content Display
function custom_delivery_addresses_content()
{
    if (!is_user_logged_in()) {
        echo '<div class="woocommerce-error">' . esc_html__('You must be logged in to manage your addresses.', 'woocommerce') . '</div>';
        return;
    }

    $user_id = get_current_user_id();
    $addresses = wc_get_saved_multiple_addresses($user_id);

    $states = array();
    if (function_exists('WC')) {
        $states = WC()->countries->get_states('IN');
    }
    if (empty($states)) {
        $states = array(
            'AN' => 'Andaman and Nicobar Islands',
            'AP' => 'Andhra Pradesh',
            'AR' => 'Arunachal Pradesh',
            'AS' => 'Assam',
            'BR' => 'Bihar',
            'CH' => 'Chandigarh',
            'CG' => 'Chhattisgarh',
            'DN' => 'Dadra and Nagar Haveli',
            'DD' => 'Daman and Diu',
            'DL' => 'Delhi',
            'GA' => 'Goa',
            'GJ' => 'Gujarat',
            'HR' => 'Haryana',
            'HP' => 'Himachal Pradesh',
            'JK' => 'Jammu and Kashmir',
            'JH' => 'Jharkhand',
            'KA' => 'Karnataka',
            'KL' => 'Kerala',
            'LD' => 'Lakshadweep',
            'MP' => 'Madhya Pradesh',
            'MH' => 'Maharashtra',
            'MN' => 'Manipur',
            'ML' => 'Meghalaya',
            'MZ' => 'Mizoram',
            'NL' => 'Nagaland',
            'OD' => 'Odisha',
            'PY' => 'Puducherry',
            'PB' => 'Punjab',
            'RJ' => 'Rajasthan',
            'SK' => 'Sikkim',
            'TN' => 'Tamil Nadu',
            'TG' => 'Telangana',
            'TR' => 'Tripura',
            'UP' => 'Uttar Pradesh',
            'UK' => 'Uttarakhand',
            'WB' => 'West Bengal'
        );
    }

    ?>
    <div class="delivery-addresses-header"
        style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="margin: 0; font-weight: 800; color: #0f172a; font-family: 'Outfit', sans-serif;">
            <?php esc_html_e('Delivery Addresses', 'woocommerce'); ?></h2>
        <button type="button" class="add-address-btn" id="open-add-address-modal"
            style="background: linear-gradient(135deg, #0ea5e9, #0284c7); color: #fff; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 700; cursor: pointer; box-shadow: 0 4px 6px -1px rgba(2, 132, 199, 0.3); transition: all 0.2s;">
            + Add New Address
        </button>
    </div>

    <div class="addresses-grid"
        style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; margin-bottom: 40px;">
        <?php if (empty($addresses)): ?>
            <div class="no-addresses-notice"
                style="grid-column: 1 / -1; background: #f8fafc; border: 2px dashed #cbd5e1; border-radius: 12px; padding: 40px; text-align: center; color: #64748b;">
                <p style="margin-bottom: 15px; font-size: 1.1rem; font-weight: 600;">
                    <?php esc_html_e('No saved delivery addresses found.', 'woocommerce'); ?></p>
                <button type="button" class="add-address-btn-inline"
                    style="background: #0284c7; color: #fff; border: none; padding: 8px 16px; border-radius: 6px; font-weight: 600; cursor: pointer;"><?php esc_html_e('Add Your First Address', 'woocommerce'); ?></button>
            </div>
        <?php else: ?>
            <?php foreach ($addresses as $addr):
                $badge_class = 'badge-' . strtolower($addr['address_type']);
                $address_text = esc_html($addr['address_1']);
                if (!empty($addr['address_2'])) {
                    $address_text .= ', ' . esc_html($addr['address_2']);
                }
                ?>
                <div class="address-card <?php echo $addr['is_default'] ? 'default-card' : ''; ?>"
                    style="background: #fff; border: 1px solid <?php echo $addr['is_default'] ? '#0284c7' : '#e2e8f0'; ?>; border-radius: 12px; padding: 20px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); position: relative; transition: all 0.2s ease; display: flex; flex-direction: column; justify-content: space-between;">
                    <?php if ($addr['is_default']): ?>
                        <div class="default-ribbon"
                            style="position: absolute; top: -10px; right: 15px; background: #0284c7; color: #fff; font-size: 0.7rem; font-weight: 700; padding: 3px 10px; border-radius: 20px; box-shadow: 0 2px 4px rgba(2, 132, 199, 0.3);">
                            <?php esc_html_e('DEFAULT ADDRESS', 'woocommerce'); ?></div>
                    <?php endif; ?>

                    <div>
                        <div style="display: flex; gap: 8px; align-items: center; margin-bottom: 12px;">
                            <span class="addr-type-badge <?php echo esc_attr($badge_class); ?>" style="font-size: 0.65rem; font-weight: 800; text-transform: uppercase; padding: 2px 8px; border-radius: 20px; color: #fff; <?php
                               if ($addr['address_type'] == 'Home')
                                   echo 'background: #0ea5e9;';
                               elseif ($addr['address_type'] == 'Office')
                                   echo 'background: #10b981;';
                               else
                                   echo 'background: #64748b;';
                               ?>"><?php echo esc_html($addr['address_type']); ?></span>
                        </div>

                        <h4 style="margin: 0 0 4px 0; font-weight: 700; color: #1e293b; font-size: 1.05rem;">
                            <?php echo esc_html($addr['name']); ?></h4>
                        <div style="font-size: 0.85rem; color: #64748b; margin-bottom: 12px; font-weight: 500;">
                            <svg style="width: 12px; height: 12px; display: inline; vertical-align: middle; margin-right: 4px;"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.725l.548 2.2a1 1 0 01-.321.988l-1.305.98a10.582 10.582 0 004.872 4.872l.98-1.305a1 1 0 01.988-.321l2.2.548a1 1 0 01.725.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                                </path>
                            </svg>
                            <?php echo esc_html($addr['phone']); ?>
                        </div>

                        <div class="address-details-text"
                            style="font-size: 0.88rem; line-height: 1.5; color: #334155; margin-bottom: 15px;">
                            <?php echo esc_html($address_text); ?><br>
                            <?php if (!empty($addr['area']))
                                echo esc_html($addr['area']) . '<br>'; ?>
                            <?php if (!empty($addr['landmark']))
                                echo '<span style="color:#64748b;">Landmark:</span> ' . esc_html($addr['landmark']) . '<br>'; ?>
                            <?php echo esc_html($addr['city'] . ', ' . (isset($states[$addr['state']]) ? $states[$addr['state']] : $addr['state']) . ' ' . $addr['postcode']); ?><br>
                            <?php echo esc_html($addr['country'] == 'IN' ? 'India' : $addr['country']); ?>
                        </div>
                    </div>

                    <div class="address-card-actions"
                        style="display: flex; gap: 10px; border-top: 1px solid #f1f5f9; padding-top: 12px; margin-top: auto;">
                        <button type="button" class="edit-address-btn"
                            data-address="<?php echo esc_attr(json_encode($addr)); ?>"
                            style="flex: 1; background: #f1f5f9; color: #334155; border: none; padding: 6px 12px; border-radius: 6px; font-size: 0.8rem; font-weight: 600; cursor: pointer; text-align: center; transition: all 0.2s;">Edit</button>
                        <button type="button" class="delete-address-btn" data-id="<?php echo esc_attr($addr['id']); ?>"
                            style="background: #fef2f2; color: #ef4444; border: none; padding: 6px 12px; border-radius: 6px; font-size: 0.8rem; font-weight: 600; cursor: pointer; transition: all 0.2s;">Delete</button>
                        <?php if (!$addr['is_default']): ?>
                            <button type="button" class="set-default-btn" data-id="<?php echo esc_attr($addr['id']); ?>"
                                style="background: #f0f9ff; color: #0284c7; border: none; padding: 6px 10px; border-radius: 6px; font-size: 0.8rem; font-weight: 600; cursor: pointer; transition: all 0.2s;">Set
                                Default</button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Beautiful Modal for Adding/Editing Address -->
    <div id="address-modal" class="address-modal-overlay"
        style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); z-index: 99999; justify-content: center; align-items: center; padding: 20px;">
        <div class="address-modal-container"
            style="background: #fff; border-radius: 16px; width: 100%; max-width: 520px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); display: flex; flex-direction: column; max-height: 90vh; overflow: hidden; animation: modalFadeIn 0.3s ease;">
            <div class="modal-header"
                style="padding: 20px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
                <h3 id="modal-title"
                    style="margin: 0; font-weight: 800; color: #0f172a; font-family: 'Outfit', sans-serif;">Add New Address
                </h3>
                <button type="button" id="close-modal-btn"
                    style="background: none; border: none; font-size: 1.5rem; color: #64748b; cursor: pointer; line-height: 1;">&times;</button>
            </div>

            <form id="address-form"
                style="display: flex; flex-direction: column; overflow-y: auto; padding: 20px; gap: 15px; margin: 0;">
                <input type="hidden" name="address_id" id="form-address-id" value="">

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="form-group">
                        <label for="form-name"
                            style="display: block; font-size: 0.8rem; font-weight: 700; color: #475569; margin-bottom: 5px;">Full
                            Name *</label>
                        <input type="text" name="name" id="form-name" required
                            style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem;">
                    </div>
                    <div class="form-group">
                        <label for="form-phone"
                            style="display: block; font-size: 0.8rem; font-weight: 700; color: #475569; margin-bottom: 5px;">Mobile
                            Number (10 digits) *</label>
                        <input type="tel" name="phone" id="form-phone" required pattern="[0-9]{10}"
                            placeholder="e.g. 9876543210"
                            style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem;">
                    </div>
                </div>

                <div class="form-group">
                    <label for="form-address-1"
                        style="display: block; font-size: 0.8rem; font-weight: 700; color: #475569; margin-bottom: 5px;">Address
                        Line 1 (Flat, House no., Building) *</label>
                    <input type="text" name="address_1" id="form-address-1" required
                        style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem;">
                </div>

                <div class="form-group">
                    <label for="form-address-2"
                        style="display: block; font-size: 0.8rem; font-weight: 700; color: #475569; margin-bottom: 5px;">Address
                        Line 2 (Street, Sector, Apartment)</label>
                    <input type="text" name="address_2" id="form-address-2"
                        style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem;">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="form-group">
                        <label for="form-area"
                            style="display: block; font-size: 0.8rem; font-weight: 700; color: #475569; margin-bottom: 5px;">Area
                            / Locality *</label>
                        <input type="text" name="area" id="form-area" required
                            style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem;">
                    </div>
                    <div class="form-group">
                        <label for="form-landmark"
                            style="display: block; font-size: 0.8rem; font-weight: 700; color: #475569; margin-bottom: 5px;">Landmark
                            (Optional)</label>
                        <input type="text" name="landmark" id="form-landmark" placeholder="e.g. Near Bus Stand"
                            style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem;">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px;">
                    <div class="form-group">
                        <label for="form-city"
                            style="display: block; font-size: 0.8rem; font-weight: 700; color: #475569; margin-bottom: 5px;">City
                            *</label>
                        <input type="text" name="city" id="form-city" required
                            style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem;">
                    </div>
                    <div class="form-group">
                        <label for="form-state"
                            style="display: block; font-size: 0.8rem; font-weight: 700; color: #475569; margin-bottom: 5px;">State
                            *</label>
                        <select name="state" id="form-state" required
                            style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem; background: #fff;">
                            <option value="">Select State</option>
                            <?php foreach ($states as $code => $name): ?>
                                <option value="<?php echo esc_attr($code); ?>"><?php echo esc_html($name); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="form-postcode"
                            style="display: block; font-size: 0.8rem; font-weight: 700; color: #475569; margin-bottom: 5px;">Pincode
                            *</label>
                        <input type="text" name="postcode" id="form-postcode" required pattern="[0-9]{6}"
                            placeholder="6 digits"
                            style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem;">
                    </div>
                </div>

                <div
                    style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; align-items: center; margin-top: 5px;">
                    <div class="form-group">
                        <label for="form-address-type"
                            style="display: block; font-size: 0.8rem; font-weight: 700; color: #475569; margin-bottom: 5px;">Address
                            Type *</label>
                        <select name="address_type" id="form-address-type" required
                            style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem; background: #fff;">
                            <option value="Home">Home (All day delivery)</option>
                            <option value="Office">Office (9 AM - 5 PM delivery)</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <div class="form-group" style="padding-top: 20px;">
                        <label
                            style="display: flex; align-items: center; font-size: 0.85rem; font-weight: 600; color: #334155; cursor: pointer;">
                            <input type="checkbox" name="is_default" id="form-is-default" value="1"
                                style="margin-right: 8px; width: 16px; height: 16px;">
                            Set as Default Address
                        </label>
                    </div>
                </div>

                <div id="modal-msg"
                    style="display: none; padding: 10px 12px; border-radius: 6px; font-size: 0.85rem; font-weight: 600;">
                </div>

                <div class="modal-footer" style="margin-top: 10px; display: flex; gap: 10px; justify-content: flex-end;">
                    <button type="button" id="cancel-modal-btn"
                        style="background: #f1f5f9; color: #334155; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 700; cursor: pointer; transition: all 0.2s;">Cancel</button>
                    <button type="submit" id="save-address-btn"
                        style="background: linear-gradient(135deg, #0ea5e9, #0284c7); color: #fff; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 700; cursor: pointer; box-shadow: 0 4px 6px -1px rgba(2, 132, 199, 0.3); transition: all 0.2s;">Save
                        Address</button>
                </div>
            </form>
        </div>
    </div>

    <style>
        @keyframes modalFadeIn {
            from {
                opacity: 0;
                transform: scale(0.95);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .address-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .address-card.default-card {
            border-color: #0284c7;
            background: #f0f9ff;
        }

        #address-modal input:focus,
        #address-modal select:focus {
            outline: none;
            border-color: #0ea5e9;
            box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.15);
        }
    </style>

    <script type="text/javascript">
        jQuery(document).ready(function ($) {
            var modal = $('#address-modal');
            var form = $('#address-form');
            var modalTitle = $('#modal-title');
            var msgDiv = $('#modal-msg');

            $('#open-add-address-modal, .add-address-btn-inline').click(function () {
                form[0].reset();
                $('#form-address-id').val('');
                modalTitle.text('Add New Address');
                $('#form-is-default').prop('checked', false).prop('disabled', false);
                msgDiv.hide().removeClass('success error');
                modal.css('display', 'flex');
            });

            $('#close-modal-btn, #cancel-modal-btn').click(function () {
                modal.hide();
            });

            modal.click(function (e) {
                if (e.target === this) {
                    modal.hide();
                }
            });

            $('.edit-address-btn').click(function () {
                var addr = $(this).data('address');
                form[0].reset();

                $('#form-address-id').val(addr.id);
                $('#form-name').val(addr.name);
                $('#form-phone').val(addr.phone);
                $('#form-address-1').val(addr.address_1);
                $('#form-address-2').val(addr.address_2);
                $('#form-area').val(addr.area);
                $('#form-landmark').val(addr.landmark);
                $('#form-city').val(addr.city);
                $('#form-state').val(addr.state);
                $('#form-postcode').val(addr.postcode);
                $('#form-address-type').val(addr.address_type);

                if (addr.is_default) {
                    $('#form-is-default').prop('checked', true).prop('disabled', true);
                } else {
                    $('#form-is-default').prop('checked', false).prop('disabled', false);
                }

                modalTitle.text('Edit Address');
                msgDiv.hide().removeClass('success error');
                modal.css('display', 'flex');
            });

            $('.delete-address-btn').click(function () {
                if (!confirm('Are you sure you want to delete this address?')) {
                    return;
                }
                var id = $(this).data('id');
                var $btn = $(this);

                $btn.prop('disabled', true).text('Deleting...');

                $.ajax({
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    type: 'POST',
                    data: {
                        action: 'wc_delivery_address_crud',
                        action_type: 'delete',
                        nonce: '<?php echo wp_create_nonce("wc_address_crud_nonce"); ?>',
                        address_id: id
                    },
                    success: function (response) {
                        if (response.success) {
                            location.reload();
                        } else {
                            alert(response.data);
                            $btn.prop('disabled', false).text('Delete');
                        }
                    }
                });
            });

            $('.set-default-btn').click(function () {
                var id = $(this).data('id');
                var $btn = $(this);

                $btn.prop('disabled', true).text('Updating...');

                $.ajax({
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    type: 'POST',
                    data: {
                        action: 'wc_delivery_address_crud',
                        action_type: 'set_default',
                        nonce: '<?php echo wp_create_nonce("wc_address_crud_nonce"); ?>',
                        address_id: id
                    },
                    success: function (response) {
                        if (response.success) {
                            location.reload();
                        } else {
                            alert(response.data);
                            $btn.prop('disabled', false).text('Set Default');
                        }
                    }
                });
            });

            form.submit(function (e) {
                e.preventDefault();

                var submitBtn = $('#save-address-btn');
                submitBtn.prop('disabled', true).text('Saving...');
                msgDiv.hide().removeClass('success error');

                $.ajax({
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    type: 'POST',
                    data: {
                        action: 'wc_delivery_address_crud',
                        action_type: 'save',
                        nonce: '<?php echo wp_create_nonce("wc_address_crud_nonce"); ?>',
                        address_id: $('#form-address-id').val(),
                        name: $('#form-name').val(),
                        phone: $('#form-phone').val(),
                        address_1: $('#form-address-1').val(),
                        address_2: $('#form-address-2').val(),
                        area: $('#form-area').val(),
                        landmark: $('#form-landmark').val(),
                        city: $('#form-city').val(),
                        state: $('#form-state').val(),
                        postcode: $('#form-postcode').val(),
                        address_type: $('#form-address-type').val(),
                        is_default: $('#form-is-default').is(':checked') ? 1 : 0
                    },
                    success: function (response) {
                        submitBtn.prop('disabled', false).text('Save Address');
                        if (response.success) {
                            msgDiv.css('background', '#ecfdf5').css('color', '#047857').text('Address saved successfully!').show();
                            setTimeout(function () {
                                location.reload();
                            }, 800);
                        } else {
                            msgDiv.css('background', '#fef2f2').css('color', '#b91c1c').text(response.data).show();
                        }
                    },
                    error: function () {
                        submitBtn.prop('disabled', false).text('Save Address');
                        msgDiv.css('background', '#fef2f2').css('color', '#b91c1c').text('An error occurred. Please try again.').show();
                    }
                });
            });
        });
    </script>
    <?php
}
add_action('woocommerce_account_delivery-addresses_endpoint', 'custom_delivery_addresses_content');

// 6. Checkout Page Integration
function custom_add_checkout_fields($fields)
{
    // Add to shipping fields
    $fields['shipping']['shipping_area'] = array(
        'type' => 'text',
        'label' => __('Area / Locality', 'woocommerce'),
        'placeholder' => __('e.g. East Coast Road', 'woocommerce'),
        'required' => true,
        'class' => array('form-row-wide'),
        'priority' => 55,
    );
    $fields['shipping']['shipping_landmark'] = array(
        'type' => 'text',
        'label' => __('Landmark', 'woocommerce'),
        'placeholder' => __('e.g. Opposite Temple', 'woocommerce'),
        'required' => false,
        'class' => array('form-row-wide'),
        'priority' => 56,
    );
    $fields['shipping']['shipping_phone'] = array(
        'type' => 'tel',
        'label' => __('Mobile Number', 'woocommerce'),
        'placeholder' => __('10-digit mobile number', 'woocommerce'),
        'required' => true,
        'class' => array('form-row-wide'),
        'priority' => 57,
    );
    $fields['shipping']['shipping_address_type'] = array(
        'type' => 'select',
        'label' => __('Address Type', 'woocommerce'),
        'options' => array(
            'Home' => __('Home', 'woocommerce'),
            'Office' => __('Office', 'woocommerce'),
            'Other' => __('Other', 'woocommerce'),
        ),
        'required' => true,
        'class' => array('form-row-wide'),
        'priority' => 58,
    );

    // Add to billing fields
    $fields['billing']['billing_area'] = array(
        'type' => 'text',
        'label' => __('Area / Locality', 'woocommerce'),
        'placeholder' => __('e.g. East Coast Road', 'woocommerce'),
        'required' => true,
        'class' => array('form-row-wide'),
        'priority' => 55,
    );
    $fields['billing']['billing_landmark'] = array(
        'type' => 'text',
        'label' => __('Landmark', 'woocommerce'),
        'placeholder' => __('e.g. Opposite Temple', 'woocommerce'),
        'required' => false,
        'class' => array('form-row-wide'),
        'priority' => 56,
    );
    $fields['billing']['billing_address_type'] = array(
        'type' => 'select',
        'label' => __('Address Type', 'woocommerce'),
        'options' => array(
            'Home' => __('Home', 'woocommerce'),
            'Office' => __('Office', 'woocommerce'),
            'Other' => __('Other', 'woocommerce'),
        ),
        'required' => true,
        'class' => array('form-row-wide'),
        'priority' => 58,
    );

    return $fields;
}
add_filter('woocommerce_checkout_fields', 'custom_add_checkout_fields');

function custom_checkout_address_carousel()
{
    if (!is_user_logged_in()) {
        return;
    }

    $user_id = get_current_user_id();
    $addresses = wc_get_saved_multiple_addresses($user_id);

    if (empty($addresses)) {
        return;
    }

    ?>
    <div class="wc-checkout-addresses-container">
        <h3><?php esc_html_e('Choose a Delivery Address', 'woocommerce'); ?></h3>
        <div class="wc-checkout-addresses-carousel">
            <?php foreach ($addresses as $addr):
                $badge_class = 'addr-badge-' . strtolower($addr['address_type']);
                $address_text = esc_html($addr['address_1']);
                if (!empty($addr['address_2'])) {
                    $address_text .= ', ' . esc_html($addr['address_2']);
                }
                $full_address = sprintf(
                    '%s, %s, %s, %s - %s',
                    $address_text,
                    esc_html($addr['area']),
                    esc_html($addr['city']),
                    esc_html($addr['state']),
                    esc_html($addr['postcode'])
                );
                $parts = explode(' ', trim($addr['name']), 2);
                $first_name = isset($parts[0]) ? $parts[0] : '';
                $last_name = isset($parts[1]) ? $parts[1] : '';
                ?>
                <div class="wc-checkout-address-card <?php echo $addr['is_default'] ? 'active-address' : ''; ?>"
                    data-address-id="<?php echo esc_attr($addr['id']); ?>"
                    data-first-name="<?php echo esc_attr($first_name); ?>" data-last-name="<?php echo esc_attr($last_name); ?>"
                    data-phone="<?php echo esc_attr($addr['phone']); ?>"
                    data-address-1="<?php echo esc_attr($addr['address_1']); ?>"
                    data-address-2="<?php echo esc_attr($addr['address_2']); ?>"
                    data-area="<?php echo esc_attr($addr['area']); ?>" data-city="<?php echo esc_attr($addr['city']); ?>"
                    data-state="<?php echo esc_attr($addr['state']); ?>"
                    data-postcode="<?php echo esc_attr($addr['postcode']); ?>"
                    data-landmark="<?php echo esc_attr($addr['landmark']); ?>"
                    data-address-type="<?php echo esc_attr($addr['address_type']); ?>">

                    <div class="address-card-header">
                        <span
                            class="address-card-type <?php echo esc_attr($badge_class); ?>"><?php echo esc_html($addr['address_type']); ?></span>
                        <?php if ($addr['is_default']): ?>
                            <span class="address-card-default-lbl"><?php esc_html_e('Default', 'woocommerce'); ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="address-card-body">
                        <div class="address-card-name"><?php echo esc_html($addr['name']); ?></div>
                        <div class="address-card-phone"><?php echo esc_html($addr['phone']); ?></div>
                        <div class="address-card-text"><?php echo esc_html($full_address); ?></div>
                        <?php if (!empty($addr['landmark'])): ?>
                            <div class="address-card-landmark"><strong>Landmark:</strong> <?php echo esc_html($addr['landmark']); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>

            <div class="wc-checkout-address-card add-new-checkout-card" data-address-id="new">
                <div class="add-new-checkout-card-content">
                    <span class="add-new-icon">+</span>
                    <span><?php esc_html_e('Deliver to a New Address', 'woocommerce'); ?></span>
                </div>
            </div>
        </div>
        <input type="hidden" name="selected_delivery_address_id" id="selected_delivery_address_id" value="<?php
        foreach ($addresses as $addr) {
            if ($addr['is_default']) {
                echo esc_attr($addr['id']);
                break;
            }
        }
        ?>">
    </div>

    <style>
        .wc-checkout-addresses-container {
            margin-bottom: 25px;
            padding: 15px;
            background: #fff;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        .wc-checkout-addresses-container h3 {
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 12px;
            color: #0f172a;
        }

        .wc-checkout-addresses-carousel {
            display: flex;
            gap: 15px;
            overflow-x: auto;
            padding-bottom: 12px;
            scrollbar-width: thin;
        }

        .wc-checkout-addresses-carousel::-webkit-scrollbar {
            height: 6px;
        }

        .wc-checkout-addresses-carousel::-webkit-scrollbar-thumb {
            background-color: #cbd5e1;
            border-radius: 3px;
        }

        .wc-checkout-address-card {
            flex: 0 0 260px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            padding: 15px;
            cursor: pointer;
            background: #f8fafc;
            transition: all 0.2s ease-in-out;
            position: relative;
            user-select: none;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .wc-checkout-address-card:hover {
            border-color: #94a3b8;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .wc-checkout-address-card.active-address {
            border-color: #0284c7;
            background: #f0f9ff;
            box-shadow: 0 4px 12px rgba(2, 132, 199, 0.08);
        }

        .wc-checkout-address-card.active-address::after {
            content: "✓";
            position: absolute;
            top: -10px;
            right: -10px;
            background: #0284c7;
            color: white;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: bold;
            border: 2px solid #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .address-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .address-card-type {
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            padding: 2px 6px;
            border-radius: 4px;
            color: #fff;
        }

        .addr-badge-home {
            background-color: #0ea5e9;
        }

        .addr-badge-office {
            background-color: #10b981;
        }

        .addr-badge-other {
            background-color: #64748b;
        }

        .address-card-default-lbl {
            font-size: 0.7rem;
            background: #e2e8f0;
            color: #334155;
            padding: 2px 6px;
            border-radius: 4px;
            font-weight: 600;
        }

        .address-card-name {
            font-weight: 700;
            color: #1e293b;
            font-size: 0.95rem;
            margin-bottom: 2px;
        }

        .address-card-phone {
            font-size: 0.85rem;
            color: #64748b;
            margin-bottom: 8px;
        }

        .address-card-text {
            font-size: 0.82rem;
            line-height: 1.4;
            color: #334155;
            word-break: break-word;
        }

        .address-card-landmark {
            font-size: 0.8rem;
            color: #475569;
            margin-top: 5px;
            padding-top: 5px;
            border-top: 1px dashed #e2e8f0;
        }

        .add-new-checkout-card {
            border: 2px dashed #cbd5e1;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .add-new-checkout-card:hover {
            border-color: #0284c7;
            background: #f8fafc;
        }

        .add-new-checkout-card-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            color: #64748b;
            font-weight: 600;
            font-size: 0.85rem;
        }

        .add-new-checkout-card:hover .add-new-checkout-card-content {
            color: #0284c7;
        }

        .add-new-icon {
            font-size: 1.8rem;
            margin-bottom: 5px;
            line-height: 1;
        }

        .save-to-account-checkout-field {
            margin: 15px 0;
            padding: 10px;
            background: #f8fafc;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            display: inline-block;
            width: 100%;
        }
    </style>

    <script type="text/javascript">
        jQuery(document).ready(function ($) {
            function autofillShippingAddress(card) {
                if (!card || card.data('address-id') === 'new') {
                    // Clear billing fields
                    $('#billing_first_name').val('');
                    $('#billing_last_name').val('');
                    $('#billing_phone').val('');
                    $('#billing_address_1').val('');
                    $('#billing_address_2').val('');
                    $('#billing_area').val('');
                    $('#billing_city').val('');
                    $('#billing_state').val('').trigger('change');
                    $('#billing_custom_pincode').val('').trigger('change');
                    $('#billing_landmark').val('');
                    $('#billing_address_type').val('Home');

                    // Clear shipping fields
                    $('#shipping_first_name').val('');
                    $('#shipping_last_name').val('');
                    $('#shipping_phone').val('');
                    $('#shipping_address_1').val('');
                    $('#shipping_address_2').val('');
                    $('#shipping_area').val('');
                    $('#shipping_city').val('');
                    $('#shipping_state').val('').trigger('change');
                    $('#shipping_postcode').val('');
                    $('#shipping_landmark').val('');
                    $('#shipping_address_type').val('Home');

                    $('#selected_delivery_address_id').val('new');
                    $('.save-to-account-checkout-field').show();
                    return;
                }

                $('.save-to-account-checkout-field').hide();
                $('#save_to_account').prop('checked', false);

                $('#selected_delivery_address_id').val(card.data('address-id'));

                // Populate billing fields
                $('#billing_first_name').val(card.data('first-name'));
                $('#billing_last_name').val(card.data('last-name'));
                $('#billing_phone').val(card.data('phone'));
                $('#billing_address_1').val(card.data('address-1'));
                $('#billing_address_2').val(card.data('address-2'));
                $('#billing_area').val(card.data('area'));
                $('#billing_city').val(card.data('city'));
                $('#billing_state').val(card.data('state')).trigger('change');
                $('#billing_landmark').val(card.data('landmark'));
                $('#billing_address_type').val(card.data('address-type'));

                // Populate shipping fields
                $('#shipping_first_name').val(card.data('first-name'));
                $('#shipping_last_name').val(card.data('last-name'));
                $('#shipping_phone').val(card.data('phone'));
                $('#shipping_address_1').val(card.data('address-1'));
                $('#shipping_address_2').val(card.data('address-2'));
                $('#shipping_area').val(card.data('area'));
                $('#shipping_city').val(card.data('city'));
                $('#shipping_state').val(card.data('state')).trigger('change');
                $('#shipping_postcode').val(card.data('postcode'));
                $('#shipping_landmark').val(card.data('landmark'));
                $('#shipping_address_type').val(card.data('address-type'));

                // Custom Pincode Dropdown handling
                var postcode = card.data('postcode');
                var $pincodeSelect = $('#billing_custom_pincode');
                if ($pincodeSelect.length) {
                    if (!$pincodeSelect.find("option[value='" + postcode + "']").length) {
                        $pincodeSelect.append(new Option(postcode, postcode));
                    }
                    $pincodeSelect.val(postcode).trigger('change');
                }

                var $shipToDiff = $('#ship-to-different-address-checkbox');
                if ($shipToDiff.length && !$shipToDiff.is(':checked')) {
                    $shipToDiff.prop('checked', true).trigger('change');
                }
            }

            $(document).on('click', '.wc-checkout-address-card', function () {
                $('.wc-checkout-address-card').removeClass('active-address');
                $(this).addClass('active-address');
                autofillShippingAddress($(this));
            });

            var $activeCard = $('.wc-checkout-address-card.active-address');
            if ($activeCard.length) {
                autofillShippingAddress($activeCard);
            }
        });
    </script>
    <?php
}
add_action('woocommerce_before_checkout_billing_form', 'custom_checkout_address_carousel');

function custom_checkout_save_to_account_checkbox()
{
    if (!is_user_logged_in()) {
        return;
    }
    ?>
    <div class="save-to-account-checkout-field" style="display:none;">
        <label class="woocommerce-form__label woocommerce-form__label-for-checkbox checkbox">
            <input class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox" type="checkbox"
                name="save_to_account" id="save_to_account" value="1">
            <span><?php esc_html_e('Save this address to my account for future orders', 'woocommerce'); ?></span>
        </label>
    </div>
    <?php
}
add_action('woocommerce_after_checkout_billing_form', 'custom_checkout_save_to_account_checkbox');

// Save checkout custom meta values to order
function custom_save_checkout_fields($order_id)
{
    $order = wc_get_order($order_id);
    if (!$order)
        return;

    // Save shipping custom fields
    if (isset($_POST['shipping_area'])) {
        $order->update_meta_data('_shipping_area', sanitize_text_field($_POST['shipping_area']));
    }
    if (isset($_POST['shipping_landmark'])) {
        $order->update_meta_data('_shipping_landmark', sanitize_text_field($_POST['shipping_landmark']));
    }
    if (isset($_POST['shipping_phone'])) {
        $order->update_meta_data('_shipping_phone', sanitize_text_field($_POST['shipping_phone']));
    }
    if (isset($_POST['shipping_address_type'])) {
        $order->update_meta_data('_shipping_address_type', sanitize_text_field($_POST['shipping_address_type']));
    }

    // Save billing custom fields and sync to shipping if shipping is empty
    if (isset($_POST['billing_area'])) {
        $order->update_meta_data('_billing_area', sanitize_text_field($_POST['billing_area']));
        if (empty($_POST['shipping_area'])) {
            $order->update_meta_data('_shipping_area', sanitize_text_field($_POST['billing_area']));
        }
    }
    if (isset($_POST['billing_landmark'])) {
        $order->update_meta_data('_billing_landmark', sanitize_text_field($_POST['billing_landmark']));
        if (empty($_POST['shipping_landmark'])) {
            $order->update_meta_data('_shipping_landmark', sanitize_text_field($_POST['billing_landmark']));
        }
    }
    if (isset($_POST['billing_phone'])) {
        if (empty($_POST['shipping_phone'])) {
            $order->update_meta_data('_shipping_phone', sanitize_text_field($_POST['billing_phone']));
        }
    }
    if (isset($_POST['billing_address_type'])) {
        $order->update_meta_data('_billing_address_type', sanitize_text_field($_POST['billing_address_type']));
        if (empty($_POST['shipping_address_type'])) {
            $order->update_meta_data('_shipping_address_type', sanitize_text_field($_POST['billing_address_type']));
        }
    }

    if (isset($_POST['selected_delivery_address_id'])) {
        $order->update_meta_data('_delivery_address_id', sanitize_text_field($_POST['selected_delivery_address_id']));
    }

    $order->save();
}
add_action('woocommerce_checkout_update_order_meta', 'custom_save_checkout_fields');

// Save new address checkbox handling
function custom_save_checkout_address_to_profile($order_id, $posted_data)
{
    if (!is_user_logged_in()) {
        return;
    }
    if (isset($_POST['save_to_account']) && $_POST['save_to_account'] == '1') {
        $user_id = get_current_user_id();

        $first_name = !empty($_POST['shipping_first_name']) ? sanitize_text_field($_POST['shipping_first_name']) : (!empty($_POST['billing_first_name']) ? sanitize_text_field($_POST['billing_first_name']) : '');
        $last_name = !empty($_POST['shipping_last_name']) ? sanitize_text_field($_POST['shipping_last_name']) : (!empty($_POST['billing_last_name']) ? sanitize_text_field($_POST['billing_last_name']) : '');
        $address_1 = !empty($_POST['shipping_address_1']) ? sanitize_text_field($_POST['shipping_address_1']) : (!empty($_POST['billing_address_1']) ? sanitize_text_field($_POST['billing_address_1']) : '');
        $address_2 = !empty($_POST['shipping_address_2']) ? sanitize_text_field($_POST['shipping_address_2']) : (!empty($_POST['billing_address_2']) ? sanitize_text_field($_POST['billing_address_2']) : '');
        $city = !empty($_POST['shipping_city']) ? sanitize_text_field($_POST['shipping_city']) : (!empty($_POST['billing_city']) ? sanitize_text_field($_POST['billing_city']) : '');
        $state = !empty($_POST['shipping_state']) ? sanitize_text_field($_POST['shipping_state']) : (!empty($_POST['billing_state']) ? sanitize_text_field($_POST['billing_state']) : '');

        $postcode = '';
        if (!empty($_POST['shipping_postcode'])) {
            $postcode = sanitize_text_field($_POST['shipping_postcode']);
        } elseif (!empty($_POST['billing_custom_pincode'])) {
            $postcode = sanitize_text_field($_POST['billing_custom_pincode']);
        } elseif (!empty($_POST['billing_postcode'])) {
            $postcode = sanitize_text_field($_POST['billing_postcode']);
        }

        $country = !empty($_POST['shipping_country']) ? sanitize_text_field($_POST['shipping_country']) : (!empty($_POST['billing_country']) ? sanitize_text_field($_POST['billing_country']) : 'IN');
        $area = !empty($_POST['shipping_area']) ? sanitize_text_field($_POST['shipping_area']) : (!empty($_POST['billing_area']) ? sanitize_text_field($_POST['billing_area']) : '');
        $landmark = !empty($_POST['shipping_landmark']) ? sanitize_text_field($_POST['shipping_landmark']) : (!empty($_POST['billing_landmark']) ? sanitize_text_field($_POST['billing_landmark']) : '');
        $phone = !empty($_POST['shipping_phone']) ? sanitize_text_field($_POST['shipping_phone']) : (!empty($_POST['billing_phone']) ? sanitize_text_field($_POST['billing_phone']) : '');
        $address_type = !empty($_POST['shipping_address_type']) ? sanitize_text_field($_POST['shipping_address_type']) : (!empty($_POST['billing_address_type']) ? sanitize_text_field($_POST['billing_address_type']) : 'Home');

        $address_data = array(
            'id' => 'addr_' . uniqid() . '_' . time(),
            'address_name' => $address_type . ' (' . $city . ')',
            'name' => trim($first_name . ' ' . $last_name),
            'phone' => $phone,
            'address_1' => $address_1,
            'address_2' => $address_2,
            'area' => $area,
            'city' => $city,
            'state' => $state,
            'postcode' => $postcode,
            'country' => $country,
            'landmark' => $landmark,
            'address_type' => $address_type,
            'is_default' => false,
        );

        wc_save_user_multiple_address($user_id, $address_data);

        $order = wc_get_order($order_id);
        if ($order) {
            $order->update_meta_data('_delivery_address_id', $address_data['id']);
            $order->save();
        }
    }
}
add_action('woocommerce_checkout_order_processed', 'custom_save_checkout_address_to_profile', 10, 2);

// 7. Formatted Address Integration for emails, order details & WooCommerce admin
function custom_order_formatted_shipping_address($address, $order)
{
    $address['area'] = $order->get_meta('_shipping_area', true);
    $address['landmark'] = $order->get_meta('_shipping_landmark', true);
    $address['phone'] = $order->get_meta('_shipping_phone', true);
    $address['address_type'] = $order->get_meta('_shipping_address_type', true);
    return $address;
}
add_filter('woocommerce_order_formatted_shipping_address', 'custom_order_formatted_shipping_address', 10, 2);

function custom_localisation_address_formats($formats)
{
    if (isset($formats['IN'])) {
        $formats['IN'] = "{name}\n{company}\n{address_1}\n{address_2}\n{area}\n{landmark}\n{city} {state} {postcode}\n{country}\nPhone: {phone}\nType: {address_type}";
    } else {
        $formats['default'] = "{name}\n{company}\n{address_1}\n{address_2}\n{area}\n{landmark}\n{city} {state} {postcode}\n{country}\nPhone: {phone}\nType: {address_type}";
    }
    return $formats;
}
add_filter('woocommerce_localisation_address_formats', 'custom_localisation_address_formats');

function custom_formatted_address_replacements($replacements, $args)
{
    $replacements['{area}'] = !empty($args['area']) ? $args['area'] : '';
    $replacements['{landmark}'] = !empty($args['landmark']) ? 'Landmark: ' . $args['landmark'] : '';
    $replacements['{phone}'] = !empty($args['phone']) ? $args['phone'] : '';
    $replacements['{address_type}'] = !empty($args['address_type']) ? $args['address_type'] : '';
    return $replacements;
}
add_filter('woocommerce_formatted_address_replacements', 'custom_formatted_address_replacements', 10, 2);

// Edit custom address fields natively in WooCommerce Admin Order Screen
function custom_admin_shipping_fields($fields)
{
    $fields['area'] = array(
        'label' => __('Area / Locality', 'woocommerce'),
        'show' => true,
    );
    $fields['landmark'] = array(
        'label' => __('Landmark', 'woocommerce'),
        'show' => true,
    );
    $fields['phone'] = array(
        'label' => __('Mobile Number', 'woocommerce'),
        'show' => true,
    );
    $fields['address_type'] = array(
        'label' => __('Address Type', 'woocommerce'),
        'show' => true,
    );
    return $fields;
}
add_filter('woocommerce_admin_shipping_fields', 'custom_admin_shipping_fields');



// Hide Cancel Order button from My Account -> Orders page
add_filter('woocommerce_my_account_my_orders_actions', 'custom_remove_cancel_order_action', 100, 2);
function custom_remove_cancel_order_action($actions, $order)
{
    if (isset($actions['cancel'])) {
        unset($actions['cancel']);
    }
    return $actions;
}

// Add WhatsApp Floating Button
add_action('wp_footer', 'add_whatsapp_floating_button');
function add_whatsapp_floating_button()
{
    ?>
    <style>
        .whatsapp-float {
            position: fixed;
            width: 60px;
            height: 60px;
            bottom: 40px;
            right: 40px;
            background-color: #25d366;
            color: #FFF;
            border-radius: 50px;
            text-align: center;
            font-size: 30px;
            box-shadow: 2px 2px 3px #999;
            z-index: 999999;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            animation: pulse-animation 2s infinite;
        }


        .whatsapp-float:hover {
            color: #FFF;
            background-color: #128C7E;
        }

        .whatsapp-float svg {
            width: 35px;
            height: 35px;
            fill: white;
        }

        @keyframes pulse-animation {
            0% {
                box-shadow: 0 0 0 0 rgba(37, 211, 102, 0.7);
            }

            70% {
                box-shadow: 0 0 0 15px rgba(37, 211, 102, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(37, 211, 102, 0);
            }
        }
    </style>
    <a href="https://wa.me/918904989004" class="whatsapp-float" target="_blank" rel="noopener noreferrer"
        aria-label="WhatsApp Us">
        <svg xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 448 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
            <path
                d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zM223.9 413.6c-33 0-65.3-8.9-93.5-25.7l-6.7-4-69.5 18.2 18.6-67.8-4.4-7c-18.4-29.3-28.2-63.5-28.2-98.3 0-101.9 83-184.9 185-184.9 54.3 0 105.4 21.1 143.8 59.5 38.4 38.4 59.5 89.5 59.5 143.8 0 101.9-82.9 184.9-184.9 184.9zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-32.6-16.3-54-29.1-75.5-66-5.7-9.8 5.7-9.1 16.3-30.3 1.8-3.7 .9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3s19.9 53.7 22.7 57.4c2.8 3.7 39.1 59.7 94.8 83.8 35.2 15.2 49 16.5 66.6 13.9 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z" />
        </svg>
    </a>
    <?php
}

// ================================================================
// ADMIN DELIVERY PDF DOWNLOAD – Generate PDF for delivery status
// Triggered by ?ads_pdf=1&delivery_date=YYYY-MM-DD on today-delivery page
// ================================================================
add_action('template_redirect', 'handle_admin_delivery_pdf_download');
function handle_admin_delivery_pdf_download()
{
    if ((!isset($_GET['ads_pdf']) || $_GET['ads_pdf'] !== '1') && (!isset($_GET['download_pdf']) || $_GET['download_pdf'] !== '1')) return;
    if (!current_user_can('manage_woocommerce') && !in_array('administrator', wp_get_current_user()->roles) && !in_array('staff', wp_get_current_user()->roles)) return;

    $selected = date('Y-m-d');
    if (isset($_GET['delivery_date']) && !empty($_GET['delivery_date'])) {
        $selected = sanitize_text_field($_GET['delivery_date']);
    } elseif (isset($_GET['selected_date']) && !empty($_GET['selected_date'])) {
        $selected = sanitize_text_field($_GET['selected_date']);
    }
    // Normalize date to Y-m-d
    $selected = date('Y-m-d', strtotime($selected));
    $display_date = date('d-m-Y', strtotime($selected));

    $status_labels = [
        'pending' => 'Pending', 'confirmed' => 'Confirmed', 'preparing' => 'Preparing',
        'packed' => 'Packed', 'out_for_delivery' => 'Out for Delivery', 'delivered' => 'Delivered',
        'rescheduled' => 'Rescheduled', 'skipped' => 'Skipped', 'cancelled' => 'Cancelled',
    ];

    $orders = wc_get_orders(['limit' => -1, 'status' => ['processing', 'completed', 'on-hold']]);
    $rows = [];

    foreach ($orders as $order) {
        $order_id   = $order->get_id();
        $first_name = $order->get_billing_first_name();
        $last_name  = $order->get_billing_last_name();
        $phone      = $order->get_billing_phone();
        $apartment  = $order->get_meta('_billing_apartment', true) ?: $order->get_shipping_address_2();
        $address    = $order->get_shipping_address_1();
        $city       = $order->get_shipping_city();
        $area       = $order->get_meta('_shipping_area', true);

        foreach ($order->get_items() as $item_id => $item) {
            $delivery_dates = $item->get_meta('_delivery_times', true);
            if (empty($delivery_dates) || !is_array($delivery_dates)) continue;

            foreach ($delivery_dates as $date) {
                $normalized = date('Y-m-d', strtotime($date));
                if ($normalized !== $selected) continue;

                $status_key = $item->get_meta('_delivery_status_' . $date) ?: 'pending';
                $status_label = isset($status_labels[$status_key]) ? $status_labels[$status_key] : ucfirst($status_key);
                $delivery_time = $item->get_meta('_delivery_time', true);

                $rows[] = [
                    'order_id'      => $order_id,
                    'customer'      => trim($first_name . ' ' . $last_name),
                    'phone'         => $phone,
                    'product'       => $item->get_name(),
                    'qty'           => $item->get_quantity(),
                    'apartment'     => $apartment,
                    'address'       => trim($address . ($area ? ', ' . $area : '') . ($city ? ', ' . $city : '')),
                    'delivery_time' => $delivery_time,
                    'status'        => $status_label,
                    'status_key'    => $status_key,
                ];
            }
        }
    }

    // Output as printable HTML page that auto-triggers browser print-to-PDF
    header('Content-Type: text/html; charset=UTF-8');

    $site_name = get_bloginfo('name');
    ?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Delivery Report - <?php echo esc_html($display_date); ?></title>
<style>
    @media print { body { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; } .no-print { display:none !important; } @page { size: A4 landscape; margin: 10mm; } }
    * { margin:0; padding:0; box-sizing:border-box; }
    body { font-family: 'Segoe UI', Arial, sans-serif; font-size: 12px; color: #1e293b; padding: 20px; background: #fff; }
    .header { text-align:center; margin-bottom: 20px; border-bottom: 2px solid #1e293b; padding-bottom: 12px; }
    .header h1 { font-size: 20px; color: #1e293b; margin-bottom: 4px; }
    .header h2 { font-size: 15px; color: #475569; font-weight: 500; }
    .header .site-name { font-size: 22px; font-weight: 800; color: #1e293b; }
    .summary { display: flex; gap: 20px; margin-bottom: 16px; flex-wrap: wrap; }
    .summary-card { background: #f1f5f9; border-radius: 6px; padding: 8px 16px; font-size: 13px; }
    .summary-card strong { color: #0f172a; }
    table { width:100%; border-collapse:collapse; margin-top:10px; font-size: 11px; }
    th { background: #1e293b; color: #fff; padding: 8px 10px; text-align: left; font-size: 11px; font-weight: 600; }
    td { padding: 7px 10px; border-bottom: 1px solid #e2e8f0; vertical-align: top; }
    tr:nth-child(even) td { background: #f8fafc; }
    .status-badge { display:inline-block; padding:3px 8px; border-radius:10px; color:#fff; font-size:10px; font-weight:700; text-transform:uppercase; }
    .status-pending { background:#f39c12; } .status-confirmed { background:#3498db; }
    .status-preparing { background:#e67e22; } .status-packed { background:#9b59b6; }
    .status-out_for_delivery { background:#1abc9c; } .status-delivered { background:#27ae60; }
    .status-rescheduled { background:#e74c3c; } .status-skipped { background:#95a5a6; }
    .status-cancelled { background:#34495e; }
    .print-btn { display:inline-block; padding:10px 24px; background:#1e293b; color:#fff; border:none; border-radius:6px; font-size:14px; font-weight:600; cursor:pointer; margin-bottom:16px; }
    .print-btn:hover { background:#0f172a; }
    .footer { margin-top:20px; text-align:center; font-size:10px; color:#94a3b8; border-top:1px solid #e2e8f0; padding-top:8px; }
</style>
</head>
<body>
    <div class="no-print" style="text-align:center; margin-bottom:20px;">
        <button class="print-btn" onclick="window.print();">🖨️ Print / Save as PDF</button>
        <button class="print-btn" onclick="window.history.back();" style="background:#64748b;">← Back</button>
    </div>

    <div class="header">
        <div class="site-name"><?php echo esc_html($site_name); ?></div>
        <h1>Delivery Report</h1>
        <h2>Date: <?php echo esc_html($display_date); ?></h2>
    </div>

    <div class="summary">
        <div class="summary-card"><strong>Total Deliveries:</strong> <?php echo count($rows); ?></div>
        <div class="summary-card"><strong>Delivered:</strong> <?php echo count(array_filter($rows, function($r){ return $r['status_key'] === 'delivered'; })); ?></div>
        <div class="summary-card"><strong>Pending:</strong> <?php echo count(array_filter($rows, function($r){ return $r['status_key'] === 'pending'; })); ?></div>
        <div class="summary-card"><strong>Confirmed:</strong> <?php echo count(array_filter($rows, function($r){ return $r['status_key'] === 'confirmed'; })); ?></div>
    </div>

    <?php if (empty($rows)): ?>
        <p style="text-align:center; padding:40px; color:#94a3b8; font-size:16px;">No deliveries scheduled for <?php echo esc_html($display_date); ?></p>
    <?php else: ?>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Order ID</th>
                <th>Customer Name</th>
                <th>Phone</th>
                <th>Product</th>
                <th>Qty</th>
                <th>Apartment</th>
                <th>Address</th>
                <th>Time</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
        <?php $i=1; foreach ($rows as $row): ?>
            <tr>
                <td><?php echo $i++; ?></td>
                <td><?php echo esc_html($row['order_id']); ?></td>
                <td><strong><?php echo esc_html($row['customer']); ?></strong></td>
                <td><?php echo esc_html($row['phone']); ?></td>
                <td><?php echo esc_html($row['product']); ?></td>
                <td><?php echo esc_html($row['qty']); ?></td>
                <td><?php echo esc_html($row['apartment']); ?></td>
                <td><?php echo esc_html($row['address']); ?></td>
                <td><?php echo esc_html($row['delivery_time']); ?></td>
                <td><span class="status-badge status-<?php echo esc_attr($row['status_key']); ?>"><?php echo esc_html($row['status']); ?></span></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>

    <div class="footer">
        Generated on <?php echo date('d-m-Y h:i A'); ?> | <?php echo esc_html($site_name); ?>
    </div>

    <script>
    // Auto-open print dialog after 500ms
    setTimeout(function() { window.print(); }, 500);
    </script>
</body>
</html>
    <?php
    exit;
}

// ================================================================
// ADMIN DELIVERY STATUS UPDATE – AJAX Handler + Shortcode
// Use shortcode [admin_delivery_status_table] on the today-delivery page
// ================================================================

// AJAX: Update per-date delivery status (admin only)
add_action('wp_ajax_update_item_delivery_status', 'handle_update_item_delivery_status');
function handle_update_item_delivery_status()
{
    if (!current_user_can('manage_woocommerce') && !in_array('administrator', wp_get_current_user()->roles) && !in_array('staff', wp_get_current_user()->roles)) {
        wp_send_json_error(['message' => 'Unauthorized']);
    }

    $item_id   = isset($_POST['item_id'])   ? intval($_POST['item_id'])                       : 0;
    $date      = isset($_POST['date'])      ? sanitize_text_field($_POST['date'])              : '';
    $new_status = isset($_POST['new_status']) ? sanitize_text_field($_POST['new_status'])      : '';
    $notes     = isset($_POST['notes'])     ? sanitize_textarea_field($_POST['notes'])         : '';
    $order_id  = isset($_POST['order_id'])  ? intval($_POST['order_id'])                       : 0;

    $allowed_statuses = ['pending','confirmed','preparing','packed','out_for_delivery','delivered','rescheduled','skipped','cancelled'];
    if (!$item_id || !$date || !in_array($new_status, $allowed_statuses)) {
        wp_send_json_error(['message' => 'Invalid data']);
    }

    $item = new WC_Order_Item_Product($item_id);
    $item->update_meta_data('_delivery_status_' . $date, $new_status);
    if (!empty($notes)) {
        $item->update_meta_data('_delivery_notes_' . $date, $notes);
    }
    $item->save();

    // Optionally send email notification to customer
    if ($order_id && $new_status !== 'pending') {
        send_delivery_status_update_email($order_id, $item_id, $date, $new_status);
    }

    wp_send_json_success(['message' => 'Status updated to ' . $new_status]);
}

// Shortcode: [admin_delivery_status_table]
// Renders a table of deliveries for a chosen date with inline status dropdowns
add_shortcode('admin_delivery_status_table', 'render_admin_delivery_status_table');
function render_admin_delivery_status_table()
{
    if (!current_user_can('manage_woocommerce') && !in_array('administrator', wp_get_current_user()->roles) && !in_array('staff', wp_get_current_user()->roles)) {
        return '<p>Access denied.</p>';
    }

    $today     = date('Y-m-d');
    $tomorrow  = date('Y-m-d', strtotime('+1 day'));
    $selected  = isset($_GET['delivery_date']) ? sanitize_text_field($_GET['delivery_date']) : $today;

    $status_colors = [
        'pending'          => '#f39c12',
        'confirmed'        => '#3498db',
        'preparing'        => '#e67e22',
        'packed'           => '#9b59b6',
        'out_for_delivery' => '#1abc9c',
        'delivered'        => '#27ae60',
        'rescheduled'      => '#e74c3c',
        'skipped'          => '#95a5a6',
        'cancelled'        => '#34495e',
    ];
    $all_statuses = array_keys($status_colors);

    // Fetch all orders with delivery dates
    $orders = wc_get_orders(['limit' => -1, 'status' => ['processing', 'completed', 'on-hold']]);

    $rows = [];
    foreach ($orders as $order) {
        $order_id   = $order->get_id();
        $first_name = $order->get_billing_first_name();
        $last_name  = $order->get_billing_last_name();
        $apartment  = $order->get_meta('_billing_apartment', true) ?: $order->get_shipping_address_2();

        foreach ($order->get_items() as $item_id => $item) {
            $delivery_dates = $item->get_meta('_delivery_times', true);
            if (empty($delivery_dates) || !is_array($delivery_dates)) continue;

            foreach ($delivery_dates as $date) {
                // Normalize date to Y-m-d
                $normalized = date('Y-m-d', strtotime($date));
                if ($normalized !== $selected) continue;

                $status = $item->get_meta('_delivery_status_' . $date) ?: 'pending';
                $rows[] = [
                    'order_id'   => $order_id,
                    'item_id'    => $item_id,
                    'date'       => $date,
                    'customer'   => trim($first_name . ' ' . $last_name),
                    'product'    => $item->get_name(),
                    'qty'        => $item->get_quantity(),
                    'apartment'  => $apartment,
                    'status'     => $status,
                ];
            }
        }
    }

    $display_date = date('d-m-Y', strtotime($selected));
    $current_url  = strtok($_SERVER['REQUEST_URI'], '?');

    ob_start();
    ?>
    <style>
        .ads-wrap { font-family: sans-serif; margin: 20px 0; }
        .ads-filter-bar { display:flex; gap:10px; flex-wrap:wrap; margin-bottom:16px; align-items:center; }
        .ads-filter-bar input[type=date] { padding:8px 12px; border:1px solid #ccc; border-radius:6px; font-size:14px; }
        .ads-btn { padding:8px 16px; border:none; border-radius:6px; cursor:pointer; font-size:14px; font-weight:600; text-decoration:none; display:inline-block; }
        .ads-btn-dark  { background:#1e293b; color:#fff; }
        .ads-btn-green { background:#27ae60; color:#fff; }
        .ads-btn-blue  { background:#2980b9; color:#fff; }
        .ads-title { font-size:1.4rem; font-weight:700; margin-bottom:12px; }
        .ads-table { width:100%; border-collapse:collapse; font-size:14px; }
        .ads-table th { background:#1e293b; color:#fff; padding:10px 12px; text-align:left; }
        .ads-table td { padding:10px 12px; border-bottom:1px solid #eee; vertical-align:middle; }
        .ads-table tr:hover td { background:#f8fafc; }
        .ads-status-badge { display:inline-block; padding:4px 10px; border-radius:9999px; color:#fff; font-size:12px; font-weight:700; text-transform:uppercase; }
        .ads-status-select { padding:6px 10px; border-radius:6px; border:1px solid #cbd5e1; font-size:13px; cursor:pointer; }
        .ads-save-btn { padding:5px 12px; background:#1e293b; color:#fff; border:none; border-radius:5px; cursor:pointer; font-size:12px; margin-left:6px; }
        .ads-save-btn:hover { background:#0f172a; }
        .ads-msg { font-size:12px; margin-left:8px; color:#27ae60; display:none; }
        .ads-empty { padding:30px; text-align:center; color:#94a3b8; font-size:1rem; }
    </style>

    <div class="ads-wrap">
        <div class="ads-title">Delivery Status by Date</div>
        <div class="ads-filter-bar">
            <a href="<?php echo esc_url(add_query_arg('delivery_date', $today, $current_url)); ?>" class="ads-btn ads-btn-dark">Today's Delivery's</a>
            <a href="<?php echo esc_url(add_query_arg('delivery_date', $tomorrow, $current_url)); ?>" class="ads-btn ads-btn-dark">Tomorrow Delivery's</a>
            <form method="get" action="" style="display:flex;gap:8px;align-items:center;">
                <input type="date" name="delivery_date" value="<?php echo esc_attr($selected); ?>">
                <button type="submit" class="ads-btn ads-btn-blue">Go</button>
            </form>
            <a href="<?php echo esc_url(add_query_arg(['delivery_date' => $selected, 'ads_pdf' => '1'], $current_url)); ?>" class="ads-btn ads-btn-green">Download PDF</a>
        </div>

        <div style="font-size:1.1rem;font-weight:700;margin-bottom:12px;"><?php echo esc_html($display_date); ?></div>

        <?php if (empty($rows)): ?>
            <div class="ads-empty">No deliveries scheduled for <?php echo esc_html($display_date); ?></div>
        <?php else: ?>
        <table class="ads-table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer Name</th>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Apartment</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($rows as $row):
                $color = $status_colors[$row['status']] ?? '#999';
            ?>
                <tr>
                    <td><a href="<?php echo esc_url(admin_url('post.php?post=' . $row['order_id'] . '&action=edit')); ?>" target="_blank"><?php echo esc_html($row['order_id']); ?></a></td>
                    <td><?php echo esc_html($row['customer']); ?></td>
                    <td><?php echo esc_html($row['product']); ?></td>
                    <td><?php echo esc_html($row['qty']); ?></td>
                    <td><?php echo esc_html($row['apartment']); ?></td>
                    <td>
                        <select class="ads-status-select"
                            data-item-id="<?php echo esc_attr($row['item_id']); ?>"
                            data-date="<?php echo esc_attr($row['date']); ?>"
                            data-order-id="<?php echo esc_attr($row['order_id']); ?>"
                            style="background:<?php echo esc_attr($color); ?>;color:#fff;">
                            <?php foreach ($all_statuses as $s): ?>
                                <option value="<?php echo esc_attr($s); ?>"
                                    <?php selected($row['status'], $s); ?>
                                    style="background:#fff;color:#000;">
                                    <?php echo esc_html(ucfirst(str_replace('_', ' ', $s))); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <button class="ads-save-btn">Save</button>
                        <span class="ads-msg">✓ Saved</span>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>

    <script>
    jQuery(document).ready(function($) {
        var ajaxurl = '<?php echo esc_js(admin_url('admin-ajax.php')); ?>';

        // Update badge color when dropdown changes
        $('.ads-status-select').on('change', function() {
            var colors = <?php echo json_encode($status_colors); ?>;
            var val = $(this).val();
            $(this).css('background', colors[val] || '#999');
        });

        // Save button click
        $('.ads-table').on('click', '.ads-save-btn', function() {
            var row     = $(this).closest('tr');
            var sel     = row.find('.ads-status-select');
            var msg     = row.find('.ads-msg');
            var btn     = $(this);
            var itemId  = sel.data('item-id');
            var date    = sel.data('date');
            var orderId = sel.data('order-id');
            var status  = sel.val();

            btn.prop('disabled', true).text('...');
            $.post(ajaxurl, {
                action:     'update_item_delivery_status',
                item_id:    itemId,
                date:       date,
                order_id:   orderId,
                new_status: status
            }, function(resp) {
                btn.prop('disabled', false).text('Save');
                if (resp.success) {
                    msg.text('✓ Saved').css('color','#27ae60').fadeIn().delay(2000).fadeOut();
                } else {
                    msg.text('✗ Error').css('color','#e74c3c').fadeIn().delay(2000).fadeOut();
                }
            }).fail(function() {
                btn.prop('disabled', false).text('Save');
                msg.text('✗ Failed').css('color','#e74c3c').fadeIn().delay(2000).fadeOut();
            });
        });
    });
    </script>
    <?php
    return ob_get_clean();
}

// Function to send email notification on delivery status update
function send_delivery_status_update_email($order_id, $item_id, $date, $new_status)
{
    $order = wc_get_order($order_id);
    if (!$order)
        return;

    $customer_email = $order->get_billing_email();
    if (empty($customer_email))
        return;

    $item = new WC_Order_Item_Product($item_id);
    $product_name = $item->get_name();
    $formatted_date = date('F j, Y', strtotime($date));
    $status_label = ucfirst(str_replace('_', ' ', $new_status));

    $notes = $item->get_meta('_delivery_notes_' . $date);

    $subject = "Delivery Status Update for Order #{$order_id}";

    $message = "Hello " . $order->get_billing_first_name() . ",\n\n";
    $message .= "The delivery status for your subscription item has been updated.\n\n";
    $message .= "Product: {$product_name}\n";
    $message .= "Delivery Date: {$formatted_date}\n";
    $message .= "New Status: {$status_label}\n";

    if (!empty($notes)) {
        $message .= "\nNote: {$notes}\n";
    }

    $message .= "\nYou can view your full delivery schedule on your account page.\n\n";
    $message .= "Thank you for shopping with us!\n";

    wp_mail($customer_email, $subject, $message);
}

// Display calendar on View Order page
add_action('woocommerce_order_details_after_order_table', 'display_delivery_calendar_on_order_details', 10, 1);
function display_delivery_calendar_on_order_details($order)
{
    if (!$order)
        return;

    $all_deliveries = [];
    foreach ($order->get_items() as $item_id => $item) {
        $delivery_dates = $item->get_meta('_delivery_times');
        $delivery_time = $item->get_meta('_delivery_time');

        if ($delivery_dates && is_array($delivery_dates)) {
            foreach ($delivery_dates as $date) {
                $status = $item->get_meta('_delivery_status_' . $date) ?: 'pending';
                $notes = $item->get_meta('_delivery_notes_' . $date);
                $all_deliveries[$date][] = [
                    'product_name' => $item->get_name(),
                    'time' => $delivery_time,
                    'status' => $status,
                    'notes' => $notes
                ];
            }
        }
    }

    if (empty($all_deliveries))
        return;

    // Default to the month of the first delivery date
    $first_date = array_keys($all_deliveries)[0];
    $month = isset($_GET['cal_month']) ? intval($_GET['cal_month']) : date('m', strtotime($first_date));
    $year = isset($_GET['cal_year']) ? intval($_GET['cal_year']) : date('Y', strtotime($first_date));

    $first_day_of_month = mktime(0, 0, 0, $month, 1, $year);
    $days_in_month = date('t', $first_day_of_month);
    $day_of_week_starts = date('w', $first_day_of_month);

    $prev_month = $month == 1 ? 12 : $month - 1;
    $prev_year = $month == 1 ? $year - 1 : $year;
    $next_month = $month == 12 ? 1 : $month + 1;
    $next_year = $month == 12 ? $year + 1 : $year;

    $status_colors = [
        'pending' => '#f1c40f',
        'confirmed' => '#3498db',
        'preparing' => '#e67e22',
        'packed' => '#9b59b6',
        'out_for_delivery' => '#1abc9c',
        'delivered' => '#2ecc71',
        'rescheduled' => '#e74c3c',
        'skipped' => '#95a5a6',
        'cancelled' => '#34495e'
    ];

    // Create base URL for navigation that preserves the current view-order endpoint
    $base_url = wc_get_endpoint_url('view-order', $order->get_id(), wc_get_page_permalink('myaccount'));

    ?>
    <section class="woocommerce-order-delivery-calendar" style="margin-top: 30px;">
        <h2 class="woocommerce-order-details__title">Delivery Calendar</h2>
        <div style="display: flex; justify-content: space-between; margin-bottom: 20px;">
            <a href="<?php echo esc_url(add_query_arg(['cal_month' => $prev_month, 'cal_year' => $prev_year], $base_url)); ?>"
                class="button">&laquo; Previous</a>
            <h3 style="margin:0;"><?php echo date('F Y', $first_day_of_month); ?></h3>
            <a href="<?php echo esc_url(add_query_arg(['cal_month' => $next_month, 'cal_year' => $next_year], $base_url)); ?>"
                class="button">Next &raquo;</a>
        </div>
        <table style="width: 100%; border-collapse: collapse; text-align: center;">
            <thead>
                <tr>
                    <th style="border: 1px solid #ddd; padding: 10px; width: 14%;">Sun</th>
                    <th style="border: 1px solid #ddd; padding: 10px; width: 14%;">Mon</th>
                    <th style="border: 1px solid #ddd; padding: 10px; width: 14%;">Tue</th>
                    <th style="border: 1px solid #ddd; padding: 10px; width: 14%;">Wed</th>
                    <th style="border: 1px solid #ddd; padding: 10px; width: 14%;">Thu</th>
                    <th style="border: 1px solid #ddd; padding: 10px; width: 14%;">Fri</th>
                    <th style="border: 1px solid #ddd; padding: 10px; width: 14%;">Sat</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <?php
                    for ($i = 0; $i < $day_of_week_starts; $i++)
                        echo '<td style="border: 1px solid #ddd; padding: 10px; background-color: #f9f9f9;"></td>';

                    $current_day = 1;
                    while ($current_day <= $days_in_month) {
                        if (($current_day + $day_of_week_starts - 1) % 7 == 0 && $current_day != 1)
                            echo '</tr><tr>';

                        $date_str = sprintf('%04d-%02d-%02d', $year, $month, $current_day);
                        $formatted_date_str = date('d-m-Y', strtotime($date_str));
                        $deliveries = isset($all_deliveries[$formatted_date_str]) ? $all_deliveries[$formatted_date_str] : (isset($all_deliveries[$date_str]) ? $all_deliveries[$date_str] : []);

                        echo '<td style="border: 1px solid #ddd; padding: 10px; vertical-align: top; height: 100px;">';
                        echo '<strong>' . $current_day . '</strong><br>';

                        if (!empty($deliveries)) {
                            foreach ($deliveries as $del) {
                                $color = isset($status_colors[$del['status']]) ? $status_colors[$del['status']] : '#999';
                                echo '<div style="margin-top: 5px; padding: 5px; background-color: ' . esc_attr($color) . '; color: white; border-radius: 4px; font-size: 11px; line-height: 1.2;">';
                                echo esc_html(ucfirst(str_replace('_', ' ', $del['status']))) . '<br>';
                                if (!empty($del['notes']))
                                    echo '<small>Note: ' . esc_html($del['notes']) . '</small>';
                                echo '</div>';
                            }
                        }
                        echo '</td>';
                        $current_day++;
                    }

                    $remaining_days = (7 - (($days_in_month + $day_of_week_starts) % 7)) % 7;
                    for ($i = 0; $i < $remaining_days; $i++)
                        echo '<td style="border: 1px solid #ddd; padding: 10px; background-color: #f9f9f9;"></td>';
                    ?>
                </tr>
            </tbody>
        </table>
    </section>
    <?php
}

// ==============================================
// EMAIL VERIFICATION FLOW
// ==============================================

// 1. Intercept Login for unverified users
add_filter('wp_authenticate_user', 'custom_check_email_verification_on_login', 10, 2);
function custom_check_email_verification_on_login($user, $password)
{
    if (is_wp_error($user)) {
        return $user;
    }

    // Do not lock out admins or staff from logging in
    if (in_array('administrator', $user->roles) || in_array('staff', $user->roles)) {
        return $user;
    }

    // Generate OTP for EVERY login
    $verification_code = wp_rand(100000, 999999);
    update_user_meta($user->ID, '_email_verify_code', $verification_code);
    update_user_meta($user->ID, '_email_verify_code_time', time());

    $subject = 'Email Verification Code';
    $message = 'Your verification code is: ' . $verification_code;
    wp_mail($user->user_email, $subject, $message);

    // Redirect to My Account page with verification param to bypass live site caching
    $myaccount_url = wc_get_page_permalink('myaccount');
    if (!$myaccount_url) {
        $myaccount_url = home_url();
    }
    $verify_url = add_query_arg('verify_email_u', base64_encode($user->ID), $myaccount_url);
    wp_safe_redirect($verify_url);
    exit;

    return $user;
}

// 2. Process Verification Form Submission
add_action('init', 'custom_process_email_verification_submission');
function custom_process_email_verification_submission()
{
    if (isset($_POST['verify_email_submit']) && isset($_POST['verify_email_u'])) {
        $user_id = absint(base64_decode(sanitize_text_field($_POST['verify_email_u'])));
        $entered_code = sanitize_text_field($_POST['verification_code']);

        if ($user_id) {
            $saved_code = get_user_meta($user_id, '_email_verify_code', true);
            $saved_time = get_user_meta($user_id, '_email_verify_code_time', true);

            if (empty($saved_code) || $entered_code != $saved_code) {
                // Redirect back with error to My Account page
                $myaccount_url = wc_get_page_permalink('myaccount');
                if (!$myaccount_url)
                    $myaccount_url = home_url();
                $url = add_query_arg(array('verify_email_u' => $_POST['verify_email_u'], 'verify_error' => 1), $myaccount_url);
                wp_redirect($url);
                exit;
            } elseif (time() - $saved_time > 60) {
                // Expired
                $myaccount_url = wc_get_page_permalink('myaccount');
                if (!$myaccount_url)
                    $myaccount_url = home_url();
                $url = add_query_arg(array('verify_email_u' => $_POST['verify_email_u'], 'verify_error' => 'expired'), $myaccount_url);
                wp_redirect($url);
                exit;
            } else {
                update_user_meta($user_id, '_is_email_verified', 'yes');
                delete_user_meta($user_id, '_email_verify_code');
                delete_user_meta($user_id, '_email_verify_code_time');

                wc_set_customer_auth_cookie($user_id);
                // Redirect based on role
                $user = get_userdata($user_id);
                $redirect_to = get_permalink(get_option('woocommerce_myaccount_page_id'));
                if (in_array('administrator', $user->roles)) {
                    $redirect_to = 'https://coconutmakers.com/add-product/';
                } elseif (in_array('staff', $user->roles)) {
                    $redirect_to = 'https://coconutmakers.com/today-delivery/';
                }

                wp_redirect($redirect_to);
                exit;
            }
        }
    }
}

add_action('admin_head', function() {
    echo '<style>
        .wp-list-table th.check-column,
        .wp-list-table td.check-column,
        .wp-list-table .check-column input[type="checkbox"] {
            display: block !important;
            opacity: 1 !important;
            visibility: visible !important;
        }
        .wp-list-table td.check-column {
            display: table-cell !important;
            width: 2.2em !important;
        }
    </style>';
});

// Force WooCommerce to allow administrators and shop managers to edit orders in HPOS
add_filter('map_meta_cap', function($caps, $cap, $user_id, $args) {
    if ($cap === 'edit_shop_order') {
        $user = get_userdata($user_id);
        if ($user && (in_array('administrator', $user->roles) || in_array('shop_manager', $user->roles))) {
            return ['read']; // Bypass strict HPOS checks
        }
    }
    return $caps;
}, 999, 4);

add_action('admin_footer', function() {
    if (isset($_GET['page']) && $_GET['page'] === 'wc-orders') {
        ?>
        <script>
        jQuery(document).ready(function($) {
            console.log("Running WooCommerce HPOS checkbox brute-force fix...");
            $('table.wp-list-table tbody tr').each(function() {
                var row = $(this);
                var order_id = '';
                
                // Try to find the order ID from the "Edit" link in the row
                var editLink = row.find('a[href*="action=edit&id="]').attr('href');
                if (editLink) {
                    var match = editLink.match(/id=(\d+)/);
                    if (match && match[1]) {
                        order_id = match[1];
                    }
                }
                
                // Fallback: Try to get from row ID/Class
                if (!order_id) {
                    var classAttr = row.attr('class') || '';
                    var match2 = classAttr.match(/(?:order|post)-(\d+)/);
                    if (match2) order_id = match2[1];
                }

                if (order_id) {
                    var cb_th = row.find('.check-column');
                    if (cb_th.length) {
                        // The column cell exists, but the checkbox is missing
                        if (cb_th.find('input[type="checkbox"]').length === 0) {
                            cb_th.html('<input type="checkbox" name="id[]" value="' + order_id + '">');
                        }
                    } else {
                        // The column cell doesn't even exist! Prepend it.
                        row.prepend('<th scope="row" class="check-column"><input type="checkbox" name="id[]" value="' + order_id + '"></th>');
                    }
                }
            });
            
            // Fix the "Select All" checkbox in the header
            $('#cb-select-all-1, #cb-select-all-2').on('change', function() {
                var is_checked = $(this).is(':checked');
                $('table.wp-list-table tbody .check-column input[type="checkbox"]').prop('checked', is_checked);
            });
        });
        </script>
        <?php
    }
});
