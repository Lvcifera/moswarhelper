@extends('layouts.layout')

@section('title')
    <title>Управление персонажами</title>
@endsection

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Авторизация персонажа</div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('character.add') }}">
                            @csrf
                            <div class="form-group">
                                <label for="exampleInputEmail">Почта</label>
                                <input type="email" class="form-control" id="exampleInputEmail" aria-describedby="emailHelp" name="email"  placeholder="test@mail.ru">
                            </div>
                            <br>
                            <div class="form-group">
                                <label for="exampleInputPassword">Пароль</label>
                                <input type="password" class="form-control" id="exampleInputPassword" name="password" placeholder="qwerty">
                            </div>
                            <br>
                            <button type="submit" class="btn btn-primary">Авторизовать персонажа</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <br>
        @if ($characters->count() != null)
            <h1 class="display-6" align="center">Ваши персонажи</h1>
            <table class="table table-hover">
                <thead>
                <tr>
                    <th scope="col">Номер</th>
                    <th scope="col">Персонаж</th>
                    <th scope="col">ID персонажа</th>
                    <th scope="col">Добавлено</th>
                    <th scope="col">Обновлено</th>
                    <th scope="col">Действие</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($characters as $key => $character)
                    <tr class="table">
                        <th scope="row">{{ $key + 1 }}</th>
                        <td>{{ $character->player }}</td>
                        <td>{{ $character->player_id }}</td>
                        <td>{{ $character->created_at }}</td>
                        <td>{{ $character->updated_at }}</td>
                        <td>
                            <a href="{{ route('character.delete', ['id' => $character->id]) }}" class="text-danger">Удалить</a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @else
            <h1 class="display-6">Вы не авторизовали ни одного персонажа</h1>
        @endif
    </div>
@endsection
