@extends('layouts.app', ['title' => "Новости колледжа", 'tab' => 'news'])
@section('content')
    @include('layouts.headers.empty')
    <div class="container-fluid mt--7">
        <div class="row">
            <div class="col">
                @foreach($news as $news_element)
                    <div class="card mb-3">
                        <div class="card-header border-0">
                            <a href="{{$news_element["uri"]}}"><h3 class="mb-0">{!! $news_element["title"] !!}</h3></a>
                        </div>
                        <div class="card-body">
                            <p class="text-black-50">
                                {!! $news_element["body"] !!}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="row mt-5">
            <div class="col">
                <div class="card d-flex justify-content-center">
                    <nav aria-label="pagination">
                        <ul class="pagination">
                            @if($page > 1)
                                <li class="page-item">
                                    <a class="page-link" href="{!! route("news.page", ['page' => $page-1]) !!}" tabindex="-1">
                                        <i class="fa fa-angle-left"></i>
                                        <span class="sr-only">Назад</span>
                                    </a>
                                </li>
                                @if($page - 2 > 1)
                                    <li class="page-item"><a class="page-link" href="{!! route("news.page", ['page' => 1]) !!}">1</a></li>
                                @endif
                                @if($page - 1 > 1)
                                    <li class="page-item"><a class="page-link" href="{!! route("news.page", ['page' => $page - 2]) !!}">{{$page-2}}</a></li>
                                @endif
                                <li class="page-item"><a class="page-link" href="{!! route("news.page", ['page' => $page-1]) !!}">{{$page-1}}</a></li>
                            @endif
                            <li class="page-item active">
                                <a class="page-link" href="{!! route("news.page", ['page' => $page]) !!}">{!! $page !!} <span class="sr-only">(current)</span></a>
                            </li>
                            @if($page < $max_page)
                                <li class="page-item"><a class="page-link" href="{!! route("news.page", ['page' => $page+1]) !!}">{!! $page+1 !!}</a></li>
                                @if($page + 1 < $max_page)
                                        <li class="page-item"><a class="page-link" href="{!! route("news.page", ['page' => $page+2]) !!}">{!! $page+2 !!}</a></li>
                                @endif
                                @if($page + 2 < $max_page)
                                    <li class="page-item"><a class="page-link" href="{!! route("news.page", ['page' => $max_page]) !!}">{!! $max_page !!}</a></li>
                                @endif
                                <li class="page-item">
                                    <a class="page-link" href="{!! route("news.page", ['page' => $page+1]) !!}">
                                        <i class="fa fa-angle-right"></i>
                                        <span class="sr-only">Дальше</span>
                                    </a>
                                </li>
                            @endif

                        </ul>
                    </nav>
                </div>
            </div>
        </div>

    </div>
@endsection
