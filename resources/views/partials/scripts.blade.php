@if($ipv6compatibility && ($ipAdapter ?? 'ipv6-adapter') === 'ipv6-adapter')
    <script src="https://ipv6-adapter.com/api/v1/api.js" async defer></script>
@endif

@if($ipv6compatibility && ($ipAdapter ?? 'ipv6-adapter') === 'ipify')
    <script>window.voteIpAdapter = 'ipify';</script>
@endif

<script src="{{ plugin_asset('vote', 'js/vote.js?v4') }}" defer></script>

@auth
    <script>window.username = '{{ $user->name }}';</script>
@endauth
