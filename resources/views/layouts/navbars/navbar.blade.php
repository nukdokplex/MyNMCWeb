<!-- Top navbar -->
<nav class="navbar navbar-top navbar-expand-md navbar-dark" id="navbar-main">
    <div class="container-fluid">

        <a class="h4 mb-0 text-white text-uppercase d-none d-lg-inline-block" href="{{ route('home') }}">Платформа «Мой НМК»</a>


        <ul class="navbar-nav align-items-center d-none d-md-flex">
            <li class="nav-item dropdown">
                @auth()
                    <a class="nav-link pr-0" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <div class="media align-items-center">
                            <span class="avatar avatar-sm rounded-circle">
                                <img alt="Фото пользователя" src="{{ Gravatar::get(auth()->user()->email)}}">
                            </span>
                            <div class="media-body ml-2 d-lg-block">
                                <span class="mb-0 text-sm  font-weight-bold">{{ auth()->user()->name }}</span>
                            </div>
                        </div>
                    </a>

                    <div class="dropdown-menu dropdown-menu-arrow dropdown-menu-right">
                        <div class=" dropdown-header noti-title">
                            <h6 class="text-overflow m-0">Меню</h6>
                        </div>
                       <!-- <a href="{{ route('profile.edit') }}" class="dropdown-item">
                            <i class="mdi mdi-18px mdi-account"></i>
                            <span>Мой профиль</span>
                        </a>-->
                        <div class="dropdown-divider"></div>
                        <a href="{{ route('logout') }}" class="dropdown-item" onclick="event.preventDefault();
                        document.getElementById('logout-form').submit();">
                            <i class="mdi mdi-18px mdi-location-exit"></i>
                            <span>Выйти</span>
                        </a>
                    </div>
                @endauth
                @guest()
                    <a class="nav-link pr-0" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <div class="media align-items-center">
                        <span class="avatar avatar-sm rounded-circle">
                            <img alt="Фото пользователя" src="{{asset("/assets/img/common/profile_photo.png")}}">
                        </span>
                            <div class="media-body ml-2 d-none d-lg-block">
                                <span class="mb-0 text-sm  font-weight-bold">Гость</span>
                            </div>
                        </div>
                    </a>

                    <div class="dropdown-menu dropdown-menu-arrow dropdown-menu-right">
                        <div class=" dropdown-header noti-title">
                            <h6 class="text-overflow m-0">Меню</h6>
                        </div>
                        <a href="{{ route('login') }}" class="dropdown-item">
                            <i class="mdi mdi-18px mdi-location-enter"></i>
                            <span>Войти</span>
                        </a>

                    </div>
                @endauth
            </li>
        </ul>
    </div>
</nav>


