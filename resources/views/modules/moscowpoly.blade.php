@extends('layouts.layout')

@section('title')
    <title>Кубики москвополии</title>
@endsection

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Броски кубиков москвополии</div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('moscowpoly.work') }}">
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
                            <br>
                            <div class="form-group row">
                                <label for="name" class="col-md-4 col-form-label text-md-right">Сколько раз бросать</label>
                                <div class="col-md-6">
                                    <input type="number" class="form-control" id="teethCount" aria-describedby="teethHelp" name="cubesCount" placeholder="1">
                                </div>
                            </div>
                            <br>
                            <p class="text-muted">Учитывайте и бесплатные суточные броски. Если вы сегодня еще ни разу не бросали, добавляйте бесплатные броски.</p>
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
