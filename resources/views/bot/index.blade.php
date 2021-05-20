@extends('layouts.layout')

@section('title')
    <title>Данные о персонаже</title>
@endsection

@section('content')
    <h1 align="center">Текст</h1>
    <div class="row">
        <div class="col-6">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th scope="col" colspan="4">Характеристики</th>
                </tr>
                <tr>
                    <th scope="col">Type</th>
                    <th scope="col">Column heading</th>
                    <th scope="col">Column heading</th>
                    <th scope="col">Column heading</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <th scope="row">Active</th>
                    <td>Column content</td>
                    <td>Column content</td>
                    <td>Column content</td>
                </tr>
                </tbody>
            </table>
        </div>
        <div class="col-6">Вторая колонка</div>
    </div>
@endsection
