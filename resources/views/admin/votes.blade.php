@extends('admin.layouts.admin')

@section('title', trans('vote::admin.votes.title'))

@section('content')
    <div class="row">

        <div class="col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col mt-0">
                            <h5 class="card-title mb-0">
                                {{ trans('vote::admin.votes.votes') }}
                            </h5>
                        </div>

                        <div class="col-auto">
                            <div class="stat text-primary">
                                <i class="bi bi-calendar"></i>
                            </div>
                        </div>
                    </div>
                    <h1 class="mt-1 mb-3">{{ $votesCount }}</h1>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col mt-0">
                            <h5 class="card-title mb-0">
                                {{ trans('vote::admin.votes.month') }}
                            </h5>
                        </div>

                        <div class="col-auto">
                            <div class="stat text-primary">
                                <i class="bi bi-calendar-month"></i>
                            </div>
                        </div>
                    </div>
                    <h1 class="mt-1 mb-3">{{ $votesCountMonth }}</h1>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col mt-0">
                            <h5 class="card-title mb-0">
                                {{ trans('vote::admin.votes.week') }}
                            </h5>
                        </div>

                        <div class="col-auto">
                            <div class="stat text-primary">
                                <i class="bi bi-calendar-week"></i>
                            </div>
                        </div>
                    </div>
                    <h1 class="mt-1 mb-3">{{ $votesCountWeek }}</h1>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col mt-0">
                            <h5 class="card-title mb-0">
                                {{ trans('vote::admin.votes.day') }}
                            </h5>
                        </div>

                        <div class="col-auto">
                            <div class="stat text-primary">
                                <i class="bi bi-calendar-day"></i>
                            </div>
                        </div>
                    </div>
                    <h1 class="mt-1 mb-3">{{ $votesCountDay }}</h1>
                </div>
            </div>
        </div>
    </div>

    <form class="row row-cols-lg-auto g-3 align-items-center" action="{{ route('vote.admin.votes.index') }}" method="GET" role="search">
        <div class="mb-3">
            <label for="searchInput" class="visually-hidden">
                {{ trans('messages.actions.search') }}
            </label>

            <div class="input-group">
                <input type="search" class="form-control" id="searchInput" name="search" value="{{ $search ?? '' }}" placeholder="{{ trans('messages.actions.search') }}">

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </div>
    </form>

    <div class="card shadow mb-4">
        <div class="table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">{{ trans('messages.fields.user') }}</th>
                    <th scope="col">{{ trans('vote::messages.fields.site') }}</th>
                    <th scope="col">{{ trans('vote::messages.fields.reward') }}</th>
                    <th scope="col">{{ trans('messages.fields.date') }}</th>
                </tr>
                </thead>
                <tbody>

                @foreach($votes as $vote)
                    <tr>
                        <th scope="row">{{ $vote->id }}</th>
                        <td>
                            <a href="{{ route('admin.users.edit', $vote->user) }}">
                                {{ $vote->user->name }}
                            </a>
                        </td>
                        <td>
                            @if($vote->site !== null)
                                <a href="{{ route('vote.admin.sites.edit', $vote->site) }}">
                                    {{ $vote->site->name }}
                                </a>
                            @else
                                #{{ $vote->site_id }}
                            @endif
                        </td>
                        <td>
                            @if($vote->reward !== null)
                                <a href="{{ route('vote.admin.rewards.edit', $vote->reward) }}">
                                    {{ $vote->reward->name }}
                                </a>
                            @else
                                #{{ $vote->reward_id }}
                            @endif
                        </td>
                        <td>{{ format_date_compact($vote->created_at) }}</td>
                    </tr>
                @endforeach

                </tbody>
            </table>
        </div>

        {{ $votes->withQueryString()->links() }}
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                {{ trans('vote::messages.sections.top') }}
            </h6>
        </div>
        <div class="card-body">
            <ul class="nav nav-pills mb-3" id="voteTabs" role="tablist">
                @foreach($topVotes as $voteDate => $userVotes)
                    <li class="nav-item" role="presentation">
                        <a class="nav-link @if($voteDate === $now) active @endif" id="voteTab{{ $loop->index }}"
                           data-bs-toggle="tab" href="#votesPane{{ $loop->index }}" role="tab"
                           aria-controls="votesPane{{ $loop->index }}"
                           aria-selected="{{ $voteDate === $now ? 'true' : 'false' }}">
                            {{ $voteDate }}
                        </a>
                    </li>
                @endforeach
            </ul>

            <div class="tab-content">
                @foreach($topVotes as $voteDate => $userVotes)
                    <div class="tab-pane fade @if($voteDate === $now) show active @endif"
                         id="votesPane{{ $loop->index }}" aria-labelledby="votesTab{{ $loop->index }}">
                        @if(! $userVotes->isEmpty())
                            <table class="table">
                                <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">{{ trans('messages.fields.name') }}</th>
                                    <th scope="col">{{ trans('vote::messages.fields.votes') }}</th>
                                </tr>
                                </thead>
                                <tbody>

                                @foreach($userVotes as $id => $vote)
                                    <tr>
                                        <th scope="row">#{{ $id }}</th>
                                        <td>{{ $vote->user->name }}</td>
                                        <td>{{ $vote->votes }}</td>
                                    </tr>
                                @endforeach

                                </tbody>
                            </table>
                        @else
                            <div class="alert alert-info text-center" role="alert">
                                <i class="bi bi-info-circle"></i> {{ trans('vote::admin.votes.empty') }}
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

        </div>
    </div>

    <div class="row">
        <div class="col-xl-8 col-lg-7">
            <div class="card flex-fill w-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        {{ trans('vote::admin.votes.votes') }}
                    </h5>
                </div>
                <div class="card-body pt-2 pb-3">
                    <div class="tab-content mb-3">
                        <div class="tab-pane fade show active" id="monthlyChart" role="tabpanel"
                             aria-labelledby="monthlyChartTab">
                            <div class="chart">
                                <canvas id="votesPerMonthsChart"></canvas>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="dailyChart" role="tabpanel" aria-labelledby="dailyChartTab">
                            <div class="chart">
                                <canvas id="votesPerDaysChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <ul class="nav nav-pills" id="pills-tab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" id="monthlyChartTab" data-bs-toggle="pill" href="#monthlyChart"
                               role="tab" aria-controls="monthlyChart" aria-selected="true">
                                {{ trans('messages.range.months') }}
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="dailyChartTab" data-bs-toggle="pill" href="#dailyChart" role="tab"
                               aria-controls="dailyChart" aria-selected="false">
                                {{ trans('messages.range.days') }}
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('footer-scripts')
    <script src="{{ asset('vendor/chart.js/chart.umd.js') }}"></script>
    <script src="{{ asset('admin/js/charts.js') }}"></script>
    <script>
        createLineChart('votesPerMonthsChart', @json($votesPerMonths), '{{ trans('vote::admin.votes.votes') }}');
        createLineChart('votesPerDaysChart', @json($votesPerDays), '{{ trans('vote::admin.votes.votes') }}');
    </script>
@endpush
