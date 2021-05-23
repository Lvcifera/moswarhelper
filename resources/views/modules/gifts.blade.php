@extends('layouts.layout')

@section('title')
    <title>Дарение подарков</title>
@endsection

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Подарить большое количество подарков</div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('gifts.work') }}">
                            @csrf
                            <div class="form-group row">
                                <label for="name" class="col-md-4 col-form-label text-md-right">Кто дарит</label>
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
                            <br>
                            <div class="form-group row">
                                <label for="name" class="col-md-4 col-form-label text-md-right">Кому подарить (ник персонажа)</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" id="teethCount" aria-describedby="teethHelp" name="reciever" placeholder="Лестер Массена">
                                    <p class="text-muted">Если хотите себе, вписывайте свой ник.</p>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="name" class="col-md-4 col-form-label text-md-right">Подарок</label>
                                <div class="col-md-6">
                                    <select class="form-select" id="exampleSelect1" name="gift">
                                        <option value="10394">Злой гений</option>
                                        <option value="10395">Очень ч0рная Метка</option>
                                        <option value="77">Заяц несудьбы</option>
                                        <option value="3351">Валуйки «Heavy Edition»</option>
                                        <option value="309">Валуйки</option>
                                        <option value="670">Респиратор</option>
                                        <option value="671">Противогаз</option>
                                        <option value="3860">Чай в железной банке</option>
                                        <option value="3864">Глазированные батончики</option>
                                        <option value="2936">Чай в пирамидках</option>
                                        <option value="2937">Шоколадки «Пралине»</option>
                                        <option value="325">Шоколад</option>
                                        <option value="328">Чай</option>
                                        <option value="324">Шоколад (малышовый)</option>
                                        <option value="327">Чай (малышовый)</option>
                                        <option value="323">Шоколад (малышовый)</option>
                                        <option value="326">Чай (малышовый)</option>
                                        <option value="11131">Собака-сопротивляка</option>
                                        <option value="11129">Асенькин цветочек</option>
                                        <option value="10274">Кроличья... а впрочем, кролик целиком</option>
                                        <option value="10275">Лампа с Джинном или Коньяком на худой конец</option>
                                        <option value="10276">След Йети или дяди Вовы</option>
                                        <option value="10277">Шапка Ахалай Махалаевича</option>
                                        <option value="793">Легомэн</option>
                                        <option value="794">Чашка кофе</option>
                                        <option value="795">Телескоп</option>
                                        <option value="1094">Бодрящий коктейль</option>
                                        <option value="3921">Чай в подстаканнике</option>
                                    </select>
                                </div>
                            </div>
                            <br>
                            <div class="form-group row">
                                <label for="name" class="col-md-4 col-form-label text-md-right">Анонимно</label>
                                <div class="col-md-6">
                                    <input class="form-check-input" type="checkbox" value="on" name="anonimous" id="flexCheckDefault">
                                </div>
                            </div>
                            <br>
                            <div class="form-group row">
                                <label for="name" class="col-md-4 col-form-label text-md-right">Комментарий</label>
                                <div class="col-md-6">
                                    <textarea class="form-control" id="exampleTextarea" name="comment" rows="2"></textarea>
                                </div>
                            </div>
                            <br>
                            <div class="form-group row">
                                <label for="name" class="col-md-4 col-form-label text-md-right">Текст виден только получателю</label>
                                <div class="col-md-6">
                                    <input class="form-check-input" type="checkbox" value="on" name="private" id="flexCheckDefault">
                                </div>
                            </div>
                            <br>
                            <div class="form-group row">
                                <label for="name" class="col-md-4 col-form-label text-md-right">Сколько раз подарить</label>
                                <div class="col-md-6">
                                    <input type="number" class="form-control" id="giftCount" aria-describedby="giftCount" name="giftCount" placeholder="1">
                                </div>
                            </div>
                            <br>
                            <div class="form-group row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary">Поехали</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
