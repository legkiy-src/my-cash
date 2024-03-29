@extends('layouts.app')
@section('content')
    <h3>Добавить расход</h3>
    @if ($errors->any())
        <div class="alert alert-danger">
            <h5><i class="icon fas fa-ban"></i>Ошибка!</h5>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form action="{{ route('expenses.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="accounts" class="form-label">Счёт</label>
            <select id="accounts" name="account" class="form-select">
                <option></option>
                @foreach($accounts as $account)
                    @if (old('account') == $account->id)
                        <option value="{{ $account->id }}" selected>{{ $account->name }}</option>
                    @else
                        <option value="{{ $account->id }}">{{ $account->name }}</option>
                    @endif
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="articles" class="form-label">Стаья расхода</label>
            <select id="articles" name="article" class="form-select">
                <option></option>
                @foreach($articles as $article)
                    @if (old('article') == $article->id)
                        <option value="{{ $article->id }}" selected>{{ $article->name }}</option>
                    @else
                        <option value="{{ $article->id }}">{{ $article->name }}</option>
                    @endif
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="total_sum" class="form-label">Сумма</label>
            <input type="text" class="form-control" id="total_sum" name="total_sum" value="{{ old('total_sum') }}">
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Описание</label>
            <textarea class="form-control" id="description" name="description">{{ old('description') }}</textarea>
        </div>
        <button type="submit" class="btn btn-primary">Сохранить</button>
    </form>
@endsection
