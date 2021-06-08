@extends('layouts.layout')

@section('title')
    <title>MoswarHelper - лучший помощник!</title>
@endsection

@section('content')
    <div class="jumbotron">
        <h1 class="display-4">MoswarHelper - лучший помощник для Мосвара!</h1>
        <p class="lead" align="justify">Хочешь купить и открыть 100500 зубных ящиков одним нажатием кнопки? Хочешь бросить миллион кубиков
            Москвополии, не утруждая себя постоянным обновлением страницы? Ты накопил миллиарды сертификатов на моментальную
            варку петриков, но откладываешь их использование? Складируешь у себя триллионы золота для игры с гадалкой,
            но хочешь сыграть на все моментально? Или ты давно желаешь надарить неприятелю негатива на год вперед, но делать это руками больно?
            А администрация никогда не сделает это внутри игры...?
            Или тебе просто надоело постоянно держать свой компьютер включенным, чтобы на нем работал бот?
        </p>
        <p class="lead" align="justify">Это приложение поможет разово выполнить рутинные действия по покупке
            и открытию зубных ящиков, бросанию кубиков Москвополии, моментальной варке петриков и игре с гадалкой. Также для
            использования доступна автоматическая отправка персонажа в патруль, шаурбургерс и смотреть ТВ по заданным настройкам.
            Также есть возможность указать настройки для игры с Кубовичем и бомбление конкретной машиной по понедельникам.
        </p>
        <hr class="my-8">
        <div class="row">
            <h2 align="center">Зачем тебе это нужно?</h2>
            <div class="col-4">
                <figure>
                    <blockquote class="blockquote">
                        <p class="mb-0">По дороге с работы домой купить и открыть кучу зубных ящиков, а полученные кубики бросить
                        в москвополии и вечером нагнуть вражину в стенке</p>
                    </blockquote>
                </figure>
            </div>
            <div class="col-4">
                <figure class="text-center">
                    <blockquote class="blockquote">
                        <p class="mb-0">Сыграть на все запасы золота с гадалкой и получить дополнительную Чайку или Снежный тигр,
                        не говоря об угадывании времени падения метеорита</p>
                    </blockquote>
                </figure>
            </div>
            <div class="col-4">
                <figure class="text-end">
                    <blockquote class="blockquote">
                        <p class="mb-0">Моментально сварить кучу петриков и прокачать еще {{ rand(1, 100) }} статов, чтобы стать на 146% сильнее.
                            Покажи своим вражинам кто круче!</p>
                    </blockquote>
                </figure>
            </div>
            <div class="col">
                <figure class="text-center">
                    <blockquote class="blockquote">
                        <p class="mb-0">Ну и конечно же просто не держать свой компьютер включенным для работы бота или арендовать
                            внешний сервер, чтобы разместить на нем бота</p>
                    </blockquote>
                </figure>
            </div>
        </div>
        <hr class="my-8">
        <div class="row">
            <div class="col-6">
                <div class="list-group">
                    <a class="list-group-item list-group-item-action flex-column align-items-start active">
                        <div class="d-flex w-100 justify-content-between">
                            <h5 class="mb-1" align="justify">Для всего вышеперечисленного уже давным давно есть скрипты. Зачем какое-то приложение?</h5>
                        </div>
                    </a>
                    <a class="list-group-item list-group-item-action flex-column align-items-start">
                        <p class="mb-1" align="justify">
                            Во-первых, скрипты есть не у всех. А если и попросить соклан, то часть из них и вовсе не помнит какой скрипт
                            за что отвечает (сам пробовал). <br>
                            Во-вторых, скрипты требуют ручного запуска в браузере. В чем автоматизация? <br>
                            В-третьих, не нужно держать свой компьютер включенным. Очень удобно, если вы играете с телефона/на работе.
                        </p>
                    </a>
                </div>
            </div>
            <div class="col-6">
                <div class="list-group">
                    <a class="list-group-item list-group-item-action flex-column align-items-start active">
                        <div class="d-flex w-100 justify-content-between">
                            <h5 class="mb-1" align="justify">Всем известный бот уже умеет играть с гадалкой и открывать зубные ящики!</h5>
                        </div>
                    </a>
                    <a class="list-group-item list-group-item-action flex-column align-items-start">
                        <p class="mb-1" align="justify">
                            Главная претензия к этому боту в том, что он, выполняя открытие зубных ящиков, исключает другие действия.
                            Иными словами, вы опоздаете на стенку противостояния, не успеете пройти метро/нефтепровод до конца и
                            так далее. А гадалка... - несколько часов уйдет на то, чтобы слить все запасы
                            золота, поскольку он дожидается окончания анимации вместо того, чтобы просто перезагрузить страницу.
                            И да, вам все равно придется руками купить зубные ящики;)<br>
                        </p>
                    </a>
                </div>
            </div>
            <div class="col-6">
                <br>
                <div class="list-group">
                    <a class="list-group-item list-group-item-action flex-column align-items-start active">
                        <div class="d-flex w-100 justify-content-between">
                            <h5 class="mb-1" align="justify">Не слишком ли дорого за такое смешное количество функций? Бот стоит 150 рублей
                            в месяц, зато полностью освобождает от рутины</h5>
                        </div>
                    </a>
                    <a class="list-group-item list-group-item-action flex-column align-items-start">
                        <p class="mb-1" align="justify">
                            Нет, не дорого, поскольку за стоимость лицензии вы получаете возможность безлимитного использования
                            модулей. Разумеется, если на момент их старта ваша лицензия не истекла. <br>
                            Более того, в будущем планируется введение и других модулей. Вряд ли стоимость от этого будет
                            увеличиваться. <br>
                            А главам кланов стоит подумать о возможности клановой лицензии по более выгодной цене для всех.
                        </p>
                    </a>
                </div>
            </div>
            <div class="col-6">
                <br>
                <div class="list-group">
                    <a class="list-group-item list-group-item-action flex-column align-items-start active">
                        <div class="d-flex w-100 justify-content-between">
                            <h5 class="mb-1" align="justify">А не ворует ли приложение пароли? Очень боязно вводить данные где попало</h5>
                        </div>
                    </a>
                    <a class="list-group-item list-group-item-action flex-column align-items-start">
                        <p class="mb-1" align="justify">
                            Дорогой ботовод, ты пользуешься ботом уже не первый год. Думаешь, если бы понадобилось
                            получить доступ к твоему персонажу, это было бы проблемой? <br>
                            При этом пароль от твоего персонажа наверняка имеется в гугл-таблице всех персонажей
                            клана дабы водить его в противостояние или на стенки метро. Бойся сливов таких таблиц,
                            а не приложения, которое призвано избавить от рутины. <br>
                            Но если ты по-прежнему боишься, то после работы модуля сразу можешь поменять пароль и
                            теперь уж его точно никто не узнает
                        </p>
                    </a>
                </div>
            </div>
        </div>

        <hr class="my-8">
        <p class="lead">Как это работает? Описание работы каждого модуля ты найдешь по кнопке ниже. Также не забывай
            делиться своими идеями по улучшению работы приложения по кнопке обратной связи</p>
        <p class="lead">
            <div class="row">
                <div class="col-4">

                </div>
                <div class="col-4">
                    <a class="btn btn-primary btn-lg" href="{{ route('manual') }}" role="button">Руководство пользователя</a>
                </div>
                <div class="col-4">
                    <a class="btn btn-primary btn-lg" href="#" role="button">Обратная связь</a>
                </div>
            </div>
        </p>
    </div>
@endsection
