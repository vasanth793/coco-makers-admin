<?php
/* Template Name: Delivery Status Date Wise */
get_header();

// Check if the user is logged in and has staff or admin role
if (!is_user_logged_in() || !(current_user_can('administrator') || current_user_can('manager') || current_user_can('shop_manager') || current_user_can('staff'))) {
    echo "<p style='padding: 50px; text-align: center;'>You do not have permission to view this page.</p>";
    get_footer();
    exit;
}

if (!function_exists('cm_get_delivery_status_badge_color')) {
    function cm_get_delivery_status_badge_color($status) {
        $s = strtolower(trim(str_replace(' ', '_', $status)));
        switch ($s) {
            case 'confirmed':
                return '#3498db'; // Blue
            case 'delivered':
                return '#27ae60'; // Green
            case 'pending':
                return '#f39c12'; // Orange/Amber
            case 'preparing':
                return '#e67e22'; // Dark Orange
            case 'packed':
                return '#9b59b6'; // Purple
            case 'out_for_delivery':
                return '#1abc9c'; // Teal
            case 'rescheduled':
                return '#e74c3c'; // Red
            case 'cancelled':
            case 'rejected':
                return '#c0392b'; // Dark Red
            case 'skipped':
                return '#95a5a6'; // Grey
            default:
                return '#7f8c8d'; // Slate
        }
    }
}

// Fetch all orders with specified statuses (Newest orders first)
$args = array(
    'limit' => -1,
    'status' => array('wc-processing', 'wc-on-hold', 'wc-completed'),
    'orderby' => 'date',
    'order' => 'DESC'
);

$orders = wc_get_orders($args);
$deliveries_by_date = [];
$deliveries_by_apartment_today = [];
$deliveries_by_apartment_tomorrow = [];
$today_date = date('d-m-Y');
$tomorrow_date = date('d-m-Y', strtotime('+1 day'));

// Organize delivery data by date and apartment
foreach ($orders as $order) {
    $order_id = $order->get_id();
    $customer_name = trim($order->get_billing_first_name() . ' ' . $order->get_billing_last_name());
    
    // Phone
    $phone = $order->get_billing_phone();
    if (empty($phone)) {
        $phone = $order->get_meta('_shipping_phone', true) ?: '-';
    }

    // Apartment
    $apartment = get_post_meta($order_id, '_billing_apartment', true);
    if (empty($apartment)) {
        $apartment = $order->get_meta('_shipping_address_type', true) ?: ($order->get_shipping_address_2() ?: '-');
    }

    // Full Address
    $address_1 = $order->get_shipping_address_1() ?: $order->get_billing_address_1();
    $address_2 = $order->get_shipping_address_2() ?: $order->get_billing_address_2();
    $area      = $order->get_meta('_shipping_area', true) ?: $order->get_meta('_billing_area', true);
    $landmark  = $order->get_meta('_shipping_landmark', true) ?: $order->get_meta('_billing_landmark', true);
    $city      = $order->get_shipping_city() ?: $order->get_billing_city();
    $postcode  = $order->get_shipping_postcode() ?: $order->get_billing_postcode();

    $address_parts = array_filter([$address_1, $address_2, $area, $landmark, $city, $postcode]);
    $full_address  = !empty($address_parts) ? implode(', ', $address_parts) : ($apartment !== '-' ? $apartment : '-');

    foreach ($order->get_items() as $item_id => $item) {
        $delivery_dates = $item->get_meta('_delivery_times', true);
        
        if (empty($delivery_dates) || !is_array($delivery_dates)) {
            $legacy_dates = $item->get_meta('_delivery_date', true);
            if (is_array($legacy_dates)) {
                $delivery_dates = $legacy_dates;
            } elseif (!empty($legacy_dates)) {
                $delivery_dates = array_map('trim', explode(',', $legacy_dates));
            } else {
                $selected_dates = $item->get_meta('Selected Dates', true);
                if ($selected_dates) {
                    $delivery_dates = array_map('trim', explode(',', $selected_dates));
                } else {
                    $delivery_dates = [];
                }
            }
        }
        
        $quantity = $item->get_quantity();
        $delivery_time = $item->get_meta('_delivery_time', true);
        if (empty($delivery_time)) {
            $delivery_time = $item->get_meta('Delivery Time', true) ?: '-';
        }

        if (!empty($delivery_dates)) {
            foreach ($delivery_dates as $date) {
                $status = $item->get_meta('_delivery_status_' . $date);
                $status_label = !empty($status) ? ucfirst(str_replace('_', ' ', $status)) : 'Pending';
                $formatted_date = date('d-m-Y', strtotime($date));

                $delivery_row = array(
                    'order_id'      => $order_id,
                    'product'       => $item->get_name(),
                    'customer_name' => $customer_name,
                    'phone'         => $phone,
                    'quantity'      => $quantity,
                    'apartment'     => $apartment,
                    'address'       => $full_address,
                    'delivery_time' => $delivery_time,
                    'status'        => $status_label
                );

                // Add to the date-wise array
                $deliveries_by_date[$formatted_date][] = $delivery_row;

                // If the date is today, organize by apartment
                if ($formatted_date === $today_date) {
                    $deliveries_by_apartment_today[$apartment][] = $delivery_row;
                }

                // If the date is tomorrow, organize by apartment
                if ($formatted_date === $tomorrow_date) {
                    $deliveries_by_apartment_tomorrow[$apartment][] = $delivery_row;
                }
            }
        }
    }
}

// Sort date-wise array descending (Newest date first at top, older dates below)
uksort($deliveries_by_date, function($a, $b) {
    return strtotime($b) - strtotime($a);
});
?>

<div class="delivery-status" style="display: flex; margin: 180px auto 40px auto; max-width: 1400px; flex-wrap: wrap;">
    
    <!-- Sidebar -->
    <div class="sidebar">
        <h3>Menu</h3>
        <ul>
            <li><a href="<?php echo site_url('/add-product'); ?>" class="<?php echo (is_page('add-product') ? 'active-menu' : ''); ?>">Add Products</a></li>
            <li><a href="<?php echo site_url('/status'); ?>" class="<?php echo (is_page('status') ? 'active-menu' : ''); ?>">Delivery</a></li>
            <li><a href="<?php echo site_url('/order-list'); ?>" class="<?php echo (is_page('order-list') ? 'active-menu' : ''); ?>">Order Details</a></li>
            <li><a href="<?php echo site_url('/edit-products'); ?>" class="<?php echo (is_page('edit-products') ? 'active-menu' : ''); ?>">Edit Products</a></li>
            <li><a href="<?php echo site_url('/add-offers'); ?>" class="<?php echo (is_page('add-offers') ? 'active-menu' : ''); ?>">Add Offers</a></li>
            <li><a href="<?php echo site_url('/apartments'); ?>" class="<?php echo (is_page('apartments') ? 'active-menu' : ''); ?>">Add Apartments</a></li>
            <li><a href="<?php echo site_url('/category'); ?>" class="<?php echo (is_page('category') ? 'active-menu' : ''); ?>">Add Category</a></li>
        </ul>
    </div>

    <div class="main-content" style="flex: 1; padding: 20px;">
        <h2 style="margin-bottom: 20px; font-weight: 700; color: #2E1C12;">Delivery Status by Date</h2>

        <!-- Action Buttons -->
        <div class="filter-actions-bar" style="margin-bottom: 20px; display: flex; flex-wrap: wrap; gap: 10px; align-items: center;">
            <button id="download-pdf" class="filter-button" style="padding: 10px 20px; background-color: #2E1C12; color: #fff; font-weight: 600;">
                📥 Download PDF
            </button>
            <button id="all-button" onclick="filterResults('all')" class="filter-button" style="padding: 10px 20px;">
                All
            </button>
            <button id="today-button" onclick="filterResults('today')" class="filter-button" style="padding: 10px 20px;">
                Today Delivery's
            </button>
            <button id="tomorrow-button" onclick="filterResults('tomorrow')" class="filter-button" style="padding: 10px 20px;">
                Tomorrow Delivery's
            </button>
            <input type="date" id="date-picker" onchange="filterResults('datePicker')" style="padding: 9px 12px; border-radius: 4px; border: 1px solid #ddd; font-size: 14px;">
        </div>
    
        <?php if (empty($deliveries_by_date)) : ?>
            <p style="padding: 30px; text-align: center; color: #888;">No delivery statuses available.</p>
        <?php else : ?>
            <!-- Show All Dates -->
            <?php foreach ($deliveries_by_date as $date => $deliveries) : ?>
                <div class="date-section" data-date="<?php echo esc_attr($date); ?>">
                    <h3 style="margin-top: 25px; margin-bottom: 12px; padding-bottom: 6px; border-bottom: 2px solid #2E1C12; color: #2E1C12;">
                        📅 <?php echo esc_html($date); ?>
                    </h3>
                    <div class="table-wrapper">
                        <table class="responsive-table" style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                            <thead>
                                <tr>
                                    <th style="width: 45px; text-align: center;">#</th>
                                    <th style="width: 80px; text-align: center;">Order ID</th>
                                    <th>Customer Name</th>
                                    <th style="width: 110px;">Phone</th>
                                    <th>Product</th>
                                    <th style="width: 55px; text-align: center;">Qty</th>
                                    <th>Apartment</th>
                                    <th>Address</th>
                                    <th style="width: 105px;">Time</th>
                                    <th style="width: 110px; text-align: center;">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = 1; foreach ($deliveries as $delivery) : 
                                    $badge_color = cm_get_delivery_status_badge_color($delivery['status']);
                                ?>
                                    <tr>
                                        <td style="text-align: center; font-weight: bold;"><?php echo $i++; ?></td>
                                        <td style="text-align: center; font-weight: bold;"><?php echo esc_html($delivery['order_id']); ?></td>
                                        <td><strong><?php echo esc_html($delivery['customer_name']); ?></strong></td>
                                        <td><?php echo esc_html($delivery['phone']); ?></td>
                                        <td><?php echo esc_html($delivery['product']); ?></td>
                                        <td style="text-align: center; font-weight: bold;"><?php echo esc_html($delivery['quantity']); ?></td>
                                        <td><?php echo esc_html($delivery['apartment']); ?></td>
                                        <td style="font-size: 12px;"><?php echo esc_html($delivery['address']); ?></td>
                                        <td style="font-size: 12px;"><?php echo esc_html($delivery['delivery_time']); ?></td>
                                        <td style="text-align: center;">
                                            <span class="status-badge" style="background-color: <?php echo esc_attr($badge_color); ?>;">
                                                <?php echo esc_html($delivery['status']); ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endforeach; ?>

            <!-- Today Delivery's Table by Apartment -->
            <div id="today-apartment-delivery" style="display: none;">
                <?php if (empty($deliveries_by_apartment_today)) : ?>
                    <p style="padding: 20px; color: #888;">No deliveries scheduled for today.</p>
                <?php else : ?>
                    <?php foreach ($deliveries_by_apartment_today as $apartment => $deliveries) : ?>
                        <div class="apartment-section" data-apartment="<?php echo esc_attr($apartment); ?>">
                            <h3 style="margin-top: 25px; margin-bottom: 12px; padding-bottom: 6px; border-bottom: 2px solid #2E1C12; color: #2E1C12;">
                                🏢 <?php echo esc_html($apartment); ?> - Today's Deliveries (<?php echo esc_html($today_date); ?>)
                            </h3>
                            <div class="table-wrapper">
                                <table class="responsive-table" style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                                    <thead>
                                        <tr>
                                            <th style="width: 45px; text-align: center;">#</th>
                                            <th style="width: 80px; text-align: center;">Order ID</th>
                                            <th>Customer Name</th>
                                            <th style="width: 110px;">Phone</th>
                                            <th>Product</th>
                                            <th style="width: 55px; text-align: center;">Qty</th>
                                            <th>Apartment</th>
                                            <th>Address</th>
                                            <th style="width: 105px;">Time</th>
                                            <th style="width: 110px; text-align: center;">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $i = 1; foreach ($deliveries as $delivery) : 
                                            $badge_color = cm_get_delivery_status_badge_color($delivery['status']);
                                        ?>
                                            <tr>
                                                <td style="text-align: center; font-weight: bold;"><?php echo $i++; ?></td>
                                                <td style="text-align: center; font-weight: bold;"><?php echo esc_html($delivery['order_id']); ?></td>
                                                <td><strong><?php echo esc_html($delivery['customer_name']); ?></strong></td>
                                                <td><?php echo esc_html($delivery['phone']); ?></td>
                                                <td><?php echo esc_html($delivery['product']); ?></td>
                                                <td style="text-align: center; font-weight: bold;"><?php echo esc_html($delivery['quantity']); ?></td>
                                                <td><?php echo esc_html($delivery['apartment']); ?></td>
                                                <td style="font-size: 12px;"><?php echo esc_html($delivery['address']); ?></td>
                                                <td style="font-size: 12px;"><?php echo esc_html($delivery['delivery_time']); ?></td>
                                                <td style="text-align: center;">
                                                    <span class="status-badge" style="background-color: <?php echo esc_attr($badge_color); ?>;">
                                                        <?php echo esc_html($delivery['status']); ?>
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Tomorrow Delivery's Table by Apartment -->
            <div id="tomorrow-apartment-delivery" style="display: none;">
                <?php if (empty($deliveries_by_apartment_tomorrow)) : ?>
                    <p style="padding: 20px; color: #888;">No deliveries scheduled for tomorrow.</p>
                <?php else : ?>
                    <?php foreach ($deliveries_by_apartment_tomorrow as $apartment => $deliveries) : ?>
                        <div class="apartment-section" data-apartment="<?php echo esc_attr($apartment); ?>">
                            <h3 style="margin-top: 25px; margin-bottom: 12px; padding-bottom: 6px; border-bottom: 2px solid #2E1C12; color: #2E1C12;">
                                🏢 <?php echo esc_html($apartment); ?> - Tomorrow's Deliveries (<?php echo esc_html($tomorrow_date); ?>)
                            </h3>
                            <div class="table-wrapper">
                                <table class="responsive-table" style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                                    <thead>
                                        <tr>
                                            <th style="width: 45px; text-align: center;">#</th>
                                            <th style="width: 80px; text-align: center;">Order ID</th>
                                            <th>Customer Name</th>
                                            <th style="width: 110px;">Phone</th>
                                            <th>Product</th>
                                            <th style="width: 55px; text-align: center;">Qty</th>
                                            <th>Apartment</th>
                                            <th>Address</th>
                                            <th style="width: 105px;">Time</th>
                                            <th style="width: 110px; text-align: center;">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $i = 1; foreach ($deliveries as $delivery) : 
                                            $badge_color = cm_get_delivery_status_badge_color($delivery['status']);
                                        ?>
                                            <tr>
                                                <td style="text-align: center; font-weight: bold;"><?php echo $i++; ?></td>
                                                <td style="text-align: center; font-weight: bold;"><?php echo esc_html($delivery['order_id']); ?></td>
                                                <td><strong><?php echo esc_html($delivery['customer_name']); ?></strong></td>
                                                <td><?php echo esc_html($delivery['phone']); ?></td>
                                                <td><?php echo esc_html($delivery['product']); ?></td>
                                                <td style="text-align: center; font-weight: bold;"><?php echo esc_html($delivery['quantity']); ?></td>
                                                <td><?php echo esc_html($delivery['apartment']); ?></td>
                                                <td style="font-size: 12px;"><?php echo esc_html($delivery['address']); ?></td>
                                                <td style="font-size: 12px;"><?php echo esc_html($delivery['delivery_time']); ?></td>
                                                <td style="text-align: center;">
                                                    <span class="status-badge" style="background-color: <?php echo esc_attr($badge_color); ?>;">
                                                        <?php echo esc_html($delivery['status']); ?>
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

        <?php endif; ?>
    </div>
</div>

<style>
/* Button styling */
.filter-button {
    background-color: #2E1C12;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
    transition: background-color 0.3s;
}

.filter-button:hover {
    background-color: #4a2f20;
}

.active-filter, .filter-button.active {
    background-color: #FE8806 !important;
}

.sidebar {
    width: 250px;
    min-width: 220px;
    padding: 20px;
    background-color: #2e1c12;
    border-right: 1px solid #ddd;
    box-shadow: 0 2px 10px rgba(46, 28, 18, 0.15);
    border-radius: 8px;
    margin-right: 20px;
}

.sidebar h3 {
    margin-top: 0;
    color: #fff;
    font-size: 18px;
    padding-bottom: 10px;
    border-bottom: 1px solid rgba(255,255,255,0.2);
}

.sidebar ul {
    list-style-type: none;
    padding-left: 0;
    margin: 0;
}

.sidebar ul li {
    margin-bottom: 8px;
}

.sidebar ul li a {
    text-decoration: none;
    color: #fff;
    padding: 8px 12px;
    border-radius: 4px;
    display: block;
    transition: background-color 0.2s;
}

.sidebar ul li a:hover {
    background-color: rgba(255,255,255,0.1);
}

.active-menu {
    color: #FE8806 !important;
    font-weight: bold;
    background-color: #f2f2f2;
}

.main-content {
    flex: 1;
    background: #ffffff;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
}

.table-wrapper {
    width: 100%;
    margin-bottom: 20px;
    overflow-x: auto;
}

.responsive-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
    background: #fff;
}

.responsive-table th {
    background-color: #2E1C12;
    color: #fff;
    padding: 10px 8px;
    text-align: left;
    border: 1px solid #ddd;
    font-weight: 600;
}

.responsive-table td {
    padding: 10px 8px;
    text-align: left;
    border: 1px solid #ddd;
    vertical-align: middle;
}

.responsive-table tr:nth-child(even) td {
    background-color: #fafafa;
}

.responsive-table tr:hover td {
    background-color: #f1f5f9;
}

.status-badge {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 12px;
    color: #ffffff !important;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    white-space: nowrap;
}

@media print {
    body {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
    .sidebar, .filter-actions-bar {
        display: none !important;
    }
}
</style>

<!-- Include HTML2PDF -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>

<script>
function filterResults(type) {
    const allButton = document.getElementById('all-button');
    const todayButton = document.getElementById('today-button');
    const tomorrowButton = document.getElementById('tomorrow-button');
    const datePicker = document.getElementById('date-picker');
    const dateSections = document.querySelectorAll('.date-section');
    const todayApartmentSection = document.getElementById('today-apartment-delivery');
    const tomorrowApartmentSection = document.getElementById('tomorrow-apartment-delivery');
    
    if (allButton) allButton.classList.remove('active');
    if (todayButton) todayButton.classList.remove('active');
    if (tomorrowButton) tomorrowButton.classList.remove('active');

    // Hide all date sections
    dateSections.forEach(function(section) {
        section.style.display = 'none';
    });
    
    if (todayApartmentSection) todayApartmentSection.style.display = 'none';
    if (tomorrowApartmentSection) tomorrowApartmentSection.style.display = 'none';
    
    if (type === 'all') {
        dateSections.forEach(function(section) {
            section.style.display = 'block';
        });
        if (allButton) allButton.classList.add('active');
    } else if (type === 'today') {
        if (todayApartmentSection) todayApartmentSection.style.display = 'block';
        if (todayButton) todayButton.classList.add('active');
    } else if (type === 'tomorrow') {
        if (tomorrowApartmentSection) tomorrowApartmentSection.style.display = 'block';
        if (tomorrowButton) tomorrowButton.classList.add('active');
    } else if (type === 'datePicker') {
        const selectedDate = datePicker.value;
        if (selectedDate) {
            const parts = selectedDate.split('-');
            if (parts.length === 3) {
                const formattedDate = parts[2] + '-' + parts[1] + '-' + parts[0]; // DD-MM-YYYY
                dateSections.forEach(function(section) {
                    if (section.getAttribute('data-date') === formattedDate) {
                        section.style.display = 'block';
                    } else {
                        section.style.display = 'none';
                    }
                });
            }
        } else {
            dateSections.forEach(function(section) {
                section.style.display = 'block';
            });
            if (allButton) allButton.classList.add('active');
        }
    }
}

// Clean and Professional PDF Generation with All 10 Columns
document.getElementById('download-pdf').addEventListener('click', function() {
    const btn = this;
    const originalText = btn.innerText;
    btn.innerText = 'Generating PDF...';
    btn.disabled = true;

    const todayButton = document.getElementById('today-button');
    const tomorrowButton = document.getElementById('tomorrow-button');
    const datePicker = document.getElementById('date-picker');

    let reportTitle = 'All Scheduled Deliveries';
    const container = document.createElement('div');
    container.style.padding = '10px';
    container.style.fontFamily = 'Arial, Helvetica, sans-serif';
    container.style.color = '#1e293b';
    container.style.backgroundColor = '#ffffff';

    if (todayButton && todayButton.classList.contains('active')) {
        reportTitle = "Today's Deliveries (<?php echo esc_js($today_date); ?>)";
        const sec = document.getElementById('today-apartment-delivery');
        if (sec) {
            const clone = sec.cloneNode(true);
            clone.style.display = 'block';
            container.appendChild(clone);
        }
    } else if (tomorrowButton && tomorrowButton.classList.contains('active')) {
        reportTitle = "Tomorrow's Deliveries (<?php echo esc_js($tomorrow_date); ?>)";
        const sec = document.getElementById('tomorrow-apartment-delivery');
        if (sec) {
            const clone = sec.cloneNode(true);
            clone.style.display = 'block';
            container.appendChild(clone);
        }
    } else {
        const visibleSections = [];
        document.querySelectorAll('.date-section').forEach(function(sec) {
            if (sec.style.display !== 'none') {
                visibleSections.push(sec);
            }
        });

        if (visibleSections.length === 1) {
            reportTitle = 'Deliveries for ' + visibleSections[0].getAttribute('data-date');
            const clone = visibleSections[0].cloneNode(true);
            clone.style.display = 'block';
            container.appendChild(clone);
        } else if (visibleSections.length > 1) {
            reportTitle = 'Delivery Status Report';
            visibleSections.forEach(function(sec) {
                const clone = sec.cloneNode(true);
                clone.style.display = 'block';
                container.appendChild(clone);
            });
        } else {
            // Default all
            document.querySelectorAll('.date-section').forEach(function(sec) {
                const clone = sec.cloneNode(true);
                clone.style.display = 'block';
                container.appendChild(clone);
            });
        }
    }

    // Add professional Header
    const header = document.createElement('div');
    header.style.textAlign = 'center';
    header.style.marginBottom = '15px';
    header.style.borderBottom = '2px solid #2E1C12';
    header.style.paddingBottom = '10px';
    header.innerHTML = `
        <h1 style="color: #2E1C12; margin: 0; font-size: 22px; font-weight: 800; letter-spacing: 1px;">COCONUT MAKERS</h1>
        <h2 style="color: #475569; margin: 4px 0; font-size: 15px; font-weight: 600;">Delivery Status by Date Report</h2>
        <p style="color: #64748b; margin: 0; font-size: 12px;"><strong>Filter:</strong> ${reportTitle} &nbsp;|&nbsp; <strong>Generated:</strong> ${new Date().toLocaleString()}</p>
    `;
    container.insertBefore(header, container.firstChild);

    // Apply strict PDF styles to the cloned table elements
    container.querySelectorAll('table').forEach(function(tbl) {
        tbl.style.width = '100%';
        tbl.style.fontSize = '9px';
        tbl.style.borderCollapse = 'collapse';
        tbl.style.tableLayout = 'auto';
        tbl.style.marginBottom = '15px';
    });
    container.querySelectorAll('th').forEach(function(th) {
        th.style.backgroundColor = '#2E1C12';
        th.style.color = '#ffffff';
        th.style.padding = '6px 4px';
        th.style.fontSize = '9px';
        th.style.border = '1px solid #cbd5e1';
        th.style.fontWeight = 'bold';
    });
    container.querySelectorAll('td').forEach(function(td) {
        td.style.padding = '5px 4px';
        td.style.fontSize = '9px';
        td.style.border = '1px solid #cbd5e1';
        td.style.verticalAlign = 'middle';
    });
    container.querySelectorAll('.status-badge').forEach(function(badge) {
        badge.style.fontSize = '8px';
        badge.style.padding = '3px 6px';
        badge.style.borderRadius = '8px';
    });

    const opt = {
        margin:       [6, 6, 6, 6],
        filename:     'delivery_report_' + (new Date().toISOString().slice(0, 10)) + '.pdf',
        image:        { type: 'jpeg', quality: 0.98 },
        html2canvas:  { scale: 2.5, useCORS: true, logging: false },
        jsPDF:        { unit: 'mm', format: 'a4', orientation: 'landscape' }
    };

    html2pdf().from(container).set(opt).save().then(function() {
        btn.innerText = originalText;
        btn.disabled = false;
    }).catch(function(err) {
        console.error('PDF generation error:', err);
        btn.innerText = originalText;
        btn.disabled = false;
        alert('Could not download PDF automatically. You can also print the page (Ctrl+P) and select Save as PDF.');
    });
});
</script>

<?php get_footer(); ?>

