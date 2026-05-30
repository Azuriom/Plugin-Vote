@if($ipv6compatibility)
    <script src="https://cdn.ipv6-adapter.com/v1/api.js" async defer></script>
@endif

<script src="{{ plugin_asset('vote', 'js/vote.js?v3') }}" defer></script>
@auth
    <script>
        window.username = '{{ $user->name }}';
    </script>
@endauth
