<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>NeuronTalks newsletter</title>
</head>
<body>
    <h1>NeuronTalks: {{ $month }}</h1>
    <p>Here are our latest blogs and news:</p>

    @foreach ($posts as $post)
        <article>
            <h2>{{ $post->title }}</h2>
            <p>{{ \Illuminate\Support\Str::limit(strip_tags($post->content ?? ''), 200) }}</p>
            <p><a href="{{ url('/blogs/'.$post->slug) }}">Read more</a></p>
        </article>
    @endforeach
</body>
</html>
