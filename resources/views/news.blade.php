@extends('layouts.layout')

@section('title')
    <title>Новости проекта</title>
@endsection

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="card mb-3">
                <div class="card-header">Состоялся релиз проекта</div>
                <div class="card-body">
                    <p class="card-text">Спустя три недели активной разработки проект наконец-то был запущен</p>
                </div>
                <div class="card-footer text-muted">
                    Размещено 8 мая 2021 года
                </div>
            </div>
        </div>
    </div>
@endsection
