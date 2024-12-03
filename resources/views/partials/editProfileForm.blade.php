<div class="form-group">
    <label for="username">Username:</label>
    <input type="text" name="username" id="username" value="{{ old('username', $user->username) }}" class="form-control">
    <span class="error text-danger" style="display: none;" id="username-error"></span>
</div>

<div class="form-group">
    <label for="email">Email:</label>
    <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" class="form-control">
    <span class="error text-danger" style="display: none;" id="email-error"></span>
</div>

<div class="form-group">
    <label for="password">New Password (leave blank to keep the current password):</label>
    <input type="password" name="password" id="password" class="form-control">
    <span class="error text-danger" style="display: none;" id="password-error"></span>
</div>

<div class="form-group">
    <label for="password_confirmation">Confirm New Password:</label>
    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
    <span class="error text-danger" style="display: none;" id="password_confirmation-error"></span>
</div>

<div class="form-group">
    <label for="profile_picture">Profile Picture:</label>
    <input type="file" name="image" id="profile_picture" class="form-control">
</div>

