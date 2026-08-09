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
            'page' => 'required',
            'sd' => 'numeric|nullable',
        ]);

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
                $page->mouse()->scrollDown($request->sd);
            }

            // find and click at an element with given id
            if ($request->filled('cid')) {
                $selector = '#' . $request->cid;
                if (\version_compare(\Composer\InstalledVersions::getVersion('chrome-php/chrome'), '1.14.0', '<')) {
                    // workaround https://github.com/chrome-php/chrome/security/advisories/GHSA-3432-fmrf-7vmh
                    $selector = \json_encode($selector, \JSON_THROW_ON_ERROR);
                }
                $page->mouse()->find($selector, 1)->click();
            }

            // screenshot
            $path = storage_path('app/screenshots/');
            $file_name = 'screenshot' . Carbon::now()->timestamp . '.png';
            $img = $path . $file_name;
            $page->screenshot()->saveToFile($img);
        } finally {
            // bye
            $browser->close();
        }
    }
}
