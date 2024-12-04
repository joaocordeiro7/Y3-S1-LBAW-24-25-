<form method="GET" action="{{ route('password.reset', ['token' => $token]) }}">
    <p>Hi {{ $user->username }},</p>
    <p>Click the link below to reset your password:</p>
    <p><a href="{{ $token }}">Reset Password</a></p>
    <p>If you did not request this, please ignore this email.</p>
</form>

