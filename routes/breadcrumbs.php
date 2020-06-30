<?php

Breadcrumbs::for('home', function ($trail) {
    $trail->push('Home', route('home'));
});

//Breadcrumbs for http://localhost/my-project/academic/usersadmin
// Home > List Users 
Breadcrumbs::for('list-user', function ($trail) {
    $trail->parent('home');
    $trail->push('List Users', route('users.index'));
});
// Home > List Users > Trash
Breadcrumbs::for('list-admin-trash', function ($trail) {
    $trail->parent('list-user');
    $trail->push('Trash', route('users.trash'));
}); 
// Home > List User > Create
Breadcrumbs::for('list-user-create', function ($trail) {
    $trail->parent('list-user');
    $trail->push('Create User', route('users.create'));
}); 
// Home > List Admin > {id}
Breadcrumbs::register('show', function ($breadcrumbs, $listAdmin, $user) {
    $breadcrumbs->parent('list-user', $listAdmin);
    $breadcrumbs->push($user->id, route('users.show', ['id' => $user->id]));
});
// Home > List Admin > {id} > Edit
Breadcrumbs::register('edit', function ($breadcrumbs, $listAdmin, $user) {
    $breadcrumbs->parent('list-user', $listAdmin);
    $breadcrumbs->push('Edit', route('users.edit', ['id' => $user->id]));
});
// Home > List Admin > Active
Breadcrumbs::for('list-active-admin', function ($trail) {
    $trail->parent('list-user');
    $trail->push('Active Users', route('users.active'));
}); 
// Home > List Admin > Inactive
Breadcrumbs::for('list-inactive-admin', function ($trail) {
    $trail->parent('list-user');
    $trail->push('Inactive Users', route('users.inactive'));
}); 
// Home > Profile
Breadcrumbs::for('profile', function ($breadcrumbs, $home, $edit) {
    $breadcrumbs->parent('home', $home);
    $breadcrumbs->push('User Profile', route('profile.edit', ['id' => $edit->id]));
});
// Home > Role Management 
Breadcrumbs::for('list-roles', function ($trail) {
    $trail->parent('home');
    $trail->push('Role Management', route('roles.index'));
});
// Home > Role Management > Edit Roles
Breadcrumbs::for('list-role-edit', function ($trail) {
    $trail->parent('list-roles');
    $trail->push('Edit Roles', route('roles.index'));
}); 
// Home > Role Management > Role Detail
Breadcrumbs::for('list-role-detail', function ($trail) {
    $trail->parent('list-roles');
    $trail->push('Role Detail', route('roles.index'));
}); 
// Home > Role Management > Create Role
Breadcrumbs::for('list-role-create', function ($trail) { 
    $trail->parent('list-roles');
    $trail->push('Create Role', route('roles.index'));
}); 
// Home > Role Management 
Breadcrumbs::for('list-profile', function ($trail) {
    $trail->parent('home');
    $trail->push('User Profile', route('roles.index'));
}); 
// Home > List of Applicants 
Breadcrumbs::for('list-applicants', function ($trail) {
    $trail->parent('home');
    $trail->push('List of Applicants', route('student-registrars.index'));
});
// Home > List of Applicants > Pending
Breadcrumbs::for('list-applicants-pending', function ($trail) {
    $trail->parent('list-applicants');
    $trail->push('Pending Applicants', route('student-registrars.pending'));
}); 
// Home > List of Applicants > Approved Applicants
Breadcrumbs::for('list-applicants-approved', function ($trail) {
    $trail->parent('list-applicants');
    $trail->push('Approved Applicants', route('student-registrars.pending'));
}); 
// Home > List of Applicants > Approved Applicants
Breadcrumbs::for('list-applicants-rejected', function ($trail) {
    $trail->parent('list-applicants');
    $trail->push('Rejected Applicants', route('student-registrars.pending'));
}); 
// Home > List of Applicants > Trash
Breadcrumbs::for('list-applicants-trash', function ($trail) {
    $trail->parent('list-applicants');
    $trail->push('Trash', route('student-registrars.pending'));
}); 
// Home > List Admin > {id}
Breadcrumbs::register('list-applicants-detail', function ($breadcrumbs, $listAdmin, $user) {
    $breadcrumbs->parent('list-applicants', $listAdmin);
    $breadcrumbs->push($user->id, route('users.show', ['id' => $user->id]));
});