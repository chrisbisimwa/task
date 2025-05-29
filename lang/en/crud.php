<?php

return [
    'common' => [
        'actions' => 'Actions',
        'create' => 'Create',
        'edit' => 'Edit',
        'update' => 'Update',
        'new' => 'New',
        'cancel' => 'Cancel',
        'attach' => 'Attach',
        'detach' => 'Detach',
        'save' => 'Save',
        'delete' => 'Delete',
        'delete_selected' => 'Delete selected',
        'search' => 'Search...',
        'back' => 'Back to Index',
        'are_you_sure' => 'Are you sure?',
        'no_items_found' => 'No items found',
        'created' => 'Successfully created',
        'saved' => 'Saved successfully',
        'removed' => 'Successfully removed',
    ],

    'employees' => [
        'name' => 'Employees',
        'index_title' => 'Employees List',
        'new_title' => 'New Employee',
        'create_title' => 'Create Employee',
        'edit_title' => 'Edit Employee',
        'show_title' => 'Show Employee',
        'inputs' => [
            'name' => 'Name',
            'phone' => 'Phone',
            'email' => 'Email',
        ],
    ],

    'tasks' => [
        'name' => 'Tasks',
        'index_title' => 'Tasks List',
        'new_title' => 'New Task',
        'create_title' => 'Create Task',
        'edit_title' => 'Edit Task',
        'show_title' => 'Show Task',
        'inputs' => [
            'employee_id' => 'Employee',
            'name' => 'Name',
            'description' => 'Description',
            'status' => 'Status',
            'due_week' => 'Due Week',
        ],
    ],

    'users' => [
        'name' => 'Users',
        'index_title' => 'Users List',
        'new_title' => 'New User',
        'create_title' => 'Create User',
        'edit_title' => 'Edit User',
        'show_title' => 'Show User',
        'inputs' => [
            'name' => 'Name',
            'email' => 'Email',
            'password' => 'Password',
        ],
    ],

    'access_tokens' => [
        'name' => 'Access Tokens',
        'index_title' => 'AccessTokens List',
        'new_title' => 'New Access token',
        'create_title' => 'Create AccessToken',
        'edit_title' => 'Edit AccessToken',
        'show_title' => 'Show AccessToken',
        'inputs' => [
            'employee_id' => 'Employee',
            'token' => 'Token',
            'expires_at' => 'Expires At',
        ],
    ],
];
