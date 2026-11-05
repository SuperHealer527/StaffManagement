@extends('layouts.app')
@section('title', $title)
@section('content')
    @if(in_array($screen, ['staff-home', 'my-requests', 'assignment-response', 'attendance-confirmed', 'report-submit', 'payment-statement', 'profile', 'first-password', 'mobile-areas', 'interview-select', 'line-connect', 'temporary-bank'], true))
        @include('screens.partials.mobile')
    @elseif(in_array($screen, ['assignments', 'event-form', 'event-copy', 'event-detail'], true))
        @include('screens.partials.operations')
    @elseif(in_array($screen, ['payments', 'profit-loss'], true))
        @include('screens.partials.finance')
    @elseif(in_array($screen, ['clients', 'client-detail', 'events', 'staff', 'staff-detail', 'shortages', 'reports'], true))
        @include('screens.partials.directory')
    @elseif(in_array($screen, ['sales-home', 'proposal-board', 'meeting-history'], true))
        @include('screens.partials.sales')
    @else
        @include('screens.partials.admin')
    @endif
@endsection
