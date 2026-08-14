<!DOCTYPE html>
<html>
<body style="font-family: sans-serif">
    <h2>New audit request</h2>
    <p><strong>Name:</strong> {{ $audit->name }}</p>
    <p><strong>Email:</strong> {{ $audit->email }}</p>
    <p><strong>Phone:</strong> {{ $audit->phone ?? 'Not provided' }}</p>
    <p><strong>Message:</strong> {{ $audit->message }}</p>
    <p><strong>What to improve:</strong> {{ $audit->improve }}</p>
</body>
</html>
