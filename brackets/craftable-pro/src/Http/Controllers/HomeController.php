<?php

namespace Brackets\CraftablePro\Http\Controllers;

use App\Settings\GeneralSettings;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    /**
     * Display the login view.
     */
    public function index()
    {
        return redirect(app(GeneralSettings::class)->default_route);
    }

    public function dashboard(): Response
    {
        return Inertia::render('Home');
    }
}
