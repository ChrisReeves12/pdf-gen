@extends('layout.default')
@section('content')
    <div class="container mt-5">
        <div class="card">
            <div class="card-body">
                <h1 class="card-title">RSS PDF Generator</h1>

                @if(!empty($error_message))
                    <h2>{{ $error_message }}</h2>
                @endif
                {{ Form::open(['url' => route('post.home.generate_pdf', [], false)]) }}
                <div class="row">
                    <div class="col-md-6 col-12">
                        <input value="{{ old('url') }}" type="text" name="url" class="form-control" placeholder="RSS Feed URL"/>
                        @error('url')
                        <div class="form-field-error">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 col-12 mt-md-0 mt-3">
                        <button class="btn btn-primary" type="submit">Generate</button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <small>Example: <a href="https://www.yahoo.com/news/rss">https://www.yahoo.com/news/rss</a></small>
                    </div>
                    <div class="col-12">
                        <small>Example: <a href="https://www.soompi.com/feed">https://www.soompi.com/feed</a></small>
                    </div>
                    <div class="col-12">
                        <small>Example: <a href="http://rss.cnn.com/rss/cnn_us.rss">http://rss.cnn.com/rss/cnn_us.rss</a></small>
                    </div>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
@endsection
