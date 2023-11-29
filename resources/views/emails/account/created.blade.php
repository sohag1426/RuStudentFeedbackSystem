<x-mail::message>
# Dear Sir

An account for you has been created successfully in the Student Feedback System.

The account access information is as follows:

<x-mail::table>
| Email | Password |
|:------------------:|:---------------:|
| {{ $user->email }} | {{ $password }} |
</x-mail::table>

<x-mail::button :url="$login_url">
    Login
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
