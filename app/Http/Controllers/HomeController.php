<?php


namespace App\Http\Controllers;

use GuzzleHttp\Exception\ClientException;
use Illuminate\Contracts\View\View;
use GuzzleHttp\Client;
use Mpdf\Mpdf;
use Illuminate\Http\Request;

/**
 * Class HomeController
 * @package App\Http\Controllers
 */
class HomeController extends Controller
{
    /**
     * @return View
     */
    public function index() {
        return view('home.index');
    }

    /**
     * @param Request $request
     * @throws \Exception
     */
    public function doGeneratePdf(Request $request) {
        $this->validate($request, [
            'url' => 'required|url'
        ]);

        $url = $request->get('url');
        $client = new Client([
            'timeout'  => 2.0,
        ]);

        try {
            $responseText = $client->get($url)->getBody()->getContents();
            if (!($xml = simplexml_load_string($responseText)))
                throw new \Exception('An error occurred while parsing RSS data.');

            $mpdf = new Mpdf();

            // Create title page if there needs to be one
            if (!empty($xml->channel->title)) {
                $titlePageContent = '<h1>' . $xml->channel->title . '</h1>';

                if (!empty($xml->channel->description)) {
                    $titlePageContent .= '<h4>' . $xml->channel->description . '</h4>';
                }

                $mpdf->WriteHTML($titlePageContent);
                $mpdf->AddPage();
            }

            // Add subsequent pages of content
            $items = $xml->channel->item ?? [];
            $numOfItems = count($items);

            for ($i = 0; $i < $numOfItems; $i++) {
                $item = $items[$i];

                $itemPageContent = '<h2>' . ($item->title ?? 'Untitled') . '</h2>';
                $media = $item->children('media', true);

                // Add image
                if ($media->count() > 0 && $media->content->count() > 0) {
                    $contentAttr = $media->content->attributes();
                    $itemPageContent .= '<div style="margin-top: 5px;"><img src="' . $contentAttr['url'] . '" style="max-width: 250px; max-height: 250px;/"></div>';
                }

                // Add Description
                if (!empty($item->description))
                    $itemPageContent .= '<p>' . $item->description . '</p>';

                // Add link to article
                if (!empty($item->link))
                    $itemPageContent .= '<a href="' . $item->link . '">' . $item->link . '</a>';

                $mpdf->WriteHTML($itemPageContent);

                if ($i < ($numOfItems - 1))
                    $mpdf->AddPage();
            }

            return \response()->stream(function () use($mpdf) {
                $mpdf->Output();
            });

        } catch (ClientException $ex) {
            return \response()->redirectToRoute('get.home.index')
                ->withErrors(['url' => $ex->getMessage()])
                ->withInput($request->input());
        }
    }
}
