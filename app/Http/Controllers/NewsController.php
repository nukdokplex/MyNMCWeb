<?php


namespace App\Http\Controllers;


use ErrorException;
use PHPHtmlParser\Dom;
use PHPHtmlParser\Exceptions\EmptyCollectionException;

class NewsController extends Controller
{
    /**
     * @param $str : string
     * @return array
     */
    function getCurrentAndLastPage($str): array
    {
        $s = trim($str);
        return [
            intval(explode(" ", $s, 2)[1]),
            intval(explode(" ", $s, 4)[3])
        ];
    }

    function index($page = 1){
        try {
            $ch = curl_init();

            $request_url = "http://nmt.edu.ru/index.php" . ($page > 1 ? "?limitstart=" . (($page - 1) * 12) : "");

            curl_setopt($ch, CURLOPT_URL, $request_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)");
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);

            $out = curl_exec($ch);
            curl_close($ch);

            $dom = new Dom();
            $dom->loadStr($out);

            $news = [];
            $news_elements = $dom->find(".catItemView.groupLeading");
            $server_page_element = $dom->find(".k2PaginationCounter")[0];
            $pages = $this->getCurrentAndLastPage($server_page_element->text);
            foreach ($news_elements as $ne) {
                $news_data = [];
                $news_data['date'] = $ne->find(".catItemHeader > span.catItemDateCreated")[0]->text;
                $news_link = $ne->find(".catItemHeader > h3.catItemTitle > a")[0];
                $news_data['title'] = $news_link->text;
                $news_data['uri'] = "http://nmt.edu.ru" . $news_link->href;
                $news_data['body'] = $ne->find("div.catItemBody > div.catItemIntroText")[0]->text;
                array_push($news, $news_data);
            }


            return view("news.index", ['news' => $news, 'page' => $pages[0], 'max_page' => $pages[1]]);
        }
        catch (EmptyCollectionException | ErrorException $e){
            return view("news.index", ['news' => [['date' => null, 'title' => "Произошла ошибка при попытке загрузить новости. Очень вероятно, что ресурс nmt.edu.ru недоступен.", 'uri' => "#", 'body' => $e->getMessage() . "<br>" . $e->getTraceAsString()]], 'page' => 0, 'max_page' => 0]);
        }
    }
}
