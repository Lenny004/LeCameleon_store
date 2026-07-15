@extends('layouts.admin')

@section('title', 'Activity log')
@section('page-title', 'Activity log')
@section('page-subtitle', 'Latest admin actions')

@section('content')
<div class="table-wrap">
    <table class="table">
        <thead>
            <tr>
                <th>When</th>
                <th>User</th>
                <th>Action</th>
                <th>Subject</th>
                <th>IP</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($logs as $log)
                <tr>
                    <td>{{ $log->created_at?->format('Y-m-d H:i') }}</td>
                    <td>{{ $log->user?->email ?? '—' }}</td>
                    <td><code>{{ $log->action }}</code></td>
                    <td>
                        @if ($log->subject_type)
                            {{ class_basename($log->subject_type) }} #{{ Str::limit($log->subject_id, 8, '') }}
                        @else
                            —
                        @endif
                    </td>
                    <td>{{ $log->ip_address ?? '—' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">No activity recorded yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
