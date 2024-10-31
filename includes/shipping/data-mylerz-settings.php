<?php

return array(
    'enabled' => array(
        'title' => esc_html__('Enable', 'mylerz'),
        'type' => 'checkbox',
        'description' => esc_html__('Enable Mylerz shipping', 'mylerz'),
        'default' => 'yes'
    ),
    'merchant_country' => array(
        'title' => esc_html__('Select Your Country', 'mylerz'),
        'type' => 'select',
        'description' => esc_html__('Choose your current country.', 'mylerz'),
        'options' => array(
            'EG' => 'Egypt',
            'TN' => 'Tunisia',
            'MA' => 'Morocco',
            'DZ' => 'Algeria',
        ),
        'default' => 'EG'
    ),
    'title' => array(
        'title' => esc_html__('Title', 'mylerz'),
        'type' => 'text',
        'description' => esc_html__('Title to be display on site', 'mylerz'),
        'default' => esc_html__('Mylerz Shipping', 'mylerz')
    ),

    'user_name' => array(
        'title' => esc_html__('* User Name', 'mylerz'),
        'type' => 'text',
        'id' => 'user_name'
    ),
    'password' => array(
        'title' => esc_html__('* Password', 'mylerz'),
        'type' => 'password',
        'id' => 'pass'
    ),
    'bulk_return' => array(
        'title' => esc_html__('Enable Bulk Return Button', 'mylerz'),
        'type' => 'checkbox',
        'description' => esc_html__('Enable bulk return for refunded items', 'mylerz'),
        'default' => 'no'
    ),
    'service_type' => array(
        'title' => esc_html__('Service Type', 'mylerz'),
        'type' => 'select',
        'description' => esc_html__('Choose one of the service types you use.', 'mylerz'),
        'options' => array(
            'DTD' => 'Door to door',
            'DTC' => 'Door to counter',
            'CTD' => 'Counter to door',
            'CTC' => 'Counter to counter',
        ),
        'default' => 'DTD'
    ),
);
