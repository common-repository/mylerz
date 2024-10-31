<?php

if (!class_exists('Mylerz_Shipping_Method')) {

    class Mylerz_Shipping_Method extends WC_Shipping_Method
    {
        public function __construct()
        {
            $this->id = 'mylerz';
            $this->method_title = esc_html__('Mylerz Settings', 'mylerz');
            $this->method_description = esc_html__('Shipping Method for Mylerz', 'mylerz');
            $this->init();
            $this->enabled = isset($this->settings['enabled']) ? $this->settings['enabled'] : 'yes';
            $this->title = esc_html(isset($this->settings['title'])) ? esc_html($this->settings['title']) : esc_html__('Mylerz Shipping', 'mylerz');
            $this->service_type = $this->settings['service_type'];
            // include_once __DIR__ . '../../core/class-mylerz-helper.php';
        }


        public function init()
        {
            // Load the settings API
            $this->init_form_fields();
            $this->init_settings();
            // Save settings in admin if you have any defined
            add_action('woocommerce_update_options_shipping_' . $this->id, array($this, 'process_admin_options'));
        }

        public function init_form_fields()
        {
            $this->form_fields = include('data-mylerz-settings.php');
        }
    }

}


?>
