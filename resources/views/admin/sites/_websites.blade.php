<div class="card shadow mb-4">
    <div class="card-header">
        {{ trans('vote::admin.sites.websites') }}
    </div>
    <div class="card-body">
        <ul>
        @foreach($sites as $site)
            <li>{{ $site->siteDomain }}</li>
        @endforeach
        </ul>
    </div>
</div>
