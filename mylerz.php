<?php
/*
* Plugin Name: Mylerz
* Plugin URI:
* Description: Convenient and Friendly parcel delivery service
* Author: Nerds Arena
* Version: 4.4.3
* Author URI: https://nerdsarena.com/
* License:
* Text Domain: mylerz
* Domain Path: /languages/
*/

// Custom function to check if HPOS is enabled
use Automattic\WooCommerce\Utilities\OrderUtil;
function is_hpos_enabled() {
    return OrderUtil::custom_orders_table_usage_is_enabled();
}

// Example usage within the plugin


function mylerz_normalize_wc_meta_data($meta_data) {
    // Check if the input is an array (in case of multiple meta items)
    if (is_array($meta_data)) {
        $normalized_data = array_map(function($meta_item) {
            // If the item is a WC_Meta_Data object, extract its value using get_data()
            if (is_a($meta_item, 'WC_Meta_Data')) {
                return $meta_item->get_data()['value'];  // Extract value from WC_Meta_Data
            }
            return $meta_item; // Return the item itself if not WC_Meta_Data
        }, $meta_data);

        return array_values($normalized_data); // Ensure zero-indexed array
    }

    // If the meta_data is a single WC_Meta_Data object, extract the value
    if (is_a($meta_data, 'WC_Meta_Data')) {
        return [$meta_data->get_data()['value']]; // Return the value as a zero-indexed array
    }

    // Ensure return value is always an array
    return is_array($meta_data) ? array_values($meta_data) : [$meta_data];
}

function mylerz_normalized_meta_data($order_id, $meta_key, $single = false) {
    if (is_hpos_enabled()) {
        $order = wc_get_order($order_id);
        $meta_data = $order->get_meta($meta_key, $single);

        // Normalize the meta data using the helper function
        return mylerz_normalize_wc_meta_data($meta_data);
    } else {
        // Handle traditional WordPress post meta
        $meta_value = get_post_meta($order_id, $meta_key, $single);
        return is_array($meta_value) ? array_values($meta_value) : [$meta_value]; // Ensure return as an array
    }
}


function mylerz_get_meta_data($order_id, $meta_key, $single = false) {

    if (is_hpos_enabled()) {
        $order = wc_get_order($order_id);
        return $order->get_meta($meta_key , $single);  // Adding the $single parameter for WooCommerce HPOS
    } else {

        return get_post_meta($order_id, $meta_key, $single);  // Using $single with the traditional get_post_meta
    }
}



function mylerz_update_meta_data($order_id, $meta_key, $meta_value) {
    if (is_hpos_enabled()) {
        $order = wc_get_order($order_id);
        $order->update_meta_data($meta_key, $meta_value);
        $order->save();  // Don't forget to save the order after updating meta
    } else {
        update_post_meta($order_id, $meta_key, $meta_value);
    }
}


function mylerz_add_meta_data($order_id, $meta_key, $meta_value, $unique = false) {
    if (is_hpos_enabled()) {
        $order = wc_get_order($order_id);
        $order->add_meta_data($meta_key, $meta_value, $unique);
        $order->save();  // Don't forget to save the order after adding meta
    } else {
        add_post_meta($order_id, $meta_key, $meta_value, $unique);
    }
}



function mylerz_delete_meta_data($order_id, $meta_key, $meta_value = '', $delete_all = false) {
    if (is_hpos_enabled()) {
        $order = wc_get_order($order_id);
        $order->delete_meta_data($meta_key);  // WooCommerce doesn't require $meta_value or $delete_all
        $order->save();  // Don't forget to save the order after deleting meta
    } else {
        delete_post_meta($order_id, $meta_key, $meta_value, $delete_all);
    }
}


// $integrationApi = 'https://mylerzintegrationtest.mylerz.com';       //testing server
//$integrationApi = 'https://mylerzintegrationtest.mylerz.com/MylerzIntegrationStaging';       //testing server
$apiCountry =
    [
        'EG' => 'https://integration.mylerz.net',
        'TN' => 'https://integration.tunisia.mylerz.net',
        'MA' => 'https://integration.morocco.mylerz.net',
        'DZ' => 'https://integration.algeria.mylerz.net',
    ];

$mylerzOptions = get_option('woocommerce_mylerz_settings');

$integrationApi = esc_url($apiCountry[$mylerzOptions['merchant_country'] ?? 'EG']);       //live server

$timeout = 45;
set_time_limit($timeout + 10);

$cities = [
    "Select Neighborhood",
    "10th of Ramadan",
    "15th of May",
    "6th of Oct",
    "Abbaseya",
    "Abdeen",
    "Abou Rawash",
    "Abu an Numros",
    "Agouza",
    "Ain Shams",
    "Al Abageyah",
    "Al Amiriyyah",
    "Al Ayat",
    "Al Badrashin",
    "Al Baragel",
    "Al Khusus",
    "Al Manawat",
    "Al Moatamadeyah",
    "Al Munib",
    "Al Salam",
    "Al Sharabiya",
    "Alexandria",
    "Ard El Lewa",
    "Aswan",
    "Asyut",
    "Awal Shubra Al Kheimah",
    "Bab El-Shaeria",
    "Badr City",
    "Basateen",
    "Beheira",
    "Beni Suef",
    "Boulak",
    "Boulak Eldakrour",
    "CFC",
    "Dakahlia",
    "Damietta",
    "Dokki",
    "El Azbakia",
    "El Gamalia",
    "El Giza",
    "El Hadba EL wosta",
    "El Hawamdeyya",
    "El Marg",
    "El Mosky",
    "El Obour City",
    "El Saf",
    "El Shorouk",
    "El Talbia",
    "El Waily",
    "El Zaher",
    "El Zawya El Hamra",
    "ELdarb Elahmar",
    "Elsayeda Aisha",
    "Elsayeda Zeinab",
    "Eltebeen",
    "Faiyum",
    "Future City",
    "Ghamra",
    "Gharbia",
    "Hadayek Al Ahram",
    "Hadayek El Qobah",
    "Haram",
    "Heliopolis",
    "Helwan",
    "Imbaba",
    "Ismailia",
    "Izbat an Nakhl",
    "Kafr El Sheikh",
    "Kafr Hakim",
    "Kafr Nassar",
    "Kafr Tuhurmis",
    "Luxor",
    "Maadi",
    "Madinaty",
    "Manial",
    "Masr El Qadeema",
    "Minya",
    "Mohandseen",
    "Monufia",
    "Nahia",
    "Nasr City",
    "Nazlet Al Batran",
    "Nazlet El Semman",
    "New Cairo",
    "New Heliopolis City",
    "North Coast",
    "Nozha",
    "Omraneya",
    "Ossim",
    "Port Said",
    "Qalyubia",
    "Qasr elneil",
    "Qena",
    "Rod El Farag",
    "Saft El Laban",
    "Saqiyet Mekki",
    "Sharqia",
    "Sheikh Zayed",
    "Shoubra",
    "Shubra El Kheima 2",
    "Shubra Ment",
    "Sohag",
    "Suez",
    "Tura",
    "Warraq",
    "Zamalek",
    "Zeitoun",
];

$zones = [
    "",
    "RMDN",
    "15th of May",
    "6th of Oct",
    "ABAS",
    "Abdeen",
    "RWSH",
    "NMRS",
    "Agouza",
    "AS",
    "ABAG",
    "AMRY",
    "AYAT",
    "BDRA",
    "BRAG",
    "KHSU",
    "MNWT",
    "MOTM",
    "MUNB",
    "Al-S",
    "SHRB",
    "Alexandria",
    "LWAA",
    "ASWN",
    "ASYT",
    "KHM1",
    "Bab El-Shaeria",
    "Badr City",
    "Basateen",
    "BEHR",
    "BENS",
    "Boulak",
    "Boulak Eldakrour",
    "CFC",
    "DAKH",
    "DAMT",
    "Dokki",
    "El-Azbakia",
    "El-Gamalia",
    "GIZA",
    "HDBA",
    "HWMD",
    "El-M",
    "El-Mosky",
    "OBOR",
    "SAF",
    "El Shorouk",
    "TLBA",
    "El-Waily",
    "ZAHR",
    "ZWYA",
    "ELdarb Elahmar",
    "ESHA",
    "Elsayeda Zeinab",
    "Eltebeen",
    "FAYM",
    "FUTR",
    "GHMR",
    "GHRB",
    "Hadayek Al Ahram",
    "Hadayek El Qobah",
    "Haram",
    "HEl",
    "Helwan",
    "Imbaba",
    "ISML",
    "NKHL",
    "SHKH",
    "KFRH",
    "NASR",
    "TRMS",
    "LUXR",
    "Maadi",
    "Madinaty",
    "Manial",
    "Masr El Qadeema",
    "MNYA",
    "Mohandseen",
    "MONF",
    "Nahia",
    "Nasr City",
    "BTRN",
    "SMAN",
    "New Cairo",
    "NHEL",
    "NorthCoast",
    "Nozha",
    "Omraneya",
    "OSIM",
    "PORS",
    "QLYB",
    "Qasr elneil",
    "QENA",
    "FRAG",
    "Saft El Laban",
    "MEKI",
    "SHRK",
    "Sheikh Zayed",
    "Shoubra",
    "KHM2",
    "MANT",
    "SOHG",
    "SUEZ",
    "TURA",
    "Warraq",
    "Zamalek",
    "Zeitoun",
];

load_plugin_textdomain('mylerz', false, dirname(plugin_basename(__FILE__)) . '/languages/');


#Add Mylerz Status Column
add_filter( 'manage_edit-shop_order_columns', 'mylerz_add_mylerz_status');

function mylerz_add_mylerz_status( $columns ) {
    $columns['mylerz_status'] = esc_html__('Mylerz Status', 'mylerz');
    return $columns;
}

add_action( 'manage_shop_order_posts_custom_column', 'mylerz_add_mylerz_status_content');

function mylerz_add_mylerz_status_content( $column ) {

    global $post;

    if ( 'mylerz_status' === $column ) {
        if (metadata_exists("post", $post->ID, "mylerzStatus")) {
            echo '<mark class="order-status status-processing tips"><span>'.esc_html(mylerz_get_meta_data($post->ID, "mylerzStatus", true)).'</span></mark>';
        }else {
            echo '--';
        }
    }
}
#end NerdsArena

 #region adding mylerz return satatus

add_action('init', 'register_order_mylerz_return_status');

function register_order_mylerz_return_status()
{
    register_post_status('wc-mylerz-return', array(
        'label'                     => esc_html_x('Return By Mylerz', 'Order status', 'woocommerce'),
        'public'                    => true,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop('Return By Mylerz <span class="count">(%s)</span>', 'Return By Mylerz <span class="count">(%s)</span>', 'woocommerce')
    ));
}

add_filter('wc_order_statuses', 'mylerz_return_order_status');

// Register in wc_order_statuses.
function mylerz_return_order_status($order_statuses)
{
    $order_statuses['wc-mylerz-return'] = esc_html_x('Return By Mylerz', 'Order status', 'woocommerce');

    return $order_statuses;
}

#endregion

#region adding fulfilled satatus

add_action('init', 'mylerz_register_order_fulfilled_status');

function mylerz_register_order_fulfilled_status()
{
    register_post_status('wc-fulfilled', array(
        'label'                     => esc_html_x('Fulfilled', 'Order status', 'woocommerce'),
        'public'                    => true,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop('Fulfilled <span class="count">(%s)</span>', 'Fulfilled<span class="count">(%s)</span>', 'woocommerce')
    ));
}

add_filter('wc_order_statuses', 'mylerz_fulfilled_order_status');

// Register in wc_order_statuses.
function mylerz_fulfilled_order_status($order_statuses)
{
    $order_statuses['wc-fulfilled'] = esc_html_x('Fulfilled', 'Order status', 'woocommerce');

    return $order_statuses;
}

#endregion

#region adding PickupCreated satatus

add_action('init', 'mylerz_register_order_created_status');

function mylerz_register_order_created_status()
{
    register_post_status('wc-created', array(
        'label'                     => esc_html_x('Created', 'Order status', 'woocommerce'),
        'public'                    => true,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop('Created <span class="count">(%s)</span>', 'Created<span class="count">(%s)</span>', 'woocommerce')
    ));
}

add_filter('wc_order_statuses', 'mylerz_created_order_status');

// Register in wc_order_statuses.
function mylerz_created_order_status($order_statuses)
{
    $order_statuses['wc-created'] = esc_html_x('Created', 'Order status', 'woocommerce');

    return $order_statuses;
}

#endregion


add_action( 'wp_loaded', function() {
    if ( is_woocommerce_checkout_block_active_globally() ) {
        // echo 'WooCommerce Checkout Block is active.';
        // die;
    } else {
        #region adding select neighborhood to backend billing address
        add_filter('woocommerce_admin_billing_fields', 'mylerz_zoneDropdownBackend');
        add_filter('woocommerce_admin_shipping_fields', 'mylerz_zoneDropdownBackend');
    }
});



function mylerz_zoneDropdownBackend($fields)
{
    global $cities;

    global $zones;

    $fields['neighborhood']   = array(
        'label'        => esc_html__('Neighborhood', 'mylerz'),
        'required'     => true,
        'show'         => true,
        'class'        => 'custom-class-backend',
        'placeholder'  => esc_html__('Select Neighborhood', 'mylerz'),
        'type' => 'select',
        'options' => array_combine($zones, $cities),
    );

    $fields['compound']   = array(
        'label'        => esc_html__('Compound', 'mylerz'),
        'required'     => true,
        'show'         => true,
        'class'        => 'custom-class-backend',
        'placeholder'  => esc_html__('Select Compound', 'mylerz'),
        'type' => 'select',
        'options' => array_combine($zones, $cities)
    );
    wp_add_inline_script( 'mylerz-call-select2', 'jQuery(document).ready(function( $ ) {$(".custom-class-backend").select2();});' );
    return $fields;
}


#endregion

#region adding select neighborhood to FrontEnd billing address

if($mylerzOptions['merchant_country'] == 'EG' || $mylerzOptions['merchant_country'] == '') {
    add_filter('woocommerce_checkout_fields', 'mylerz_zoneDropdownFrontEnd', PHP_INT_MAX);

    function mylerz_zoneDropdownFrontEnd($fields)
    {
        global $cities;

        global $zones;

        $neighborhoods = GetCityZoneList();

        $fields['billing']['billing_state']['required'] = true;
        $fields['billing']['billing_state']['priority'] = 80;

        $fields['shipping']['shipping_state']['required'] = true;
        $fields['shipping']['shipping_state']['priority'] = 80;

        $fields['billing']['billing_neighborhood']   = array(
            'label'        => esc_html__('Neighborhood', 'mylerz'),
            'required'     => true,
            'show'         => true,
            'class'        => array('form-row-wide'),
            'placeholder'  => esc_html__('Select Neighborhood', 'mylerz'),
            'type' => 'select',
            'options' => $neighborhoods['Zones'],
            'priority'     => 85
        );

        $fields['billing']['billing_compound']   = array(
            'label'        => esc_html__('Compound', 'mylerz'),
            'required'     => false,
            'show'         => false,
            'class'        => array('form-row-wide'),
            'placeholder'  => esc_html__('Select Compound', 'mylerz'),
            'type' => 'select',
            'options' => $neighborhoods['Zones'],
            'priority'     => 87
        );

        $fields['shipping']['shipping_neighborhood']   = array(
            'label'        => esc_html__('Neighborhood', 'mylerz'),
            'required'     => false,
            'show'         => true,
            'class'        => array('form-row-wide'),
            'placeholder'  => esc_html__('Select Neighborhood', 'mylerz'),
            'type' => 'select',
            'options' => $neighborhoods,
            'priority'     => 85
        );
        $fields['shipping']['shipping_compound']   = array(
            'label'        => esc_html__('Compound', 'mylerz'),
            'required'     => false,
            'show'         => false,
            'class'        => array('form-row-wide'),
            'placeholder'  => esc_html__('Select Compound', 'mylerz'),
            'type' => 'select',
            'options' => $neighborhoods,
            'priority'     => 87
        );
        return $fields;
    }

    // Validate the neighborhood field.

    add_action('woocommerce_checkout_process', 'mylerz_action_woocommerce_after_checkout_validation');
    function mylerz_action_woocommerce_after_checkout_validation() {
        // Check if set, if its not set add an error.

        if ( ! sanitize_text_field($_POST['billing_neighborhood'])  && !empty(sanitize_text_field($_POST['billing_country'])) && sanitize_text_field($_POST['billing_country']) == "EG" ){
          wc_add_notice( esc_html__( 'Billing Neighborhood is required.' ), 'error' );
        }

        if ( ! sanitize_text_field($_POST['shipping_neighborhood'])  && !empty(sanitize_text_field($_POST['shipping_country'])) && sanitize_text_field($_POST['shipping_country']) == "EG" && sanitize_text_field($_POST['ship_to_different_address']) ){
          wc_add_notice( esc_html__( 'Shipping Neighborhood is required.' ), 'error' );
       }
    }
} else {
    //add_filter('woocommerce_states', 'mylerz_wc_states');

    // function  mylerz_wc_states() {
    //     //get countries allowed by store owner
    //     $allowed = mylerz_get_store_allowed_countries();

    //     $states = array();

    //     if (!empty( $allowed ) ) {
    //         foreach ($allowed as $code => $country) {
    //             if (! isset( $states[$code] ) && file_exists(plugin_dir_path( __FILE__ ) . '/states/' . $code . '.php')) {
    //                 include(plugin_dir_path( __FILE__ ) . '/states/' . $code . '.php');
    //             }
    //         }
    //     }

    //     return $states;
    // }

    // function mylerz_get_store_allowed_countries() {
    //     return array_merge( WC()->countries->get_allowed_countries(), WC()->countries->get_shipping_countries() );
    // }

    add_filter('woocommerce_checkout_fields', 'mylerz_zoneDropdownFrontEnd', PHP_INT_MAX);

    function mylerz_zoneDropdownFrontEnd($fields)
    {
        global $cities;

        global $zones;


        $neighborhoods = GetCityZoneList();

        $fields['billing']['billing_state']['required'] = true;
        $fields['billing']['billing_state']['priority'] = 80;

        $fields['shipping']['shipping_state']['required'] = true;
        $fields['shipping']['shipping_state']['priority'] = 80;

        $fields['billing']['billing_neighborhood']   = array(
            'label'        => esc_html__('Neighborhood', 'mylerz'),
            'required'     => true,
            'show'         => true,
            'class'        => array('form-row-wide'),
            'placeholder'  => esc_html__('Select Neighborhood', 'mylerz'),
            'type' => 'select',
            'options' => $neighborhoods['Zones'],
            'priority'     => 85
        );
        $fields['billing']['billing_compound']   = array(
            'label'        => esc_html__('Compound', 'mylerz'),
            'required'     => false,
            'show'         => false,
            'class'        => array('form-row-wide'),
            'placeholder'  => esc_html__('Select Compound', 'mylerz'),
            'type' => 'select',
            'options' => $neighborhoods['Zones'],
            'priority'     => 87
        );

        $fields['shipping']['shipping_neighborhood']   = array(
            'label'        => esc_html__('Neighborhood', 'mylerz'),
            'required'     => false,
            'show'         => true,
            'class'        => array('form-row-wide'),
            'placeholder'  => esc_html__('Select Neighborhood', 'mylerz'),
            'type' => 'select',
            'options' => $neighborhoods['Zones'],
            'priority'     => 85
        );
        $fields['shipping']['shipping_compound']   = array(
            'label'        => esc_html__('Compound', 'mylerz'),
            'required'     => false,
            'show'         => false,
            'class'        => array('form-row-wide'),
            'placeholder'  => esc_html__('Select Compound', 'mylerz'),
            'type' => 'select',
            'options' => $neighborhoods['Zones'],
            'priority'     => 87
        );
        return $fields;
    }

    // Validate the neighborhood field.

    add_action('woocommerce_checkout_process', 'mylerz_action_woocommerce_after_checkout_validation');
    function mylerz_action_woocommerce_after_checkout_validation() {
        // Check if set, if its not set add an error.
        if ( ! sanitize_text_field($_POST['billing_neighborhood'])  && !empty(sanitize_text_field($_POST['billing_country'])) && sanitize_text_field($_POST['billing_country']) == "MA" ){
          wc_add_notice( esc_html__( 'Billing Neighborhood is required.' ), 'error' );
        }

        if ( ! sanitize_text_field($_POST['shipping_neighborhood'])  && !empty(sanitize_text_field($_POST['shipping_country'])) && sanitize_text_field($_POST['shipping_country']) == "MA" && sanitize_text_field($_POST['ship_to_different_address']) ){
          wc_add_notice( esc_html__( 'Shipping Neighborhood is required.' ), 'error' );
       }
    }
}

// jQuery
function mylerz_action_woocommerce_after_order_notes( $checkout ) {
    ?>
    <script>
        (function($) {
            $(document).ready(function () {
                required_or_optional(); //this calls it on load
                $( '#billing_country' ).change( required_or_optional );

                function required_or_optional() {
                    if ( $( '#billing_country' ).val() == 'EG' || $( '#billing_country' ).val() == 'MA' || $( '#billing_country' ).val() == 'TN' || $( '#billing_country' ).val() == 'DZ') {
                        // Required
                        $( '#billing_neighborhood' ).prop( 'required', true );
                        $( 'label[for="billing_neighborhood"] .optional' ).remove();
                        $( 'label[for="billing_neighborhood"]' ).append( '<abbr class="required" title="required">*</abbr>' );
                        $('#billing_neighborhood_field').show();
                    } else {
                        $( '#billing_neighborhood' ).removeProp( 'required' );
                        $( 'label[for="billing_neighborhood"] .required' ).remove();

                        // Avoid append this multiple times
                        if ( $( 'label[for="billing_neighborhood"] .optional' ).length == 0 ) {
                            $( 'label[for="billing_neighborhood"]' ).append( '<span class="optional"></span>' );
                        }
                        $('#billing_neighborhood_field').hide();
                    }

                    if ( $( '#shipping_country' ).val() == 'EG' || $( '#shipping_country' ).val() == 'MA' || $( '#shipping_country' ).val() == 'TN' || $( '#shipping_country' ).val() == 'DZ') {
                        // Required
                        $( '#shipping_neighborhood' ).prop( 'required', true );
                        $( 'label[for="shipping_neighborhood"] .optional' ).remove();
                        $( 'label[for="shipping_neighborhood"]' ).append( '<abbr class="required" title="required">*</abbr>' );
                        $('#shipping_neighborhood_field').show();
                    } else {
                        $( '#shipping_neighborhood' ).removeProp( 'required' );
                        $( 'label[for="shipping_neighborhood"] .required' ).remove();

                        // Avoid append this multiple times
                        if ( $( 'label[for="shipping_neighborhood"] .optional' ).length == 0 ) {
                            $( 'label[for="shipping_neighborhood"]' ).append( '<span class="optional"></span>' );
                        }
                        $('#shipping_neighborhood_field').hide();
                    }
                }
            });
        })(jQuery);
    </script>
    <?php
}
add_action( 'woocommerce_after_checkout_form', 'mylerz_action_woocommerce_after_order_notes', 10, 1 );


add_filter('woocommerce_billing_fields', 'mylerz_stateRequiredBilling', PHP_INT_MAX);

function mylerz_stateRequiredBilling($fields)
{
    $fields['billing_state']['required'] = true;
    $fields['billing_state']['priority'] = 80;
    return $fields;
}

add_filter('woocommerce_shipping_fields', 'mylerz_stateRequiredShipping', PHP_INT_MAX);

function mylerz_stateRequiredShipping($fields)
{
    $fields['shipping_state']['required'] = true;
    $fields['shipping_state']['priority'] = 80;
    return $fields;
}

#endregion

#region adding neighborhood header
function mylerz_add_neighborhood_column_header($columns)
{

    $new_columns = array();

    foreach ($columns as $column_name => $column_info) {

        $new_columns[$column_name] = $column_info;

        if ('order_date' === $column_name) {
            $new_columns['neighborhood'] = esc_html__('Neighborhood', 'mylerz');
        }
    }

    return $new_columns;
}
add_filter('manage_edit-shop_order_columns', 'mylerz_add_neighborhood_column_header', 20);

#endregion

#region adding neighborhood content

function mylerz_add_neighborhood_column_content($column)
{
    global $post;
    $order = wc_get_order($post->ID);
    $orderData = $order->get_data();

    if ('neighborhood' === $column) {

        echo esc_html(mylerz_get_meta_data($post->ID, '_shipping_neighborhood', true));
    }
}
add_action('manage_shop_order_posts_custom_column', 'mylerz_add_neighborhood_column_content');

#endregion

#region adding select warehouse on item table

add_action('woocommerce_admin_order_item_headers', 'mylerz_admin_order_items_headers');
function mylerz_admin_order_items_headers($order)
{
    $column_name = 'Warehouse';
    echo '<th>' . esc_html($column_name) . '</th>';
}


add_action('woocommerce_admin_order_item_values', 'mylerz_woocommerce_admin_order_item_values', 10, 3);
function mylerz_woocommerce_admin_order_item_values($_product, $item, $item_id)
{
    global $integrationApi;

    $url = esc_url($integrationApi . '/api/orders/GetWarehouses');

    $token = get_option('access_token');

    $warehouses = mylerzGetWarehouses($url, $token);

    // $selectedWarehouse = mylerz_get_meta_data($item_id, 'warehouse', true);
    $selectedWarehouse = get_post_meta($item_id, 'warehouse', true);

    if ($_product != NULL) {
        echo '<td> <select name="warehouse' . esc_html($item_id) . '">';
        echo '<option disabled selected value="">'. esc_html__('Select Warehouse', 'mylerz') .'</option>';
        foreach ($warehouses["Warehouses"] as $warehouse) {
            if ($warehouse == $selectedWarehouse) {
                echo '<option selected>' . esc_html($warehouse) . '</option>';
            } else {
                echo '<option>' . esc_html($warehouse) . '</option>';
            }
        }
        echo ' </select> </td>';
    } else {
        echo '<td></td>';
    }
}

add_action('woocommerce_order_item_add_action_buttons', 'mylerz_action_woocommerce_order_item_add_action_buttons', 10, 1);
// define the woocommerce_order_item_add_action_buttons callback
function mylerz_action_woocommerce_order_item_add_action_buttons($order)
{
    echo '<button type="button" onclick="document.post.submit();" class="button generate-items">' . esc_html__('Update', 'mylerz') . '</button>';
    // indicate its taopix order generator button
    echo '<input type="hidden" value="1" name="renew_order" />';
}

add_action('save_post', 'mylerz_renew_save_again', 10, 3);
function mylerz_renew_save_again($post_id, $post, $update)
{
    $slug = 'shop_order';
    if (is_admin()) {
        if ($slug != $post->post_type) {
            return;
        }
        if (sanitize_text_field(isset($_POST['renew_order'])) && sanitize_text_field($_POST['renew_order'])) {
            foreach ($_POST as $key => $value) {
                if (strpos($key, 'warehouse') !== false) {
                    $id = explode("warehouse", $key)[1];
                    echo '<pre>';
                    echo var_dump(esc_html($value));
                    echo '</pre>';

                    mylerz_update_meta_data($id, 'warehouse', esc_html($value));
                }
            }
        }
    }
}
#endregion

#region Adding Mylerz Setting in Shipping Page
function mylerz_shipping_method_init()
{
    include_once('includes/shipping/mylerz-shipping-method.php');
}
add_action('woocommerce_shipping_init', 'mylerz_shipping_method_init');



function mylerz_add_shipping_method($methods)
{
    $methods['mylerz'] = 'Mylerz_Shipping_Method';
    return $methods;
}
add_filter('woocommerce_shipping_methods', 'mylerz_add_shipping_method');

#endregion

#region Adding Bulk Fulfillment Button

add_action('admin_footer', 'mylerz_bulk_admin_footer',80);

function mylerz_bulk_admin_footer()
{
    global $post_type;
    if ((sanitize_text_field(isset($_GET['page'])) && $_GET['page'] == 'wc-orders') || ($post_type == 'shop_order' && sanitize_text_field(isset($_GET['post_type'])))) {
        include_once('templates/adminhtml/bulk.php');
        mylerz_display_bulkFulfillment_button();
    }
}



#endregion

#region Adding Bulk Print AWB Button

add_action('admin_footer', 'mylerz_bulk_print_awb',20);

function mylerz_bulk_print_awb()
{
    // update_option('access_token', " ");
    global $post_type;

    if ((sanitize_text_field(isset($_GET['page'])) && $_GET['page'] == 'wc-orders') || ($post_type == 'shop_order' && sanitize_text_field(isset($_GET['post_type'])))) {
        include_once('templates/adminhtml/bulk_awb.php');
        mylerz_display_bulkPrintAWB_button();
    }
}

#endregion

#region Adding Bulk Print AWB Button

add_action('admin_footer', 'mylerz_bulk_cancel_awb',20);

function mylerz_bulk_cancel_awb()
{
    global $post_type;

    if ((sanitize_text_field(isset($_GET['page'])) && $_GET['page'] == 'wc-orders') || ($post_type == 'shop_order' && sanitize_text_field(isset($_GET['post_type'])))) {
        include_once('templates/adminhtml/bulk_cancel.php');
        mylerz_display_bulkCancelAWB_button();
    }
}

#endregion

#region Adding Bulk Print AWB Button

add_action('admin_footer', 'mylerz_bulk_delivery_status',20);

function mylerz_bulk_delivery_status()
{
    global $post_type;

    if ((sanitize_text_field(isset($_GET['page'])) && $_GET['page'] == 'wc-orders') || ($post_type == 'shop_order' && sanitize_text_field(isset($_GET['post_type'])))) {
        include_once('templates/adminhtml/bulk_delivery_status.php');
        mylerz_display_bulkDeliveryStatus_button();
    }
}

#endregion

#region Adding Create Pickup Button

add_action('admin_footer', 'mylerz_create_pickup_order',50);

function mylerz_create_pickup_order()
{
    // update_option('access_token', " ");
    global $post_type;

    if ((sanitize_text_field(isset($_GET['page'])) && $_GET['page'] == 'wc-orders') || ($post_type == 'shop_order' && sanitize_text_field(isset($_GET['post_type'])))) {
        include_once('templates/adminhtml/create_pickup_order.php');
        mylerz_display_create_pickup_order_button();
    }
}

#endregion

#region Adding Bulk Return Button
if($mylerzOptions['bulk_return'] == 'yes') {
    add_action('admin_footer', 'mylerz_bulk_return_admin_footer', 15);

    function mylerz_bulk_return_admin_footer()
    {
        global $post_type;

        if ((sanitize_text_field(isset($_GET['page'])) && $_GET['page'] == 'wc-orders') || ($post_type == 'shop_order' && sanitize_text_field(isset($_GET['post_type'])))) {
            include_once('templates/adminhtml/bulk_return.php');
            mylerz_display_bulkReturn_button();
        }
    }
}
#region load custom select script
function mylerz_load_drop_script()
{
    global $integrationApi;
    global $mylerzOptions;

    $states = GetCityZoneList();

    wp_register_script(
        'dynamic_drop_down',
        esc_js(plugin_dir_url(__FILE__) . 'assets/js/dynamic_drop_down.js'),
        array('jquery'),
        '1.0.0',
        true
    );
    wp_enqueue_script('dynamic_drop_down');

    wp_localize_script(
        "dynamic_drop_down",
        'config',
        array(
            'integration_api' => esc_url($integrationApi),
            'merchant_country' => $mylerzOptions['merchant_country'],
            'states' => $states,
        )
    );
}

function mylerz_load_drop_script_admin()
{
    global $post_type;

    if ((sanitize_text_field(isset($_GET['page'])) && $_GET['page'] == 'wc-orders') || ($post_type == 'shop_order' && sanitize_text_field(isset($_GET['post_type'])))) {
        global $post;
        global $integrationApi;
        global $mylerzOptions;
        $states = GetCityZoneList();

        wp_register_script(
            'dynamic_drop_down_admin',
            esc_js(plugin_dir_url(__FILE__) . 'assets/js/dynamic_drop_down_admin.js'),
            array('jquery'),
            '1.0.0',
            true
        );
        wp_enqueue_script('dynamic_drop_down_admin');

        wp_register_script(
            'pdf-lib',
            esc_js(plugin_dir_url(__FILE__) . 'assets/js/pdf-lib.min.js'),
            array('jquery'),
            '1.0.0',
            true
        );
        wp_enqueue_script('pdf-lib');

        wp_localize_script(
            "dynamic_drop_down_admin",
            'config',
            array(
                'merchant_country' => $mylerzOptions['merchant_country'],
                'shipping_neighborhood' => $post ? mylerz_get_meta_data($post->ID, '_shipping_neighborhood', true) : '',
                'billing_neighborhood' => $post ? mylerz_get_meta_data($post->ID, '_billing_neighborhood', true) : '',
                'shipping_compound' => $post ? mylerz_get_meta_data($post->ID, '_shipping_compound', true) : '',
                'billing_compound' => $post ? mylerz_get_meta_data($post->ID, '_billing_compound', true) : '',
                'integration_api' => esc_url($integrationApi),
                'states' => $states,
            )
        );
    }
}

function mylerz_add_script_at_checkout()
{
    wp_register_script(
        'mylerz-include-select2',
        esc_js(plugin_dir_url(__FILE__)) . 'assets/js/sel.js',
        array('jquery'),
        '1.0.0',
        true
    );
    wp_enqueue_script('mylerz-include-select2');
}

add_action('woocommerce_after_checkout_form', 'mylerz_add_script_at_checkout');
add_action('wp_enqueue_scripts', 'mylerz_load_drop_script', PHP_INT_MAX);
add_action('admin_enqueue_scripts', 'mylerz_load_drop_script_admin', PHP_INT_MAX);


#endregion

#region pdf.js

function mylerz_load_pdfjs_script()
{
    wp_register_script(
        'pdfjs',
        esc_js(plugin_dir_url(__FILE__) . 'assets/js/pdf.js'),
        array('jquery'),
        '1.0.0',
        true
    );
    wp_enqueue_script('pdfjs');
}


add_action('admin_enqueue_scripts', 'mylerz_load_pdfjs_script');

#endregion

#region awb.js

function mylerz_load_awb_script()
{
    wp_register_script(
        'awbjs',
        esc_js(plugin_dir_url(__FILE__) . 'assets/js/awb.js'),
        array('jquery'),
        '1.0.0',
        true
    );
    wp_enqueue_script('awbjs');
}


add_action('admin_enqueue_scripts', 'mylerz_load_awb_script');

#endregion

#region print.js

function mylerz_load_printjs_script()
{
    wp_register_script(
        'printjs',
        esc_js(plugin_dir_url(__FILE__) . 'assets/js/print.min.js'),
        array('jquery'),
        '1.0.0',
        true
    );
    wp_enqueue_script('printjs');
}


add_action('admin_enqueue_scripts', 'mylerz_load_printjs_script');

#endregion

#region loading style.css

function mylerz_load_css_file()
{
    wp_register_style('custom_mylerz_css', esc_js(plugin_dir_url(__FILE__) . 'assets/css/style.css'));
    wp_enqueue_style('custom_mylerz_css');
}

add_action('admin_enqueue_scripts', 'mylerz_load_css_file');

#endregion

#region adding ajax bulk fulfill handler

add_action('wp_ajax_mylerzBulkFulfillOrdersById', 'mylerzBulkFulfillOrdersById');
add_action('wp_ajax_nopriv_mylerzBulkFulfillOrdersById', 'mylerzBulkFulfillOrdersById');


function mylerzBulkFulfillOrdersById()
{
    global $integrationApi; //= 'http://41.33.122.61:58639';
    // should read from cookies or localstorage the token
    $token = get_option('access_token');

    $ordersIds = array_map( 'sanitize_text_field', $_POST['ordersIds'] );
    $bulkwarehouse = sanitize_text_field($_POST['warehouse']);

    $orders = mylerzGetOrders($ordersIds);




    try {
        $lineItems = mylerzFlatten(mylerzGetLineItemsOfOrders($orders));


    } catch (\Throwable $th) {
        exit(json_encode(array(
            'Status' => 'Failed',
            'Message' => "Error Flattening Line Items",
            'Error' => $th->getMessage(),
        )));
    }
    try {

        mylerzStampWarehouseMetatoItems($lineItems, $bulkwarehouse);
    } catch (\Throwable $th) {
        exit(json_encode(array(
            'Status' => 'Failed',
            'Message' => "Error Stamping Line Items",
            'Error' => $th->getMessage(),
            'bulkwarehouse' => $bulkwarehouse
        )));
    }

    try {
        // testtest
        $mylerzOrders = array_map('constructMylerzOrder', $orders, array_keys($orders));
        // print_r('<pre>');
        // print_r($mylerzOrders);
        // die;

    } catch (\Throwable $th) {
        exit(json_encode(array(
            'Status' => 'Failed',
            'Message' => "Error Constructing PickupOrder",
            'Error' => $th->getMessage(),
            'pickupOrder' => $mylerzOrders
        )));
    }

    $pickupOrdersGroupedByWarehouse = array_merge_recursive(...$mylerzOrders);





    $packagesResponse = array_map(function ($pickupOrders) use ($integrationApi, $token, $orders) {


        if (gettype($pickupOrders) == "array") {
            $pickupOrderDecoded = array_map(function ($order) {
                return json_decode($order);
            }, $pickupOrders);
        } else {
            $pickupOrderDecoded = array(json_decode($pickupOrders));
        }

        $response = mylerzUploadPackages(esc_url($integrationApi . '/api/orders/addorders'), $pickupOrderDecoded, $token);




        if ($response["Status"] === "Failed") {
            exit(json_encode(array(
                'Status' => 'Failed',
                'Message' => 'Error Adding Pickup Order',
                'Error' => $response["Error"],
                'MylerzOrder' => $pickupOrderDecoded
            )));
        }

        return $response["Packages"];
    }, $pickupOrdersGroupedByWarehouse);

    $statusChangedResult = mylerzChangeStatusToFulfilled($orders);
    if ($statusChangedResult["Status"] === "Failed") {

        exit(json_encode(array(
            'Status' => 'Failed',
            'Message' => 'Error Changing Status To Fulfilled',
            'Error' => $statusChangedResult["Error"],
            'MylerzOrder' => $mylerzOrders
        )));
    }

    $packages = array_merge(...array_values($packagesResponse));


    $packagesGroupedByReference = mylerzGroupBy($packages, "Reference");

    $barcodes = array_map(function ($packagesArray) use ($ordersIds) {
        $barcode = "BarCode";
        $barcodes = array_map(function ($package) use ($barcode) {
            return $package->$barcode;
        }, $packagesArray);


        return $barcodes;
    }, $packagesGroupedByReference);

    rsort($ordersIds);
    array_map(function ($barcodeList, $orderId) {
        if (metadata_exists("post", $orderId, "barcode")) {
            mylerz_delete_meta_data($orderId, "barcode");
        }
        array_map(function ($barcode) use ($orderId) {
            mylerz_add_meta_data($orderId, 'barcode', $barcode);
        }, array_values($barcodeList));
    }, $barcodes, $ordersIds);


    $barcodes = array_merge(...array_values($barcodes));




    $response = mylerzGetAWB(esc_url($integrationApi . '/api/packages/GetAWB'), $barcodes, $token);

    if ($response["Status"] === "Failed")
        exit(json_encode(array(
            'Status' => 'Failed',
            'Message' => 'Error Getting AWB',
            'Error' => $response["Error"],
            'MylerzOrder' => $mylerzOrders
        )));


    exit(json_encode($response));
}


#endregion my mamay is the best the

#region adding ajax bulk print handler

add_action('wp_ajax_mylerzBulkPrintAWB', 'mylerzBulkPrintAWB');
add_action('wp_ajax_nopriv_mylerzBulkPrintAWB', 'mylerzBulkPrintAWB');

function mylerzBulkPrintAWB()
{

    global $integrationApi;

    $token = get_option('access_token');

    $ordersIds = array_map( 'sanitize_text_field', $_POST['ordersIds'] );

    // $orders = mylerzGetOrders($ordersIds);

    $barcodes = array_map('mylerzGetBarcodesMeta', $ordersIds);

    $barcodes = array_merge(...$barcodes);

    $response = mylerzGetAWB(esc_url($integrationApi . '/api/packages/GetAWB'), $barcodes, $token);

    if ($response["Status"] === "Failed")
        exit(json_encode(array(
            'Status' => 'Failed',
            'Error' => $response["Error"]
        )));


    exit(json_encode($response));
}

#endregion

#nerdsArena Cancel

add_action('wp_ajax_mylerzBulkCancelAWB', 'mylerzBulkCancelAWB');
add_action('wp_ajax_nopriv_mylerzBulkCancelAWB', 'mylerzBulkCancelAWB');

function mylerzBulkCancelAWB()
{

    global $integrationApi;

    $token = get_option('access_token');

    $ordersIds = array_map( 'sanitize_text_field', $_POST['ordersIds'] );

    $orders = mylerzGetOrders($ordersIds);

    $barcodes = array_map('mylerzGetBarcodesMeta', $ordersIds);

    $barcodes = array_merge(...$barcodes);

    $response = mylerzCancelAWB(esc_url($integrationApi . '/api/packages/CancelPackage'), $barcodes, $token);

    if ($response["Status"] === "Failed")
        exit(json_encode(array(
            'Status' => 'Failed',
            'Error' => $response["Error"],
            'Description' => $response["ErrorDescription"]
        )));

    $statusChangedResult = mylerzChangeStatusToCanceled($orders, $response['CancelledOrders']);
    if ($statusChangedResult["Status"] === "Failed") {
        exit(json_encode(array(
            'Status' => 'Failed',
            'Message' => 'Error Changing Status To Canceled',
            'Error' => $statusChangedResult["Error"]
        )));
    }

    exit(json_encode($response));
}

#end nerdsArena Cancel

#nerdsArena BulkStatusUpdate

add_action('wp_ajax_mylerzBulkStatusUpdate', 'mylerzBulkStatusUpdate');
add_action('wp_ajax_nopriv_mylerzBulkStatusUpdate', 'mylerzBulkStatusUpdate');

function mylerzBulkStatusUpdate()
{

    global $integrationApi;

    $token = get_option('access_token');

    $ordersIds = array_map( 'sanitize_text_field', $_POST['ordersIds'] );

    $orders = mylerzGetOrders($ordersIds);

    $barcodesListPerOrder = array_map('mylerzGetBarcodesMeta', $ordersIds);

    $barcodes = array_merge(...$barcodesListPerOrder);

    $response = mylerzGetDeliveryStatus(esc_url($integrationApi . '/api/packages/GetPackageListStatus'), $barcodes, $token);

    if ($response["Status"] === "Failed")
        exit(json_encode(array(
            'Status' => 'Failed',
            'Error' => $response["Error"],
            'Description' => $response["ErrorDescription"]
        )));


    $packagesToUpdateDeliveryObj = [];
    foreach($response["UpdatedOrders"] as $updatedOrder){
        if($updatedOrder->BarCode != NULL){
            $packagesToUpdateDeliveryObj[$updatedOrder->BarCode] = $updatedOrder->Status;
        }else{
            $packagesToUpdateDeliveryObj[$updatedOrder->BarCode] = "";
        }

    }

    $orderNewStatuses = array_map(function($barcode) use ($packagesToUpdateDeliveryObj){
        $mylerzStatuses[] = $packagesToUpdateDeliveryObj[$barcode];
        return $mylerzStatuses;
    },$barcodes);

    array_map(function ($mylerzStatus, $orderId) {
        if (metadata_exists("post", $orderId, "mylerzStatus")) {
            mylerz_delete_meta_data($orderId, "mylerzStatus");
        }

        array_map(function ($status) use ($orderId) {
            if($status != ""){
                mylerz_add_meta_data($orderId, 'mylerzStatus', $status);
            }
        }, array_values($mylerzStatus));
    }, $orderNewStatuses, $ordersIds);

    $statusChangedResult = changeStatusBasedOnMylerz($orders);
    if ($statusChangedResult["Status"] === "Failed") {

        exit(json_encode(array(
            'Status' => 'Failed',
            'Message' => 'Error Getting Mylerz Statuses',
            'Error' => $statusChangedResult["Error"]
        )));
    }

    exit(json_encode($response));
}

#end nerdsArena Cancel

#region adding ajax bulk print handler

add_action('wp_ajax_mylerzCreatePickupOrder', 'mylerzCreatePickupOrder');
add_action('wp_ajax_nopriv_mylerzCreatePickupOrder', 'mylerzCreatePickupOrder');

function mylerzCreatePickupOrder()
{

    global $integrationApi;

    $token = get_option('access_token');

    $ordersIds = array_map( 'sanitize_text_field', $_POST['ordersIds'] );

    $orders = mylerzGetOrders($ordersIds);

    $barcodesListPerOrder = array_map('mylerzGetBarcodesMeta', $ordersIds);

    $barcodesFlat = array_merge(...$barcodesListPerOrder);

    $response = mylerzCreatePickup(esc_url($integrationApi . '/api/packages/CreateMultiplePickup'), $barcodesFlat, $token);

    if ($response["Status"] === "Failed") {

        exit(json_encode(array(
            'Status' => 'Failed',
            'Error' => $response["Error"],
            'Description' => $response["ErrorDescription"]

        )));
    }

    $packagesToPickupOrderObj = [];

    foreach($response["PickupOrders"] as $pickupOrder){
        foreach($pickupOrder->PickupPackages as $package){
            if($pickupOrder->PickupOrderCode!=NULL){
                $packagesToPickupOrderObj[$package->Barcode] = $pickupOrder->PickupOrderCode;
            }else{
                $packagesToPickupOrderObj[$package->Barcode] = "";
            }
        }
    }

    $pickupOrderCodesPerOrder = array_map(function($barcodes) use ($packagesToPickupOrderObj){
        $pickupOrderCodes = array_map(function($barcode) use ($packagesToPickupOrderObj){
            return $packagesToPickupOrderObj[$barcode];
        },$barcodes);

        return array_unique($pickupOrderCodes);

    },$barcodesListPerOrder);


    array_map(function ($pickupOrderList, $orderId) {
        if (metadata_exists("post", $orderId, "pickupOrderCode")) {
            mylerz_delete_meta_data($orderId, "pickupOrderCode");
        }
        array_map(function ($pickupOrderCode) use ($orderId) {
            if($pickupOrderCode!=""){
                mylerz_add_meta_data($orderId, 'pickupOrderCode', $pickupOrderCode);
            }
        }, array_values($pickupOrderList));
    }, $pickupOrderCodesPerOrder, $ordersIds);

    $statusChangedResult = mylerzChangeStatusToCreated($orders);
    if ($statusChangedResult["Status"] === "Failed") {

        // exit(json_encode(array(
        //     'Status' => 'Failed',
        //     'Message' => 'Error Changing Status To Created',
        //     'Error' => $statusChangedResult["Error"],
        // )));
    }


    exit(json_encode(array(
        "CreateResponse" => $response,
        "pickupOrderCodesPerOrder" => $pickupOrderCodesPerOrder,
        "StatusChangeResult" => $statusChangedResult
    )));
}

#endregion

if($mylerzOptions['bulk_return'] == 'yes') {
    #region adding ajax bulk fulfill handler

    add_action('wp_ajax_mylerzBulkReturnOrdersById', 'mylerzBulkReturnOrdersById');
    add_action('wp_ajax_nopriv_mylerzBulkReturnOrdersById', 'mylerzBulkReturnOrdersById');


    function mylerzBulkReturnOrdersById()
    {
        global $integrationApi; //= 'http://41.33.122.61:58639';
        // should read from cookies or localstorage the token
        $token = get_option('access_token');

        $ordersIds = array_map( 'sanitize_text_field', $_POST['ordersIds'] );

        $orders = mylerzGetOrders($ordersIds);

        try {
            $mylerzOrders = array_map('constructMylerzReturnOrder', $orders, array_keys($orders));
        } catch (\Throwable $th) {
            exit(json_encode(array(
                'Status' => 'Failed',
                'Message' => "Error Constructing PickupOrder",
                'Error' => $th->getMessage(),
                'pickupOrder' => ''
            )));
        }

        $response = mylerzUploadPackages(esc_url($integrationApi . '/api/orders/addorders'), $mylerzOrders, $token);
            if ($response["Status"] === "Failed") {
                exit(json_encode(array(
                    'Status' => 'Failed',
                    'Message' => 'Error Adding Pickup Order',
                    'Error' => $response["Error"],
                    'MylerzOrder' => $mylerzOrders
                )));
            }

        $packagesResponse =  $response["Packages"];

        $statusChangedResult = changeStatusToMylerzReturn($orders);
        if ($statusChangedResult["Status"] === "Failed") {

            exit(json_encode(array(
                'Status' => 'Failed',
                'Message' => 'Error Changing Status To Mylerz Return',
                'Error' => $statusChangedResult["Error"],
                'MylerzOrder' => $mylerzOrders
            )));
        }

        $packagesGroupedByReference = mylerzGroupBy(array_values($packagesResponse), "Reference");

        $barcodes = array_map(function ($packagesArray) use ($ordersIds) {

            $barcode = "BarCode";
            $barcodes = array_map(function ($package) use ($barcode) {
                return $package->$barcode;
            }, $packagesArray);

            return $barcodes;
        }, $packagesGroupedByReference);

        rsort($ordersIds);

        array_map(function ($barcodeList, $orderId) {
            if (metadata_exists("post", $orderId, "mylerzReturnBarcode")) {
                mylerz_delete_meta_data($orderId, "mylerzReturnBarcode");
            }
            array_map(function ($barcode) use ($orderId) {
                mylerz_add_meta_data($orderId, 'mylerzReturnBarcode', $barcode);
            }, array_values($barcodeList));
        }, $barcodes, $ordersIds);
    }

    function constructMylerzReturnOrder($order, $index)
    {
        global $mylerzOptions;
        $compound =  mylerz_get_meta_data($order->get_id(), '_shipping_compound', true);
        $compoundPart = strlen($compound) > 0 ? $compound . "(Compound), " : "";
        $countryData = mylerzGetCountryData($order);
        return array(
            'WarehouseName' => '',
            'PickupDueDate' => date('Y-m-d H:i:s'),
            'Package_Serial' => $index + 1,
            'Reference' => 'R#' . $order->get_id(),
            'Description' => 'Return Order #' . $order->get_id(),
            'Service_Type' => $mylerzOptions['service_type'] ?? 'DTD',
            'Service' => 'ND',
            'Service_Category' => 'RETURN',
            'Payment_Type' => ($order->get_payment_method() === 'cod') ? 'COD' : 'PP',
            'COD_Value' => ($order->get_payment_method() === 'cod') ? ($order->get_total_refunded() * -1) : 0,
            'Pieces' => [array(
                'PieceNo' => 1,
                'Special_Notes' => ''   //note from customer or shop owner ?
            )],
            'Customer_Name' => $order->get_shipping_first_name() . ' ' . $order->get_shipping_last_name(),
            'Mobile_No' => $order->get_billing_phone(),
            'Street' => $compoundPart . $order->get_shipping_address_2() . ', ' . $order->get_shipping_address_1() . ', ' . $order->get_shipping_city(),
            'Country' => $countryData['country'],
            'Neighborhood' => $countryData['neighborhood'],
            'Address_Category' => 'H',
            'Currency' => $order->get_currency(),
        );
    }
}

#region adding ajax validate token

add_action('wp_ajax_mylerzValidateAndGenerateNewToken', 'mylerzValidateAndGenerateNewToken');
add_action('wp_ajax_nopriv_mylerzValidateAndGenerateNewToken', 'mylerzValidateAndGenerateNewToken');

function mylerzValidateAndGenerateNewToken()
{

    global $integrationApi;

    try {
        //code...
        list($validationResult, $warehouses, $error) = mylerzValidateToken(esc_url($integrationApi . '/api/orders/GetWarehouses'));
    } catch (\Throwable $th) {
        //throw $th;
        exit(json_encode(array(
            'Status' => 'Failed',
            'Message' => "Error validating token",
            'Error' => $th->getMessage(),
        )));
    }

    if ($validationResult == false) {
        try {
            //code...
            $response = mylerzRequestNewToken(esc_url($integrationApi . '/Token'));
        } catch (\Throwable $th) {
            //throw $th;
            exit(json_encode(array(
                'Status' => 'Failed',
                'Message' => "Error requesting new token",
                'Validate Result' => $error,
                'Error' => $th->getMessage(),
            )));
        }

        if ($response["Status"] == "Success") {

            update_option('access_token', $response["Token"]);
            $warehousesResponse = mylerzGetWarehouses(esc_url($integrationApi .  '/api/orders/GetWarehouses'), $response["Token"]);

            if ($warehousesResponse["Status"] == "Success") {

                exit(json_encode(array(
                    'Status' => 'Success',
                    'Message' => "New Token Generated Successfully",
                    'Validate Result' => $error,
                    'Warehouses' => $warehousesResponse["Warehouses"]
                )));
            } else {
                exit(json_encode(array(
                    'Status' => 'Failed',
                    'Message' => "Error Validating New Token",
                    'Validate Result' => $error,
                    'Validate New Token Result' => $warehousesResponse["Error"]
                )));
            }
        } else {
            exit(json_encode(array(
                'Status' => 'Failed',
                'Message' => "Error Requesting New Token",
                'Validate Result' => $error,
                'Error' => $response["Error"]
            )));
        }
    } else {
        exit(json_encode(array(
            'Status' => 'Success',
            'Message' => "Token Is Valid",
            'Warehouses' => $warehouses
        )));
    }
}

#endregion

#region adding ajax checkItemWarehouse

add_action('wp_ajax_mylerzCheckItemWarehouses', 'mylerzCheckItemWarehouses');
add_action('wp_ajax_nopriv_mylerzCheckItemWarehouses', 'mylerzCheckItemWarehouses');

function mylerzCheckItemWarehouses()
{

    // global $integrationApi;
    $ordersIds = array_map( 'sanitize_text_field', $_POST['ordersIds'] );

    $orders = mylerzGetOrders($ordersIds);

    $lineItems = mylerzFlatten(mylerzGetLineItemsOfOrders($orders));

    // update_option('access_token', $response->access_token);
    // $warehousesResponse = mylerzGetWarehouses($integrationApi .  '/api/orders/GetWarehouses', $response->access_token);

    $result = mylerz_array_every($lineItems, function ($item) {
        // $warehouse = mylerz_get_meta_data($item->get_id(), "warehouse", true);
        $warehouse = get_post_meta($item->get_id(), "warehouse", true);


        if (metadata_exists("post", $item->get_id(), "warehouse") && $warehouse !== "") {
            return true;
        }
        return false;
    });

    if ($result == true) {

        exit(json_encode(array(
            'ItemWarehouse' => true
        )));
    } else {

        exit(json_encode(array(
            'ItemWarehouse' => false
        )));
    }
}

#endregion

#region Helper Functions

function mylerzGetOrders($ordersIds)
{
    return array_map(
        function ($orderId) {
            return wc_get_order($orderId);
        },
        $ordersIds
    );
}

function mylerzGetLineItemsOfOrders($orders)
{
    return array_map(function ($order) {
        return $order->get_items();
    }, $orders);
}

function mylerzGetAddressList($orders)
{
    return array_map(function ($order) {
        $orderData = $order->get_data();
        return $orderData['shipping']['address_1'] . ', ' . $orderData['shipping']['address_2'] . ', ' . $orderData['shipping']['city'];
    }, $orders);
}

function mylerzGetZones($url, $addressList, $token)
{
    global $timeout;

    $response = wp_remote_post($url, array(
        'method'      => 'POST',
        'blocking'    => true,
        'timeout'     => $timeout,
        'headers'     =>  array(
            'content-type' => 'application/json',
            'Authorization' => 'bearer ' . $token
        ),
        'body'        =>  json_encode($addressList)
    ));

    $result = json_decode($response["body"]);

    if ($result->Message === "Authorization has been denied for this request.") {

        return array(
            'Status' => 'Failed',
            'Error' => $result->Message,
        );
    } else {

        if ($result->IsErrorState === TRUE) {
            return array(
                'Status' => 'Failed',
                'Error' => $result->ErrorDescription,
            );
        } else {
            return array(
                'Status' => 'Success',
                'Zones' => $result->Value,
            );
        }
    }
}

function mylerzGetWarehouses($url, $token)
{
    global $timeout;

    $response = wp_remote_post($url, array(
        'method'      => 'GET',
        'blocking'    => true,
        'timeout'     => $timeout,
        'headers'     =>  array(
            'content-type' => 'application/json',
            'Authorization' => 'bearer ' . $token
        ),
    ));
    if (is_wp_error($response)) {

        $error_message = $response->get_error_message();
        return array(
            'Status' => 'Failed',
            'Error' => $error_message,
        );
    } else {

        $result = json_decode($response["body"]);

        if (isset($result->Message) && $result->Message === "Authorization has been denied for this request.") {

            return array(
                'Status' => 'Failed',
                'Error' => $result->Message,
            );
        } else {

            if ($result->IsErrorState === TRUE) {
                return array(
                    'Status' => 'Failed',
                    'Error' => $result->ErrorDescription,
                );
            } else {
                return array(
                    'Status' => 'Success',
                    'Warehouses' => array_map(function ($warehouse) {
                        return $warehouse->Name;
                    }, $result->Value),
                );
            }
        }
    }
}
function constructMylerzOrder($order, $index)
{
    global $mylerzOptions;
    $orderItems = $order->get_items();
    $total_items_prices = 0;

    foreach ($orderItems as $item) {
        // $itemsGroupedByWarehouse[mylerz_get_meta_data($item->get_id(), 'warehouse', true)][] = $item;
        $itemsGroupedByWarehouse[get_post_meta($item->get_id(), 'warehouse', true)][] = $item;
        $total_items_prices += $item->get_total();
    }

    $numberOfWarehouses = count($itemsGroupedByWarehouse);

    $totalFees = $order->get_total() - $total_items_prices;

    $feesPerWarehouse = $totalFees / $numberOfWarehouses;

    $countryData = mylerzGetCountryData($order);

    $mylerzPackagesPerOrder = array_map(function ($itemsPerwarehouse) use ($order, $index, $feesPerWarehouse, $mylerzOptions, $countryData) {
        $compound =  mylerz_get_meta_data($order->get_id(), '_shipping_compound', true);
        $compoundPart = strlen($compound) > 0 ? $compound . "(Compound), " : "";
        return json_encode(array(
            // 'WarehouseName' => mylerz_get_meta_data($itemsPerwarehouse[0]->get_id(), 'warehouse', true),
            'WarehouseName' => get_post_meta($itemsPerwarehouse[0]->get_id(), 'warehouse', true),
            'PickupDueDate' => date('Y-m-d H:i:s'),
            'Package_Serial' => $index,
            'Reference' => '#' . $order->get_id(),
            'Description' => join("\r\n", array_map(function ($item) {
                return 'Title: ' . $item->get_name() . '( ' . $item->get_product()->get_sku() . ' ), Quantity: ' . $item->get_quantity();
            }, $itemsPerwarehouse)),
            'Service_Type' => $mylerzOptions['service_type'] ?? 'DTD',
            'Service' => 'ND',
            'Service_Category' => 'DELIVERY',
            'Payment_Type' => ($order->get_payment_method() === 'cod') ? 'COD' : 'PP',
            'COD_Value' => ($order->get_payment_method() === 'cod') ? round(array_sum(array_map(function ($item) {
                return  $item->get_total();
            }, $itemsPerwarehouse)) + $feesPerWarehouse, 2) : 0,
            'Pieces' => [array(
                'PieceNo' => 1,
                'Special_Notes' => "", //$order->get_customer_order_notes()   //note from customer or shop owner ?
            )],
            'Customer_Name' => $order->get_shipping_first_name() . ' ' . $order->get_shipping_last_name(),
            'Mobile_No' => $order->get_billing_phone(),
            'Street' => $compoundPart . $order->get_shipping_address_2() . ', ' . $order->get_shipping_address_1() . ', ' . $order->get_shipping_city(),
            'Country' => $countryData['country'],
            'Neighborhood' => $countryData['neighborhood'],
            'Address_Category' => 'H',
            //"Special_Notes" => $order->get_customer_order_notes(),
            'Currency' => $order->get_currency(),
        ));
    }, $itemsGroupedByWarehouse);

    return $mylerzPackagesPerOrder;
}

function mylerzUploadPackages($url, $mylerzOrder, $token)
{
    global $timeout;
    $response = wp_remote_post($url, array(
        'method'      => 'POST',
        'blocking'    => true,
        'timeout'     => $timeout,
        'headers'     =>  array(
            'content-type' => 'application/json',
            'Authorization' => 'bearer ' . $token
        ),
        'body'        =>  json_encode($mylerzOrder)
    ));

    if (is_wp_error($response)) {

        $error_message = $response->get_error_message();
        return array(
            'Status' => 'Failed',
            'Error' => $error_message,
            'Response' => $response,
            'Description' => ""
        );
    } else {

        $result = json_decode($response["body"]);

        if ($result->Message) {
            return array(
                'Status' => 'Failed',
                'Error' => $result->Message,
                'Description' => ""

            );
        }

        if ($result->IsErrorState === TRUE) {
            return array(
                'Status' => 'Failed',
                'Error' => $result,
            );
        } else {
            return array(
                'Status' => 'Success',
                'Packages' => $result->Value->Packages
            );
        }
    }
}

function mylerzGetAWB($url, $barcodeList, $token)
{

    global $timeout;

    $awbList =  array_map(function ($barcode) use ($token, $url,$timeout) {

        $response = wp_remote_post($url, array(
            'method'      => 'POST',
            'blocking'    => true,
            'timeout'     => $timeout,
            'headers'     =>  array(
                'content-type' => 'application/json',
                'Authorization' => 'bearer ' . $token
            ),
            'body'        =>  json_encode(array(
                'Barcode' => $barcode
            ))
        ));

        $result =  json_decode($response["body"]);

        if ($result->IsErrorState === TRUE) {
            return NULL;
        } else {
            return $result->Value;
        }
    }, $barcodeList);

    if (in_array(NULL, $awbList)) {
        return array(
            'Status' => 'Failed',
            'Error' => "Error Retrieving AWB",
        );
    } else {
        return array(
            'Status' => 'Success',
            'AWBList' => $awbList,
        );
    }
}
#nerdsarena
function mylerzCancelAWB($url, $barcodeList, $token)
{

     global $timeout;

    $requestObject =  array_map(function ($barcode) use ($token, $url) {
        return array(
            "Barcode" => $barcode
        );
    }, $barcodeList);

    $response = wp_remote_post($url, array(
        'method'      => 'POST',
        'blocking'    => true,
        'timeout'     => $timeout,
        'headers'     =>  array(
            'content-type' => 'application/json',
            'Authorization' => 'bearer ' . $token
        ),
        'body'        =>  json_encode($requestObject)
    ));

    if (is_wp_error($response)) {

        $error_message = $response->get_error_message();
        return array(
            'Status' => 'Failed',
            'Error' => $error_message,
        );
    } else {
        $result =  json_decode($response["body"]);

        if ($result->IsErrorState === TRUE) {
            return array(
                'Status' => 'Failed',
                'Error' => $result->Value,
                'Description' => $result->ErrorDescription
            );
        } else {
            return array(
                'Status' => 'Success',
                'CancelledOrders' => $result->Value,
            );
        }
    }
}
#end nerdsarena

#nerdsarena
function mylerzGetDeliveryStatus($url, $barcodeList, $token)
{
     global $timeout;

    $response = wp_remote_post($url, array(
        'method'      => 'POST',
        'blocking'    => true,
        'timeout'     => $timeout,
        'headers'     =>  array(
            'content-type' => 'application/json',
            'Authorization' => 'bearer ' . $token
        ),
        'body'        =>  json_encode($barcodeList)
    ));

    if (is_wp_error($response)) {

        $error_message = $response->get_error_message();
        return array(
            'Status' => 'Failed',
            'Error' => $error_message,
        );
    } else {
        $result =  json_decode($response["body"]);

        if ($result->IsErrorState === TRUE) {
            return array(
                'Status' => 'Failed',
                'Error' => $result->Value,
                'Description' => $result->ErrorDescription
            );
        } else {
            return array(
                'Status' => 'Success',
                'UpdatedOrders' => $result->Value,
            );
        }
    }
}
#end nerdsarena
function mylerzCreatePickup($url, $barcodeList, $token)
{

    global $timeout;

    $requestObject =  array_map(function ($barcode) use ($token, $url) {
        return array(
            "Barcode" => $barcode
        );
    }, $barcodeList);


    $response = wp_remote_post($url, array(
        'method'      => 'POST',
        'blocking'    => true,
        'timeout'     => $timeout,
        'headers'     =>  array(
            'content-type' => 'application/json',
            'Authorization' => 'bearer ' . $token
        ),
        'body'        =>  json_encode($requestObject)
    ));

    if (is_wp_error($response)) {

        $error_message = $response->get_error_message();
        return array(
            'Status' => 'Failed',
            'Error' => $error_message,
        );
    } else {


        $result =  json_decode($response["body"]);

        if ($result->IsErrorState === TRUE) {
            return array(
                'Status' => 'Failed',
                'Error' => $result->Value,
                'Description' => $result->ErrorDescription
            );
        } else {
            return array(
                'Status' => 'Success',
                'PickupOrders' => $result->Value,
            );
        }
    }
}

function mylerzChangeStatusToFulfilled($orders)
{
    $resultArray = array_map(function ($order) {
        return $order->update_status('fulfilled');
    }, $orders);

    return in_array(FALSE, $resultArray) ? array(
        'Status' => 'Failed',
        'Error' => 'Error Changing Status To Fulfilled',
    ) : array(
        'Status' => 'Success',
        'Message' => 'Status Changed To Fulfilled',
    );
}

function mylerzChangeStatusToCreated($orders)
{
    $resultArray = array_map(function ($order) {
        return $order->update_status('created');
    }, $orders);

    return in_array(FALSE, $resultArray) ? array(
        'Status' => 'Failed',
        'Error' => 'Error Changing Status To Created',
    ) : array(
        'Status' => 'Success',
        'Message' => 'Status Changed To Created',
    );
}

function mylerzChangeStatusToCanceled($orders, $responseArray)
{
    $resultArray = array_map(function ($response) use ($orders){
        $cancelledArray = array_map(function ($order) use ($response) {
            if($order->id == trim($response->ReferenceNumber, "R#") && $response->IsChanged == true) {
                return $order->update_status('cancelled');
            }
        }, $orders);

        return $cancelledArray;
    }, $responseArray);

    return in_array(FALSE, $resultArray) ? array(
        'Status' => 'Failed',
        'Error' => 'Error Changing Some Order Status To Canceled',
    ) : array(
        'Status' => 'Success',
        'Message' => 'Status Changed To Canceled',
    );
}

function changeStatusBasedOnMylerz($orders)
{
    $resultArray = array_map(function ($order) {
        if(mylerz_get_meta_data($order->id, 'mylerzStatus', true) == 'Delivered, Thank you :-)') {
            return $order->update_status('completed');
        }elseif(mylerz_get_meta_data($order->id, 'mylerzStatus', true) == 'Rejected - reason to be mentioned') {
            return $order->update_status('cancelled');
        }else {
            return true;
        }
    }, $orders);

    return in_array(FALSE, $resultArray) ? array(
        'Status' => 'Failed',
        'Error' => 'Error Changing Status To Completed',
    ) : array(
        'Status' => 'Success',
        'Message' => 'Statuses Updated',
    );
}

if($mylerzOptions['bulk_return'] == 'yes') {
    function changeStatusToMylerzReturn($orders)
    {
        $resultArray = array_map(function ($order) {
            return $order->update_status('mylerz-return');
        }, $orders);

        return in_array(FALSE, $resultArray) ? array(
            'Status' => 'Failed',
            'Error' => 'Error Changing Status To Mylerz Return',
        ) : array(
            'Status' => 'Success',
            'Message' => 'Status Changed To Mylerz Return',
        );
    }
}

function mylerzGetBarcodesMeta($orderId)
{
    // return metadata_exists("post", $orderId, "mylerzReturnBarcode") ? mylerz_get_meta_data($orderId, 'mylerzReturnBarcode') : mylerz_get_meta_data($orderId, 'barcode');

    // Get the WooCommerce order object
    $order = wc_get_order($orderId);

    // Use the WooCommerce `meta_exists` method to check for metadata
    if ($order->meta_exists('mylerzReturnBarcode')) {
        // Return normalized mylerzReturnBarcode meta data
        return mylerz_normalized_meta_data($orderId, 'mylerzReturnBarcode');
    } else {
        // Fallback to normalized barcode meta data

        return mylerz_normalized_meta_data($orderId, 'barcode');
    }

}


function mylerzSetBarcodesMeta($orderId, $barcodes)
{
    mylerz_add_meta_data($orderId, 'barcode', $barcodes);
}

function mylerzRequestNewToken($url)
{
    global $timeout;
    include_once('includes/shipping/mylerz-shipping-method.php');

    $settings = new Mylerz_Shipping_Method();

    $userName = $settings->settings['user_name'];
    $password = $settings->settings['password'];

    $response = wp_remote_post($url, array(
        'method'      => 'POST',
        'blocking'    => true,
        'timeout'     => $timeout,
        'headers'     =>  array(
            'content-type' => 'application/x-www-form-urlencoded',
        ),
        'body'        =>  array(
            'username' => $userName,
            'password' => $password,
            'grant_type' => 'password'
        )
    ));

    if (is_wp_error($response)) {

        $error_message = $response->get_error_message();
        return array(
            'Status' => 'Failed',
            'Error' => $error_message,
        );
    } else {
        $result = json_decode($response["body"]);
        if ($result->access_token) {
            return array(
                'Status' => 'Success',
                'Token' => $result->access_token,
            );
        } else {

            return array(
                'Status' => 'Failed',
                'Error' => "Wrong Mylerz Credentials",
            );
        }
    }
}

function mylerzValidateToken($url)
{

    $token = get_option('access_token');
    $response = mylerzGetWarehouses($url, $token);

    if ($response["Status"] == "Failed") {
        return array(false, [], $response["Error"]);
    } else if ($response["Status"] == "Success") {
        return array(true, $response["Warehouses"], "");
        // return array(false,[]);
    }
}

function mylerzFlatten(array $array)
{
    $return = array();
    array_walk_recursive($array, function ($a) use (&$return) {
        $return[] = $a;
    });
    return $return;
}
function mylerzStampWarehouseMetatoItems($orderItems, $bulkwarehouse)
{
    array_map(function ($item) use ($bulkwarehouse) {
        // echo '<pre>';
        // echo var_dump($item);
        // echo '</pre>';
        // die;
        $item_id = $item->get_id();
        if (get_post_meta($item_id, 'warehouse', true) == "") {
            add_post_meta($item_id, 'warehouse', $bulkwarehouse);
        }
        // if (mylerz_get_meta_data($item_id, 'warehouse', true) == "") {
        //     mylerz_add_meta_data($item_id, 'warehouse', $bulkwarehouse);
        // }
    }, $orderItems);

    // die();
}

function mylerzGroupBy(array $array, string $key)
{
    foreach ($array as $item) {
        $arrayGroup[$item->$key][] = $item;
    }

    return $arrayGroup;
}

function mylerz_array_every($array, $callback)
{

    return  !in_array(false,  array_map($callback, $array));
}

function mylerzGetCountryData($order) {
    global $mylerzOptions;
    if($mylerzOptions['merchant_country'] == 'TN') {
        $country = 'Tunisia';
        $neighborhood = 'TUN';
    }else {
        $country = 'Egypt';
        $neighborhood = mylerz_get_meta_data($order->get_id(), '_shipping_neighborhood', true);
         // print_r($neighborhood);
         // die;
        // testtest
    }
    return ['country' => $country, 'neighborhood' => $neighborhood];
}

function GetCityZoneList()
{
    global $timeout;
    global $integrationApi;

    $url = esc_url($integrationApi . '/api/packages/GetCityZoneList');

    $response = wp_remote_post($url, array(
        'method'      => 'GET',
        'blocking'    => true,
        'timeout'     => $timeout,
        'headers'     =>  array(
            'content-type' => 'application/json',
        ),
    ));
    if (is_wp_error($response)) {

        $error_message = $response->get_error_message();
        return array(
            'Status' => 'Failed',
            'Error' => $error_message,
        );
    } else {

        $result = json_decode($response["body"], true);

        if (!$result['IsErrorState']) {
            $cityNeighborhoodObject = array();
            $cityNeighborhoodObject['Zones'][""] = "Select Neighborhood";

            if (!$result['IsErrorState']) {
                foreach ($result['Value'] as $city) {
                    $cityNeighborhoodObject['States'][$city["Code"]] = $city["EnName"];
                    foreach ($city["Zones"] as $zone) {
                        $cityNeighborhoodObject['Zones'][$zone["Code"]] = $zone["EnName"];
                    }
                }
            }

            return $cityNeighborhoodObject;
        }
    }
}


/**
 * Add or modify States
 */
if($mylerzOptions['merchant_country'] != 'EG' || $mylerzOptions['merchant_country'] != '') {
    add_filter('woocommerce_states', 'mylerz_countries_woocommerce_states');

    function mylerz_countries_woocommerce_states($states)
    {
        global $mylerzOptions;

        $states = GetCityZoneList();
        $states[$mylerzOptions['merchant_country']] = $states['States'];

        return $states;
    }
}




// Mohammeed Abdelfattah




function enqueue_custom_checkout_script() {
    // Register your script
    wp_register_script('custom-checkout-script',
    esc_js(plugin_dir_url(__FILE__) . 'assets/js/custom-checkout.js'),
    array('jquery'), null, true);

    // Localize the script with the base URL
    wp_localize_script('custom-checkout-script', 'custom_checkout_params', array(
        'base_url' => get_site_url(), // This will pass the site's base URL
    ));

    // Enqueue the script
    wp_enqueue_script('custom-checkout-script');
}
add_action('wp_enqueue_scripts', 'enqueue_custom_checkout_script');




add_action( 'wp_loaded', function() {

    $neighborhoods_original = GetCityZoneList();

    // Initialize the new array
    $neighborhoods = array();

    // Convert the original array to the required format
    foreach ($neighborhoods_original['Zones'] as $value => $label) {
        $neighborhoods[] = array('value' => $value, 'label' => $label);
    }

    if ( function_exists( 'woocommerce_register_additional_checkout_field' ) ) {
        woocommerce_register_additional_checkout_field(
            array(
                'id'       => 'my_custom_namespace/billing-neighborhood',
                'label'    => 'Neighborhood',
                'location' => 'address',
                'type'     => 'select',
                'options'  => $neighborhoods,
            )
        );
    }
});






function is_woocommerce_checkout_block_active_globally() {
    // Ensure WooCommerce is active
    if ( ! class_exists( 'WooCommerce' ) ) {
        // error_log('WooCommerce is not active.');
        return false; // WooCommerce isn't active or hasn't loaded yet
    }

    // Get the ID of the WooCommerce checkout page
    $checkout_page_id = wc_get_page_id( 'checkout' );
    if ( ! $checkout_page_id ) {
        // error_log('No Checkout Page Found.');
        return false; // No checkout page found
    }

    // Get the content of the checkout page
    $checkout_page_content = get_post_field( 'post_content', $checkout_page_id );
    // error_log('Checkout Page Content: ' . $checkout_page_content);

    // First, check if the WooCommerce Checkout Block is present
    if ( has_block( 'woocommerce/checkout', $checkout_page_content ) ) {
        // error_log('WooCommerce Checkout Block is detected.');
        return true; // WooCommerce Checkout Block is in use
    }

    // Then, check if the WooCommerce Checkout Shortcode is present
    if ( strpos( $checkout_page_content, '[woocommerce_checkout]' ) !== false ) {
        // error_log('WooCommerce Checkout Shortcode is detected.');
        return false; // Classic WooCommerce Checkout is in use
    }

    // error_log('Neither Block nor Shortcode found.');
    return false; // Default to Classic WooCommerce Checkout if neither is found
}




add_action(
    'woocommerce_set_additional_field_value',
    function ( $key, $value, $group, $wc_object ) {
        // Check if the key matches your custom field key
        if ( 'my_custom_namespace/billing-neighborhood' !== $key ) {
            return;
        }

        // Determine the meta key based on the group (billing/shipping)
        if ( 'billing' === $group ) {
            $meta_key = 'billing_neighborhood'; // Meta key for the billing neighborhood
        } else {
            $meta_key = 'shipping_neighborhood'; // Adjust as necessary for shipping fields
        }

        // Ensure $wc_object is a valid WC_Order object
        if ( is_a( $wc_object, 'WC_Order' ) ) {
            // Save the value to the order meta
            $wc_object->update_meta_data( $meta_key, sanitize_text_field( $value ), true );
            $wc_object->save(); // Save the order to persist the meta data

            // Update the post meta directly as well
            update_post_meta( $wc_object->get_id(), $meta_key, sanitize_text_field( $value ) );
        } else {
            error_log('Error: $wc_object is not a valid WC_Order object.');
        }
    },
    10,
    4
);



