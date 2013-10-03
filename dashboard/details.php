<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Module_dashboard extends Module {

    public $version = '1.0';

    public function info() {
        return array(
            'name' => array(
                'en' => 'Inquiry System'
            ),
            'description' => array(
                'en' => 'Save client inquiries, send quotations to suppliers, allow suppliers to quote, and keep track of confirmed and unconfired orders.'
            ),
            'frontend' => TRUE,
            'backend' => TRUE,
            'menu' => 'content',
            'roles'    => array(
                'view_module', 'add_inquiry'
            )
        );
    }

    public function install() {
        return TRUE;
    }

    public function uninstall() {
        return TRUE; //Not interested in uninstalling this for the time being.
    }

    public function upgrade($old_version) {
        // Your Upgrade Logic
        return TRUE;
    }

    public function help() {
        // Return a string containing help info
        // You could include a file and return it here.
        return "<h4>Overview</h4>
    <p>This system allows Snow International Trading Ltd staff to save client inquiries, send quotations to suppliers based the inquiries received, allow suppliers to quote, and keep track of confirmed and unconfired orders.</p>";
    }

}

/* End of file details.php */