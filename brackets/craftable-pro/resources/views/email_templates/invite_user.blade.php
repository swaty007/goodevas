<x-mail::message>

# {{ ___('craftable-pro', 'Invite user') }}

You were invited by user {{ $userFullName }} to join.

Please follow the link bellow to create your account.

<x-mail::button :url="route('craftable-pro.invite-user.create', $email)">
{{ ___('craftable-pro', 'Sign in') }}
</x-mail::button>

</x-mail::message>
