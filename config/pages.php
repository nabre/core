<?php

return [
    //Pages default
    'login' => ['i' => 'fa-solid fa-right-to-bracket'],
    'logout' => ['i' => 'fa-solid fa-right-from-bracket'],
    'password_confirm' => ['i' => 'fa-solid fa-clipboard-check'],
    'password_request' => ['i' => 'fa-solid fa-key'],
    "register" => ['i' => 'fa-regular fa-id-card'],
    'verification_notice' => ['i' => 'fa-solid fa-envelope-circle-check'],
    'welcome' => ['i' => 'fa-solid fa-house'],

    //Pages default Nabre
    'nabre_admin_settings_index' => ['i' => 'fa-solid fa-sliders'],
    'nabre_admin_users_impersonate_index' => ['i' => 'fa-solid fa-user-astronaut'],
    'nabre_admin_users_list_index' => ['i' => 'fa-solid fa-user-pen', 't' => ['it' => 'Elenco utenti']],
    'nabre_admin_users_permission_index' => ['i' => 'fa-solid fa-user-group'],
    'nabre_admin_users_role_index' => ['i' => 'fa-solid fa-user-lock'],
    'nabre_builder_collections_fields_index' => ['i' => 'fa-solid fa-table-list'],
    'nabre_builder_collections_relations_index' => ['i' => 'fa-solid fa-diagram-successor'],
    'nabre_builder_database_index' => ['i' => 'fa-solid fa-database'],
    'nabre_builder_navigation_menu_auto_index' => ['i' => 'fa-solid fa-wand-magic'],
    'nabre_builder_navigation_menu_custom_index' => ['i' => 'fa-solid fa-sliders'],
    'nabre_builder_navigation_pages_index' => ['i' => 'fa-solid fa-file', 't' => ['it' => 'Pagine']],
    'nabre_builder_settings_form-field-type_index' => ['i' => 'fa-solid fa-hashtag'],
    'nabre_builder_settings_variables_index' => ['i' => 'fa-solid fa-tags'],
    'nabre_manage_contact_index' => ['i' => 'fa-solid fa-user-pen'],
    'nabre_manage_dashboard_index' => ['i' => 'fa-solid fa-gauge'],
    'nabre_user_account_index' => ['i' => 'fa-solid fa-key'],
    'nabre_user_contact_index' => ['i' => 'fa-solid fa-id-badge'],
    'nabre_user_dashboard_index' => ['i' => 'fa-solid fa-gauge'],
    'nabre_user_settings_index' => ['i' => 'fa-solid fa-sliders'],

    //Folder
    "admin" => ['i' => 'fa-solid fa-gears', 'd' => 'nabre_admin_settings_index'],
    'admin_builder' =>  ['i' => 'fa-solid fa-building-columns', 'd' => 'nabre_builder_database_index', 't' => ['it' => 'Costruttore']],
    'admin_builder_collections' =>  ['i' => 'fa-solid fa-table', 'd' => 'nabre_builder_collections_fields_index', 't' => ['it' => 'Collections']],
    'admin_builder_navigation' => ['i' => 'fa-regular fa-compass'],
    'admin_builder_navigation_menu' => ['i' => 'fa-brands fa-elementor', 'd' => 'nabre_builder_navigation_menu_auto_index'],
    'admin_builder_settings' => ['i' => 'fa-solid fa-sliders', 'd' => 'nabre_builder_settings_variables_index'],
    'admin_users' => ['i' => 'fa-solid fa-users-gear', 'd' => 'nabre_admin_users_list_index'],
    'manage' => ['i' => 'fa-solid fa-screwdriver-wrench', 'd' => 'nabre_manage_dashboard_index'],
    'user' => ['i' => 'fa-solid fa-circle-user', 'd' => 'nabre_user_dashboard_index'],
];
