<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use HeadlessChromium\BrowserFactory;
use HeadlessChromium\Page;
use Illuminate\Http\Request;

class ScreenShotController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $request->validate([
            'page' => ['required', 'url'],
            'sd' => ['numeric', 'nullable'],
            'cid' => ['string', 'nullable', 'regex:/^[a-zA-Z0-9_\-]+$/'],
        ]);

        $appHost = parse_url(config('app.url'), PHP_URL_HOST);
        $targetHost = parse_url($request->page, PHP_URL_HOST);

        if ($appHost && $targetHost !== $appHost && $targetHost !== 'localhost' && $targetHost !== '127.0.0.1') {
            abort(403, 'Screenshots can only be taken for application pages.');
        }

        if (config('app.chromium_executable') == 'default') {
            $browserFactory = new BrowserFactory();
        } else {
            $browserFactory = new BrowserFactory(config('app.chromium_executable'));
        }

        // starts headless chrome
        $browser = $browserFactory->createBrowser([
            'headers'   => ['cookie' => $request->headers->get('cookie')],
            'enableImages' => true,
            'windowSize' => [1280, 1024],
        ]);

        try {
            // creates a new page and navigate to an URL
            $page = $browser->createPage();
            $page->navigate($request->page)->waitForNavigation(Page::NETWORK_IDLE);

            // scroll down
            if ($request->filled('sd')) {
                $page->mouse()->scrollDown((int) $request->sd);
            }

            // find and click at an element with given id
            if ($request->filled('cid')) {
                $page->mouse()->find('#' . $request->cid, 1)->click();
            }

            // screenshot
            $path = storage_path('app/screenshots/');
            if (! file_exists($path)) {
                mkdir($path, 0755, true);
            }

            $file_name = 'screenshot' . Carbon::now()->timestamp . '.png';
            $img = $path . $file_name;
            $page->screenshot()->saveToFile($img);

            return response()->file($img);
        } finally {
            // bye
            $browser->close();
        }
    }
}
