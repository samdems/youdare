# Unified Authentication Flow

## Overview

The login and register flows have been merged into a single, seamless authentication experience. Users can now enter their email address (and optionally their name) on the login page, and the system will automatically:

1. **Create a new account** if the email doesn't exist in the system
2. **Send a magic link** to existing users who are logging back in

This eliminates the confusion between "login" and "register" and provides a frictionless user experience.

## How It Works

### User Experience

1. User visits the login page (`/login`)
2. User enters their **email address**
3. User clicks "Send Magic Link"
4. System automatically:
   - Creates an account if email is new (with auto-generated name from email)
   - Sends magic link to the email address
5. User clicks the magic link in their email
6. User is logged in and redirected to the game

### Technical Implementation

#### Auto-Registration Logic

In `AuthController::sendMagicLink()`:

```php
public function sendMagicLink(Request $request)
{
    $validated = $request->validate([
        "email" => "required|email|max:255",
    ]);

    $user = User::where("email", $validated["email"])->first();

    if (!$user) {
        // Auto-create account if it doesn't exist
        // Generate name from email: split on . and - and capitalize each word
        $emailPrefix = explode("@", $validated["email"])[0];
        $nameParts = preg_split("/[.\-_]/", $emailPrefix);
        $defaultName = implode(" ", array_map("ucfirst", $nameParts));

        $user = User::create([
            "name" => $defaultName,
            "email" => $validated["email"],
        ]);
    }

    $this->generateAndSendMagicLink($user);

    return back()->with(
        "success",
        "Check your email for a magic login link!",
    );
}
```

#### Automatic Name Generation

The system automatically generates a user-friendly name from the email address by:
1. Taking the part before the `@` symbol
2. Splitting on `.`, `-`, and `_` characters
3. Capitalizing each word
4. Joining with spaces

For example:
- Email: `john.doe@example.com` → Generated name: `John Doe`
- Email: `jane-smith@example.com` → Generated name: `Jane Smith`
- Email: `bob.the-builder@example.com` → Generated name: `Bob The Builder`
- Email: `alice_wonder@example.com` → Generated name: `Alice Wonder`

#### Route Changes

- `/register` now redirects to `/login`
- Old register controller methods have been removed
- Single unified form handles both login and registration

## Benefits

1. **Reduced Friction**: Users don't need to decide between "login" or "register"
2. **Simpler UX**: One form, one field - just enter your email
3. **No Failed Logins**: New users won't get "account doesn't exist" errors
4. **Passwordless**: Still maintains the magic link approach with no passwords needed
5. **Smart Defaults**: Names are automatically generated from email addresses and look professional

## Backwards Compatibility

- Old `/register` route still exists but redirects to `/login`
- Existing magic link verification and logout functionality unchanged
- All existing users can continue to login normally

## Future Considerations

- Consider adding a profile page where users can update their name after registration
- Could add social login options (Google, GitHub, etc.) in the future
- Might want to add rate limiting to prevent abuse of auto-registration