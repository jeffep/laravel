@extends('layouts.app_gtouch')

@section('content')
    <div class="full-screen">
         <a href="{{ route('touch.clock') }}" class="back-arrow"> </a>
        <!-- TradingView Widget -->
        <div style="width: 100%; height: 80%;">
            <script type="text/javascript" src="https://s3.tradingview.com/tv.js"></script>
            <script type="text/javascript">
                window.onload = function() {
                new TradingView.widget({
                    "width": "100%",
                    "height": "100%",
                    "symbol": "CBOT:ZC1!", // Corn Futures
                    "interval": "D",
                    "timezone": "Etc/UTC",
                    "theme": "light",
                    "style": "1",
                    "locale": "en",
                    "toolbar_bg": "#f1f3f6",
                    "enable_publishing": false,
                    "allow_symbol_change": false,
                    "container_id": "tradingview_corn"
                });
                };
            </script>
            <div id="tradingview_corn"></div>
        </div>
        <a href="{{ route('sensor-data') }}" class="next-arrow">➡️</a>
    </div>
@endsection
