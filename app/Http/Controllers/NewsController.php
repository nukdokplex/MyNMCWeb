<?php


namespace App\Http\Controllers;


use Illuminate\View\View;
use PHPHtmlParser\Dom;
use PHPHtmlParser\Exceptions\ChildNotFoundException;
use PHPHtmlParser\Exceptions\CircularException;
use PHPHtmlParser\Exceptions\ContentLengthException;
use PHPHtmlParser\Exceptions\LogicalException;
use PHPHtmlParser\Exceptions\StrictException;
use Psr\Http\Client\ClientExceptionInterface;

class NewsController extends Controller
{
    function index($page = 1){
        $dom = new Dom();

        /*try {
            $dom->loadFromUrl("http://nmt.edu.ru");
        } catch (ChildNotFoundException $e) {
        } catch (CircularException $e) {
        } catch (ContentLengthException $e) {
        } catch (LogicalException $e) {
        } catch (StrictException $e) {
        } catch (ClientExceptionInterface $e) {
        }*/

        return view('news.index');
    }


}
