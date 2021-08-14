@extends('layouts.layout')

@section('title')
    <title>Зубные ящики</title>
@endsection

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Покупка и открытие ящиков</div>
                    <div class="card-body">
                        <form method="POST" id="teethWork" action="{{ route('teeth.work') }}">
                            @csrf
                            <div class="form-group row">
                                <label for="name" class="col-md-4 col-form-label text-md-right">На каком персонаже</label>
                                <div class="col-md-6">
                                    <select class="form-select" id="player" name="player">
                                        @if (!$characters->isEmpty())
                                            @foreach ($characters as $character)
                                                <option>{{ $character->player }}</option>
                                            @endforeach
                                        @else
                                            <option>Нет персонажей</option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <br>
                            <div class="form-group row">
                                <label for="name" class="col-md-4 col-form-label text-md-right">Сколько купить и открыть</label>
                                <div class="col-md-6">
                                    <input type="number" class="form-control" id="teethCount" aria-describedby="teethHelp" name="teethCount" placeholder="1">
                                </div>
                            </div>
                            <br>
                            <div class="form-group row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" id="submit" class="btn btn-primary">Поехали</button>
                                </div>
                            </div>
                        </form>
                        <br>
                        <div class="alert alert-dismissible alert-danger" hidden="true" id="danger">
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <div class="alert alert-dismissible alert-success" hidden="true" id="success">
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $('#teethWork').on('submit',function(event){
            event.preventDefault();
            let player = $('#player').val();
            let teethCount = $('#teethCount').val();
            let count = 0;

            while (count < teethCount) {
                let xhr = $.ajax({
                    url: '{{ route('teeth.work') }}',
                    type: 'POST',
                    async: false,
                    data: {
                        '_token': '{{ csrf_token() }}',
                        player: player,
                        teethCount: teethCount
                    },
                    success: function () {
                        console.log('success');
                    }
                });
                if (xhr.responseJSON.teethLost === true) {
                    let danger = document.getElementById('danger');
                    danger.textContent = 'Закончились зубы. Куплено и открыто ' + count + ' ящиков.';
                    danger.hidden = false;
                    break;
                } else if (xhr.responseJSON.fight === true) {
                    let danger = document.getElementById('danger');
                    danger.textContent = 'Персонаж оказался в стенке. Куплено и открыто ' + count + ' ящиков.';
                    danger.hidden = false;
                    break;
                }
                count++;
            }
            if (count == teethCount) {
                let success = document.getElementById('success');
                success.textContent = 'Успешно. Куплено и открыто ' + count + ' ящиков.';
                success.hidden = false;
            }
        });
    </script>
@endsection
