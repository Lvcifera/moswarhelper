@extends('layouts.layout')

@section('title')
    <title>Управлении лицензиями</title>
@endsection

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Добавить лицензию</div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('licence.add') }}">
                            @csrf
                            <div class="form-group row">
                                <label for="name" class="col-md-4 col-form-label text-md-right">Количество месяцев</label>
                                <div class="col-md-6">
                                    <input type="number" class="form-control" id="teethCount" aria-describedby="teethHelp" name="monthCount" placeholder="1">
                                </div>
                            </div>
                            <br>
                            <div class="form-group row">
                                <label for="name" class="col-md-4 col-form-label text-md-right">Ник персонажа</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" id="teethCount" aria-describedby="teethHelp" name="player" placeholder="Бедвер">
                                </div>
                            </div>
                            <br>
                            <p class="text-muted">На каждого персонажа создавайте отдельную лицензию. Стоимость одной лицензии 50 рублей в месяц.</p>
                            <p class="text-danger">Создавайте лицензию только на тех персонажей, к которым у вас есть доступ. В противном случае средства
                            возвращены не будут. В случае случайной ошибки в имени персонажа возврат средств за лицензию осуществляется через обратную связь</p>
                            <br>
                            <div class="form-group row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary">Добавить лицензию</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <br>
        @if ($licences->count() != null)
            <h1 class="display-6" align="center">Ваши лицензии</h1>
            <table class="table table-hover">
                <thead>
                <tr>
                    <th scope="col">Номер</th>
                    <th scope="col">Персонажи</th>
                    <th scope="col">Активирована</th>
                    <th scope="col">Истекает</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($licences as $key => $licence)
                    @if ($licence->end < \Carbon\Carbon::now())
                        <tr class="table-danger">
                    @else
                        <tr class="table">
                    @endif
                        <th scope="row">{{ $key + 1 }}</th>
                        <td>{{ $licence->player }}</td>
                        <td>{{ $licence->start }}</td>
                        <td>{{ $licence->end }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @else
            <h1 class="display-6">У вас нет ни одной лицензии</h1>
        @endif

    </div>
@endsection
