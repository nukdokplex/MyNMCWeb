<nav class="navbar navbar-vertical fixed-left navbar-expand-md navbar-light bg-white" id="sidenav-main">
    <div class="container-fluid">
        <!-- Toggler -->
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#sidenav-collapse-main" aria-controls="sidenav-main" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <!-- Brand -->
        <a class="navbar-brand pt-0" href="{{ route('home') }}">
            <img src="{{ asset('assets/img/brand/blue.png') }}" class="navbar-brand-img" alt="...">
        </a>
        <!-- User -->
        <ul class="nav align-items-center d-md-none">
            <li class="nav-item dropdown">
                <a class="nav-link" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <div class="media align-items-center">
                        <span class="avatar avatar-sm rounded-circle">
                            @auth()
                                <img alt="Фото пользователя" src="{!! Gravatar::get(auth()->user()->email) !!}">
                            @endauth
                            @guest()
                                <img alt="Фото пользователя" src="https://www.gravatar.com/avatar/?d=mp">
                            @endguest
                        </span>
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-arrow dropdown-menu-right">
                    <div class="dropdown-header noti-title">
                        <h6 class="text-overflow m-0">Меню</h6>
                    </div>
                    @auth()
                      <!--  <a href="{{ route('profile.edit') }}" class="dropdown-item">
                            <i style="font-size: .800rem !Important;" class="mdi mdi-18px mdi-account"></i>
                            <span>Мой профиль</span>
                        </a>-->

                        <div class="dropdown-divider"></div>
                        <a href="{{ route('logout') }}" class="dropdown-item" onclick="event.preventDefault();
                            document.getElementById('logout-form').submit();">
                            <i class="mdi mdi-18px mdi-logout-variant"></i>
                            <span>Выйти</span>
                        </a>
                    @endauth
                    @guest()
                        <a href="{{ route('login') }}" class="dropdown-item">
                            <i class="mdi mdi-login-variant"></i>
                            <span>Войти</span>
                        </a>
                    @endguest

                </div>
            </li>
        </ul>
        <!-- Collapse -->
        <div class="collapse navbar-collapse" id="sidenav-collapse-main">
            <!-- Collapse header -->
            <div class="navbar-collapse-header d-md-none">
                <div class="row">
                    <div class="col-6 collapse-brand">
                        <a href="{{ route('home') }}">
                            <img alt="Мой НМК" src="{{ asset('assets/img/brand/blue.png') }}">
                        </a>
                    </div>
                    <div class="col-6 collapse-close">
                        <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#sidenav-collapse-main" aria-controls="sidenav-main" aria-expanded="false" aria-label="Открыть/Закрыть">
                            <span></span>
                            <span></span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link{{$active_tab == 'home' ? ' active' : ''}}" href="{{ route('home') }}">
                        <i class="mdi mdi-18px mdi-home"></i> Главная
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link{{$active_tab == 'posts' ? ' active' : ''}}" href="{{route('posts')}}">
                        <i class="mdi mdi-18px mdi-post"></i> Новости
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link{{$active_tab == 'news' ? ' active' : ''}}" href="{{ route('news') }}">
                        <i class="mdi mdi-18px mdi-newspaper"></i> Новости колледжа
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link{{$active_tab == 'schedule' ? ' active' : ''}}" href="{{ route('schedule') }}">
                        <i class="mdi mdi-18px mdi-clock-outline"></i> Расписание
                    </a>
                </li>
            </ul>

                <!-- Divider -->
                <hr class="my-3">
                <!-- Heading -->
                <h6 class="navbar-heading text-muted">Управление</h6>
                <!-- Navigation -->
                <ul class="navbar-nav mb-md-3">
                    @can('manage groups')
                        <li class="nav-item">
                            <a class="nav-link{{$active_tab == 'groups' ? ' active' : ''}}" href="{{route('groups')}}">
                                <i class="mdi mdi-18px mdi-account-group"></i> Группы
                            </a>
                        </li>
                    @endcan
                    @can('manage specializations')
                        <li class="nav-item">
                            <a class="nav-link{{$active_tab == 'specializations' ? ' active' : ''}}" href="{{route('specializations')}}">
                                <i class="mdi mdi-18px mdi-toolbox"></i> Специальности
                            </a>
                        </li>
                    @endcan
                    @can('manage subjects')
                        <li class="nav-item">
                            <a class="nav-link{{$active_tab == 'subjects' ? ' active' : ''}}" href="{{route('subjects')}}">
                                <i class="mdi mdi-18px mdi-book-open-variant"></i> Предметы
                            </a>
                        </li>
                    @endcan
                    @can('manage auditories')
                        <li class="nav-item">
                            <a class="nav-link{{$active_tab == 'auditories' ? ' active' : ''}}" href="{{route('auditories')}}">
                                <i class="mdi mdi-18px mdi-google-classroom"></i> Аудитории
                            </a>
                        </li>
                    @endcan
                    @can('manage users')
                        <li class="nav-item">
                            <a class="nav-link{{$active_tab == 'users' ? ' active' : ''}}" href="{{route('users')}}">
                                <i class="mdi mdi-18px mdi-account-multiple"></i> Пользователи
                            </a>
                        </li>
                    @endcan
                    @can('manage schedule')
                        <li class="nav-item">
                            <a class="nav-link" href="#schedule-sidebar-item" data-toggle="collapse" role="button" aria-expanded="true" aria-controls="navbar-examples">
                                <i class="mdi mdi-18px mdi-table-clock"></i>
                                <span class="nav-link-text">Расписание</span>
                            </a>
                            <div class="collapse{{in_array($active_tab, ['schedule_edit', 'primary_schedule_edit', 'rings_schedule_edit']) ? ' show' : ''}}" id="schedule-sidebar-item">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a class="nav-link{{$active_tab == 'schedule_edit' ? ' active' : ''}}" href="{{route('schedule.edit')}}">
                                            Текущее расписание
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link{{$active_tab == 'primary_schedule_edit' ? ' active' : ''}}" href="{{route('schedule.edit.primary.groups')}}">
                                            Основное расписание
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link{{$active_tab == 'rings_schedule_edit' ? ' active' : ''}}" href="{{route('schedule.edit.rings')}}">
                                            Расписание звонков
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                    @endcan
                </ul>
        </div>
    </div>
</nav>
