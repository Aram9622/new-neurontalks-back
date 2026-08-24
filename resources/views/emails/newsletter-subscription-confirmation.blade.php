<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to the NeuronTalks newsletter</title>
</head>
<body>
    <h1>Thank you for subscribing!</h1>
    <p>Your email address, {{ $subscription->email }}, is now subscribed to the NeuronTalks newsletter.</p>
    <p>We look forward to sharing our latest blogs and news with you.</p>
    @include('emails.partials.unsubscribe', ['subscription' => $subscription])
</body>
</html>
