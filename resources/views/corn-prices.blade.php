@extends('dashboard')

@section('control-content')
    <h1 class="text-center mb-4">Corn Dec 2025 Futures</h1>
    
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-orange">
            Current Futures Price
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h3 class="price-display">$<?php echo e(number_format((float)$futuresPrice, 2)); ?></h3>
                    <p class="text-muted">Last Updated: {{ $lastUpdated }}</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <span class="badge bg-{{ $futuresPrice > 500 ? 'success' : 'warning' }}">
                        {{ $futuresPrice > 500 ? 'In the Money' : 'Out of the Money' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mt-4">
        <div class="card-header bg-secondary text-orange">
            500 Strike Put Option
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Bid:</strong> ${{ $bid }}</p>
                    <p><strong>Ask:</strong> ${{ $ask }}</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="text-muted">Option data not currently available</p>
                </div>
            </div>
        </div>
    </div>

    @if (isset($error))
        <div class="alert alert-danger mt-4">{{ $error }}</div>
    @endif

    <div class="text-center mt-4">
        <a href="{{ route('schwab.auth') }}" class="btn btn-outline-primary">Re-authenticate</a>
    </div>
</div>

<style>
    .container {
        max-width: 700px;
        margin-top: 30px;
    }
    .card {
        border: none;
        border-radius: 10px;
    }
    .card-header {
        font-size: 1.2rem;
        padding: 12px;
    }
    .card-body {
        padding: 20px;
    }
    .price-display {
        font-size: 2.5rem;
        font-weight: bold;
        color: #2c3e50;
        margin-bottom: 10px;
    }
    .badge {
        font-size: 1rem;
        padding: 8px 12px;
    }
    .btn {
        padding: 10px 20px;
        font-size: 1rem;
    }
    .text-muted {
        font-size: 0.9rem;
    }
</style>
@endsection
