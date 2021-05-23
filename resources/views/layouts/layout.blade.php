<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="shortcut icon" href="https://img.icons8.com/dusk/64/000000/cursor.png" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('style/bootstrap.css') }}" media="screen">
    <link rel="stylesheet" href="{{ asset('style/custom.min.css') }}">
    @yield('title')
</head>
<body>

<div class="navbar navbar-expand-lg fixed-top navbar-light bg-light">
    <div class="container">
        <a href="{{ route('welcome') }}" class="navbar-brand">MoswarHelper</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-bs-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse d-flex justify-content-between" id="navbarResponsive">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('welcome') }}">Главная</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('manual') }}">Руководство пользователя</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Модули</a>
                    <div class="dropdown-menu" aria-labelledby="download">
                        <a class="dropdown-item" href="{{ route('teeth') }}">Зубные ящики</a>
                        <a class="dropdown-item" href="{{ route('moscowpoly') }}">Кубики Москвополии</a>
                        <a class="dropdown-item" href="{{ route('gypsy') }}">Игра с гадалкой</a>
                        <a class="dropdown-item" href="{{ route('petriks') }}">Варка петриков</a>
                        <a class="dropdown-item" href="{{ route('gifts') }}">Дарение подарков</a>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="">Обратная связь</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="">Что нового?</a>
                </li>
            </ul>

            <ul class="navbar-nav ml-auto">
                <!-- Authentication Links -->
                @guest
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">{{ __('Войти') }}</a>
                    </li>
                    @if (Route::has('register'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">{{ __('Регистрация') }}</a>
                        </li>
                    @endif
                @else
                    <li class="nav-item">
                        <a class="nav-link">Баланс {{ auth()->user()->balance }} рублей</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('auth') }}">Авторизовать персонажей</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('licences') }}">Управление лицензиями</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            {{ Auth::user()->name }} <span class="caret"></span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="">
                                {{ __('Обо мне') }}
                            </a>
                            <a class="dropdown-item" href="">
                                {{ __('Редактировать профиль') }}
                            </a>
                            <a class="dropdown-item" href="">
                                {{ __('Мои тикеты') }}
                            </a>
                            <a class="dropdown-item" href=""
                               onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                {{ __('Выйти') }}
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </div>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</div>

<div class="container">
    @if(session('success'))
        <div class="alert alert-dismissible alert-success">
            {{ session('success') }}
        </div>
    @endif
    @if(session('danger'))
        <div class="alert alert-dismissible alert-danger">
            {{ session('danger') }}
        </div>
    @endif

    @if($errors->any())
        @foreach($errors->all() as $error)
            <div class="alert alert-dismissible alert-danger" role="alert">
                {{ $error }}
            </div>
        @endforeach
    @endif

    @yield('content')
</div>

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>

</body>
</html>
