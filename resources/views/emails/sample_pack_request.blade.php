<!DOCTYPE html>
<html>
<head>
    <title>New Sample Pack Request</title>
</head>
<body>
    <h2>Sample Pack Request Details</h2>
    <p><strong>Full Name:</strong> {{ $data['full_name'] }}</p>
    <p><strong>Email:</strong> {{ $data['email'] }}</p>
    <p><strong>Phone:</strong> {{ $data['phone'] }}</p>
    <p><strong>Address:</strong> {{ $data['address'] }}</p>
    <p><strong>Notes:</strong> {{ $data['notes'] ?? 'N/A' }}</p>
</body>
</html>
