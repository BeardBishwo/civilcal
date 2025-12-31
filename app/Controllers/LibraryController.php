<?php

namespace App\Controllers;

use App\Services\Auth;
use App\Models\User;

class LibraryController extends Controller
{
    public function index()
    {
        $this->view('library/index', [
            'title' => 'Blueprint Vault - Civil City Library',
            'user' => Auth::user(),
            'coins' => (new User())->getCoins(Auth::id())
        ]);
    }

    public function upload()
    {
        $this->view('library/upload', [
            'title' => 'Upload Resource - Blueprint Vault',
            'user' => Auth::user()
        ]);
    }

    public function adminRequests()
    {
        // Admin check is done via middleware, but double check doesn't hurt or relies on middleware
        // Using view 'library/requests' in admin theme
        // The 'view' method in base Controller usually handles themes. 
        // Need to check Base Controller implementation.
        // Assuming Admin Controller or separate Admin namespace? 
        // Let's look at `Admin\DashboardController` pattern.
        
        // Actually, for Admin views, it likely uses a different layout or method.
        // Let's create `App\Controllers\Admin\LibraryController` instead for the admin part?
        // Existing Admin controllers are in `App\Controllers\Admin`.
        
        // I'll stick to Public parts here.
        // Admin parts should go to `App\Controllers\Admin\LibraryController`.
    }
}
