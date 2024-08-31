<?php

FF_PLUGINS_VITE->enqueue('settings_page', 'src/settings-page/settings-page.js');

$this->vite->enqueue('admin', 'src/admin/admin.js');

echo '<h2>'. $this->plugin_name .' Settings</h2>';

$tabs = [
    'general' => 'General',
];

if( function_exists('ff_admin_tabs') ) {
    ff_admin_tabs( $tabs, __DIR__, $this->plugin_slug );
}
else {
    pre_debug('Update fivebyfive plugin to latest');
}