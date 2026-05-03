<!DOCTYPE html>
<html>
<head>
    <title>New Inquiry - NeuronTalks</title>
</head>
<body style="font-family: sans-serif; background-color: #f4f4f4; padding: 20px;">
    <div style="background-color: #ffffff; padding: 20px; border-radius: 8px; border-top: 5px solid #0c4a6e;">
        <h2 style="color: #0c4a6e;">New Message Received</h2>
        <p>You have a new inquiry from the <strong>NeuronTalks</strong> website.</p>
        
        <table border="0" cellpadding="10" cellspacing="0" width="100%" style="border: 1px solid #eeeeee;">
            <tr>
                <td style="background-color: #f9f9f9; width: 150px;"><strong>Name:</strong></td>
                <td>{{ $contact->name }}</td>
            </tr>
            <tr>
                <td style="background-color: #f9f9f9;"><strong>Email:</strong></td>
                <td>{{ $contact->email }}</td>
            </tr>
            <tr>
                <td style="background-color: #f9f9f9;"><strong>Phone:</strong></td>
                <td>{{ $contact->phone ?? 'Not provided' }}</td>
            </tr>
            <tr>
                <td style="background-color: #f9f9f9; vertical-align: top;"><strong>Message:</strong></td>
                <td>{{ $contact->message }}</td>
            </tr>
        </table>

        <p style="margin-top: 20px;">
            <a href="{{ url('/admin/contacts/' . $contact->id . '/edit') }}" 
               style="background-color: #0c4a6e; color: #ffffff; padding: 10px 20px; text-decoration: none; border-radius: 4px;">
               View in Admin Panel
            </a>
        </p>
    </div>
</body>
</html>
