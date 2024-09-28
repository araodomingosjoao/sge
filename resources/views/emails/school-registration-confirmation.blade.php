<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Registration Confirmation</title>
</head>
<body>
    <h1>Welcome to Our Platform!</h1>
    
    <p>Dear {{ $user->name }},</p>
    
    <p>We are pleased to confirm that your school, {{ $school->name }}, has been successfully registered on our platform.</p>
    
    <p>Here are some details of your registration:</p>
    <ul>
        <li>School Name: {{ $school->name }}</li>
        <li>Education Type: {{ $school->education_type }}</li>
        <li>Admin Email: {{ $user->email }}</li>
    </ul>
    
    <p>You can now log in to your account using your email address and the password you provided during registration.</p>
    
    <p>If you have any questions or need assistance, please don't hesitate to contact our support team.</p>
    
    <p>Thank you for choosing our platform!</p>
    
    <p>Best regards,<br>Your Platform Team</p>
</body>
</html>