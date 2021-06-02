@extends('layouts.layout')

@section('title')
    <title>Функции бота</title>
@endsection

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Повседневные функции бота</div>
                    <div class="card-body">
                        <p class="text-muted">Все функции выполняются автоматически в фоновом режиме, для каждой
                        свой период запуска. Просто настройте нужную вам функцию и создайте задачу.
                        <p class="text-info">Внимание! Возможны погрешности во времени запуска.</p>
                        <ul class="nav nav-tabs">
                            <li class="nav-item">
                                <a class="nav-link active" data-toggle="tab" href="#patrol">Патруль</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#shaurburgers">Шаурбургерс</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#cosmodrome">Космодром</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#taxes">Бомбила</a>
                            </li>
                        </ul>
                        <div id="myTabContent" class="tab-content">
                            <div class="tab-pane fade active show" id="patrol">
                                <form method="POST" action="{{ route('patrol.create') }}">
                                    @csrf
                                    <div class="form-group row">
                                        <label for="name" class="col-md-4 col-form-label text-md-right">На каком персонаже</label>
                                        <div class="col-md-6">
                                            <select class="form-select" id="exampleSelect1" name="player">
                                                @if (!$players->isEmpty())
                                                    @foreach ($players as $player)
                                                        <option value="{{ $player->id }}">{{ $player->player }}</option>
                                                    @endforeach
                                                @else
                                                    <option>Нет персонажей</option>
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="form-group row">
                                        <label for="name" class="col-md-4 col-form-label text-md-right">Район для патрулирования</label>
                                        <div class="col-md-6">
                                            <select class="form-select" id="exampleSelect1" name="region">
                                                <option value="1">Кремлевский</option>
                                                <option value="2">Звериный</option>
                                                <option value="3">Вокзальный</option>
                                                <option value="4">Винно-заводский</option>
                                                <option value="5">Монеточный</option>
                                                <option value="6">Небоскреб-сити</option>
                                                <option value="7">Промышленный</option>
                                                <option value="8">Телевизионный</option>
                                                <option value="10">Парковый</option>
                                                <option value="11">Спальный</option>
                                                <option value="12">Дворцовый</option>
                                                <option value="13">Газовый</option>
                                                <option value="15">Причальный</option>
                                                <option value="16">Водоохранный</option>
                                                <option value="17">Лосинск</option>
                                                <option value="18">Внучатово</option>
                                            </select>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="form-group row">
                                        <label for="name" class="col-md-4 col-form-label text-md-right">Сколько минут патрулировать</label>
                                        <div class="col-md-6">
                                            <select class="form-select" id="exampleSelect1" name="time">
                                                <option value="10">10 минут</option>
                                                <option value="20">20 минут</option>
                                                <option value="30">30 минут</option>
                                                <option value="40">40 минут</option>
                                                <option value="50">50 минут</option>
                                                <option value="60">60 минут</option>
                                            </select>
                                        </div>
                                    </div>
                                    <br>
                                    <p class="text-muted">По одной задаче на каждого персонажа. Повторы обновляют задачу этого персонажа.</p>
                                    <div class="form-group row mb-0">
                                        <div class="col-md-6 offset-md-4">
                                            <button type="submit" class="btn btn-primary">Добавить задачу</button>
                                        </div>
                                    </div>
                                </form>
                                <br>
                                <table class="table table-hover">
                                    <thead>
                                    <tr>
                                        <th scope="col">Номер</th>
                                        <th scope="col">Персонаж</th>
                                        <th scope="col">Район</th>
                                        <th scope="col">Время</th>
                                        <th scope="col">Последний запуск</th>
                                        <th scope="col">Действие</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($patrols as $key => $patrol)
                                        @if ($patrol->character->licence->end < \Carbon\Carbon::now())
                                            <tr class="table-danger">
                                        @else
                                            <tr class="table">
                                        @endif
                                            <th scope="row">{{ $key + 1 }}</th>
                                            <td>{{ $patrol->character->player }}</td>
                                            <td>{{ $patrol->region }}</td>
                                            <td>{{ $patrol->time }} минут</td>
                                            <td>
                                                @if ($patrol->last_start != null)
                                                    {{ $patrol->last_start }}
                                                @else
                                                    Сегодня не было запуска
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('patrol.delete', ['id' => $patrol->id]) }}" class="text-danger">Удалить</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="tab-pane fade" id="shaurburgers">
                                <form method="POST" action="{{ route('shaurburgers.create') }}">
                                    @csrf
                                    <div class="form-group row">
                                        <label for="name" class="col-md-4 col-form-label text-md-right">На каком персонаже</label>
                                        <div class="col-md-6">
                                            <select class="form-select" id="exampleSelect1" name="player">
                                                @if (!$players->isEmpty())
                                                    @foreach ($players as $player)
                                                        <option value="{{ $player->id }}">{{ $player->player }}</option>
                                                    @endforeach
                                                @else
                                                    <option>Нет персонажей</option>
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="form-group row">
                                        <label for="name" class="col-md-4 col-form-label text-md-right">Сколько часов работать</label>
                                        <div class="col-md-6">
                                            <select class="form-select" id="exampleSelect1" name="time">
                                                <option value="1">1 час</option>
                                                <option value="2">2 часа</option>
                                                <option value="3">3 часа</option>
                                                <option value="4">4 часа</option>
                                                <option value="5">5 часов</option>
                                                <option value="6">6 часов</option>
                                                <option value="7">7 часов</option>
                                                <option value="8">8 часов</option>
                                            </select>
                                        </div>
                                    </div>
                                    <br>
                                    <p class="text-muted">По одной задаче на каждого персонажа. Повторы обновляют задачу этого персонажа.</p>
                                    <div class="form-group row mb-0">
                                        <div class="col-md-6 offset-md-4">
                                            <button type="submit" class="btn btn-primary">Добавить задачу</button>
                                        </div>
                                    </div>
                                </form>
                                <br>
                                <table class="table table-hover">
                                    <thead>
                                    <tr>
                                        <th scope="col">Номер</th>
                                        <th scope="col">Персонаж</th>
                                        <th scope="col">Время</th>
                                        <th scope="col">Последний запуск</th>
                                        <th scope="col">Действие</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($shaurburgers as $key => $shaurburger)
                                        @if ($shaurburger->character->licence->end < \Carbon\Carbon::now())
                                            <tr class="table-danger">
                                        @else
                                            <tr class="table">
                                        @endif
                                            <th scope="row">{{ $key + 1 }}</th>
                                            <td>{{ $shaurburger->character->player }}</td>
                                            <td>{{ $shaurburger->time }}</td>
                                            <td>
                                                @if ($shaurburger->last_start != null)
                                                    {{ $shaurburger->last_start }}
                                                @else
                                                    Сегодня не было запуска
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('shaurburgers.delete', ['id' => $shaurburger->id]) }}" class="text-danger">Удалить</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="tab-pane fade" id="cosmodrome">
                                <form method="POST" action="">
                                    @csrf
                                    <div class="form-group row">
                                        <label for="name" class="col-md-4 col-form-label text-md-right">На каком персонаже</label>
                                        <div class="col-md-6">
                                            <select class="form-select" id="exampleSelect1" name="player">
                                                @if (!$players->isEmpty())
                                                    @foreach ($players as $player)
                                                        <option>{{ $player->player }}</option>
                                                    @endforeach
                                                @else
                                                    <option>Нет персонажей</option>
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="tab-pane fade" id="taxes">
                                <form method="POST" action="{{ route('taxes.create') }}">
                                    @csrf
                                    <div class="form-group row">
                                        <label for="name" class="col-md-4 col-form-label text-md-right">На каком персонаже</label>
                                        <div class="col-md-6">
                                            <select class="form-select" id="exampleSelect1" name="player">
                                                @if (!$players->isEmpty())
                                                    @foreach ($players as $player)
                                                        <option value="{{ $player->id }}">{{ $player->player }}</option>
                                                    @endforeach
                                                @else
                                                    <option>Нет персонажей</option>
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="form-group row">
                                        <label for="name" class="col-md-4 col-form-label text-md-right">Порядковый номер машины</label>
                                        <div class="col-md-6">
                                            <input type="number" class="form-control" id="carNumber" aria-describedby="carNumber" name="carNumber" placeholder="1">
                                        </div>
                                    </div>
                                    <br>
                                    <p class="text-muted">По одной задаче на каждого персонажа. Повторы обновляют задачу этого персонажа.</p>
                                    <div class="form-group row mb-0">
                                        <div class="col-md-6 offset-md-4">
                                            <button type="submit" class="btn btn-primary">Добавить задачу</button>
                                        </div>
                                    </div>
                                </form>
                                <br>
                                <table class="table table-hover">
                                    <thead>
                                    <tr>
                                        <th scope="col">Номер</th>
                                        <th scope="col">Персонаж</th>
                                        <th scope="col">Номер машины</th>
                                        <th scope="col">Последний запуск</th>
                                        <th scope="col">Действие</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($taxes as $key => $tax)
                                        @if ($tax->character->licence->end < \Carbon\Carbon::now())
                                            <tr class="table-danger">
                                        @else
                                            <tr class="table">
                                                @endif
                                                <th scope="row">{{ $key + 1 }}</th>
                                                <td>{{ $tax->character->player }}</td>
                                                <td>{{ $tax->car_number }}</td>
                                                <td>
                                                    @if ($tax->last_start != null)
                                                        {{ $tax->last_start }}
                                                    @else
                                                        Еще не было запуска
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{ route('taxes.delete', ['id' => $tax->id]) }}" class="text-danger">Удалить</a>
                                                </td>
                                            </tr>
                                            @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
