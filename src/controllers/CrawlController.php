<?php

/**
 * Created by PhpStorm.
 * User: fabs
 * Date: 23.11.17
 * Time: 05:31
 */

namespace Brotzka\PhpCrawler\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Brotzka\PhpCrawler\Models\UncheckedUrl;
use Brotzka\PhpCrawler\Jobs\UrlCheckerJob;
use Brotzka\PhpCrawler\Models\Url;

use Brotzka\PhpCrawler\Tools\UrlHtmlMetaGrabberTool as MetaGrabber;

class CrawlController extends Controller {

    public function index(Request $request) {

        // Test
        
        // Test

        $response = ['test' => 'Testeintrag'];

        if ($request->isMethod('post')) {

            $response['post']['url'] = $request->input('url');

            $valid = $this->validate($request, [
                'url' => 'url'
            ]);

            $uncheckedUrl = new UncheckedUrl();
            $uncheckedUrl->url = $request->input('url');
            $uncheckedUrl->save();
            UrlCheckerJob::dispatch($uncheckedUrl);
            return back();
        } else {
            $response['get']['status'] = 'Seite wurde per GET aufgerufen';
        }

        return view('php-crawler::crawler-index', ['response' => $response]);
    }

    public function runUrlChecker() {
        UrlCheckerJob::dispatch();
        return redirect(route('crawler.index'));
    }

    public function test1(Request $request) {
        $response = '';
        if ($request->isMethod('post')) {

            $valid = $request->validate([
                'url' => 'url'
                    ]);
            $url = new Url();
            $url->url = $request->input('url');
            $url->save();

            $grabber = new UrlGrabber($url);
            $response = $grabber->getStatusCode();

            //GrabUrl::dispatch($url);
        }

        return view('php-crawler::index', ['response' => $response]);
    }

    public function test2() {
        echo "<h1>Test 2</h1>";


        for ($i = 0; $i <= 10; $i++) {
            TestJob::dispatch();
            echo "Job $i dispatched!<br>";
        }
    }

}
