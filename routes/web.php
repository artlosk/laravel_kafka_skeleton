<?php

use App\Http\Controllers\Backend\AdminController;
use App\Http\Controllers\Backend\PostController as BackendPostController;
use App\Http\Controllers\Backend\PostNotificationSettingController;
use App\Http\Controllers\Backend\RolePermissionController;
use App\Http\Controllers\Backend\UserController;
use App\Http\Controllers\Frontend\DashboardController;
use App\Http\Controllers\Frontend\PostController as FrontendPostController;
use App\Http\Controllers\Frontend\ProfileController as FrontendProfileController;
use App\Http\Controllers\Backend\ProfileController as BackendProfileController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::get('/', function () {
    return view('frontend.welcome');
});

Auth::routes();

Route::get('/posts', [FrontendPostController::class, 'index'])->name('frontend.posts.index');
Route::get('/posts/{post}', [FrontendPostController::class, 'show'])->name('frontend.posts.show');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('frontend.dashboard');
    Route::match(['get', 'post'], '/profile', [FrontendProfileController::class, 'update'])->name('frontend.profile');
    Route::match(['get', 'post'], '/password', [FrontendProfileController::class, 'changePassword'])->name('frontend.password');

    // API Token management for profile
    Route::post('/profile/generate-token', [FrontendProfileController::class, 'generateApiToken'])->name('frontend.profile.generate-token');
    Route::delete('/profile/revoke-token', [FrontendProfileController::class, 'revokeApiToken'])->name('frontend.profile.revoke-token');
});

Route::middleware(['auth', 'permission:access-admin-panel'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('backend.dashboard');
    Route::match(['get', 'post'], '/profile', [BackendProfileController::class, 'update'])->name('backend.profile');
    Route::match(['get', 'post'], '/password', [BackendProfileController::class, 'changePassword'])->name('backend.password');

    // API Token management for admin profile
    Route::post('/profile/generate-token', [BackendProfileController::class, 'generateApiToken'])->name('backend.profile.generate-token');
    Route::delete('/profile/revoke-token', [BackendProfileController::class, 'revokeApiToken'])->name('backend.profile.revoke-token');

    Route::prefix('posts')->name('backend.posts.')->group(function () {

        Route::get('/', [BackendPostController::class, 'index'])
            ->middleware('permission:read-posts')
            ->name('index');

        Route::get('/create', [BackendPostController::class, 'create'])
            ->middleware('permission:create-posts')
            ->name('create');

        Route::post('/', [BackendPostController::class, 'store'])
            ->middleware('permission:create-posts')
            ->name('store');

        Route::get('/{post}', [BackendPostController::class, 'show'])
            ->middleware('permission:read-posts')
            ->name('show');

        Route::get('/{post}/edit', [BackendPostController::class, 'edit'])
            ->middleware('permission:edit-posts')
            ->name('edit');

        Route::put('/{post}', [BackendPostController::class, 'update'])
            ->middleware('permission:edit-posts')
            ->name('update');

        Route::delete('/{post}', [BackendPostController::class, 'delete'])
            ->middleware('permission:delete-posts')
            ->name('delete');
    });
    Route::middleware(['auth', 'permission:manage-roles|manage-permissions'])->group(function () {
        Route::get('/roles', [RolePermissionController::class, 'indexRoles'])->name('backend.roles.index');
        Route::get('/roles/create', [RolePermissionController::class, 'createRole'])->name('backend.roles.create');
        Route::post('/roles', [RolePermissionController::class, 'storeRole'])->name('backend.roles.store');
        Route::get('/roles/{role}', [RolePermissionController::class, 'showRole'])->name('backend.roles.show');
        Route::get('/roles/{role}/edit', [RolePermissionController::class, 'editRole'])->name('backend.roles.edit');
        Route::put('/roles/{role}', [RolePermissionController::class, 'updateRole'])->name('backend.roles.update');
        Route::delete('/roles/{role}', [RolePermissionController::class, 'destroyRole'])->name('backend.roles.destroy');

        Route::get('/permissions', [RolePermissionController::class, 'indexPermissions'])->name('backend.permissions.index');
        Route::get('/permissions/create', [RolePermissionController::class, 'createPermission'])->name('backend.permissions.create');
        Route::post('/permissions', [RolePermissionController::class, 'storePermission'])->name('backend.permissions.store');
        Route::get('/permissions/{permission}', [RolePermissionController::class, 'showPermission'])->name('backend.permissions.show');
        Route::get('/permissions/{permission}/edit', [RolePermissionController::class, 'editPermission'])->name('backend.permissions.edit');
        Route::put('/permissions/{permission}', [RolePermissionController::class, 'updatePermission'])->name('backend.permissions.update');
        Route::delete('/permissions/{permission}', [RolePermissionController::class, 'destroyPermission'])->name('backend.permissions.destroy');

        Route::get('/users/roles-permissions', [RolePermissionController::class, 'manageUserRolesPermissions'])->name('backend.roles.manage');
        Route::post('/users/roles-permissions', [RolePermissionController::class, 'updateUserRolesPermissions'])->name('backend.roles.update-user');
        Route::get('/users/{user}/roles-permissions', [RolePermissionController::class, 'getUserRolesPermissions'])->name('backend.roles.get-user');
    });

    Route::middleware(['auth', 'permission:manage-users'])->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('backend.users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('backend.users.create');
        Route::post('/users', [UserController::class, 'store'])->name('backend.users.store');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('backend.users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('backend.users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('backend.users.destroy');

        // API Token management
        Route::post('/users/{user}/generate-api-token', [UserController::class, 'generateApiToken'])->name('backend.users.generate-api-token');
        Route::delete('/users/{user}/revoke-api-token', [UserController::class, 'revokeApiToken'])->name('backend.users.revoke-api-token');
    });

    Route::view('/upload', 'backend.upload');

    Route::post('/upload-media', [BackendPostController::class, 'uploadMedia'])->name('admin.upload-media');
    Route::delete('/posts/{post}/remove-media/{media}', [BackendPostController::class, 'removeMedia'])->name('backend.posts.removeMedia');

    Route::post('/upload-image', [BackendPostController::class, 'uploadImage'])->name('upload.image');
    Route::get('/get-gallery-images', [BackendPostController::class, 'getGalleryImages'])->name('gallery.images');
    Route::post('/posts/{post}/attach-media/{mediaId}', [BackendPostController::class, 'attachMediaToPost'])->name('post.attach.media');

    Route::get('media', [App\Http\Controllers\Backend\MediaController::class, 'index'])->name('backend.media.index');
    Route::post('media/upload', [App\Http\Controllers\Backend\MediaController::class, 'upload'])->name('backend.media.upload');
    Route::get('media/get-by-ids', [App\Http\Controllers\Backend\MediaController::class, 'getByIds'])->name('backend.media.getByIds');
    Route::delete('media/{media}', [App\Http\Controllers\Backend\MediaController::class, 'deleteMedia'])->name('backend.media.delete');

    Route::post('filepond/upload', [App\Http\Controllers\Backend\MediaController::class, 'uploadFilepond'])->name('backend.filepond.upload');
    Route::delete('filepond/delete', [App\Http\Controllers\Backend\MediaController::class, 'deleteFilepond'])->name('backend.filepond.delete');

    Route::prefix('post-notification-settings')->name('backend.post-notification-settings.')->group(function () {
        Route::get('/', [PostNotificationSettingController::class, 'index'])->name('index');
        Route::get('/create', [PostNotificationSettingController::class, 'create'])->name('create');
        Route::post('/', [PostNotificationSettingController::class, 'store'])->name('store');
        Route::get('/{postNotificationSetting}/edit', [PostNotificationSettingController::class, 'edit'])->name('edit');
        Route::put('/{postNotificationSetting}', [PostNotificationSettingController::class, 'update'])->name('update');
        Route::delete('/{postNotificationSetting}', [PostNotificationSettingController::class, 'destroy'])->name('destroy');
        Route::patch('/{postNotificationSetting}/toggle-active', [PostNotificationSettingController::class, 'toggleActive'])->name('toggle-active');
    });
});
