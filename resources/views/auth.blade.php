@extends('layouts.layout')

@section('title')
    <title>Авторизация персонажа</title>
@endsection

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Авторизация персонажа</div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('authorizeTry') }}">
                            <input hidden type="text" class="form-control" id="action" value="login" name="action">
                            <div class="form-group">
                                <label for="exampleInputEmail">Почта</label>
                                <input type="email" class="form-control" id="exampleInputEmail" aria-describedby="emailHelp" name="email" value="ak-47y@mail.ru" placeholder="Enter email">
                            </div>
                            <div class="form-group">
                                <label for="exampleInputPassword">Пароль</label>
                                <input type="password" class="form-control" id="exampleInputPassword" name="password" value="R2004pBC" placeholder="Password">
                            </div>
                            <br>
                            <input hidden type="text" class="form-control" id="remember" value="on" name="remember">
                            <button type="submit" class="btn btn-primary">Авторизоваться</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
