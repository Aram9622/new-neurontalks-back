<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>{{ $subject }}</title></head>
<body>
    {!! $body !!}
    @isset($subscription)
        @include('emails.partials.unsubscribe', ['subscription' => $subscription])
    @endisset
</body>
</html>
