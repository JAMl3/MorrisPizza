@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    {{ __('You are logged in!') }}
                </div>
            </div>
        </div>
    </div>
</div>

<audio id="notification-sound" src="{{ asset('sounds/notification.mp3') }}" preload="auto"></audio>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Notification listener initialized');
        
        // Pre-load the audio
        const audio = document.getElementById('notification-sound');
        audio.load();
        
        // Function to play notification sound
        async function playNotificationSound() {
            console.log('Attempting to play sound');
            try {
                audio.currentTime = 0;
                const playPromise = audio.play();
                if (playPromise !== undefined) {
                    playPromise.then(() => {
                        console.log('Sound played successfully');
                    }).catch(error => {
                        console.error('Error playing sound:', error);
                    });
                }
            } catch (error) {
                console.error('Error playing sound:', error);
            }
        }

        // Function to handle order updates
        function handleOrderUpdate() {
            console.log('Order update received');
            playNotificationSound();
            
            if (window.location.pathname.includes('/orders')) {
                console.log('On orders page, will refresh in 1 second');
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            }
        }

        // Listen for order notifications
        window.Echo.channel('orders')
            .listen('OrderCreated', (e) => {
                console.log('Order notification received:', e);
                handleOrderUpdate();
            });

        // Test sound button (for debugging)
        const testButton = document.createElement('button');
        testButton.textContent = 'Test Notification Sound';
        testButton.style.position = 'fixed';
        testButton.style.bottom = '20px';
        testButton.style.right = '20px';
        testButton.onclick = playNotificationSound;
        document.body.appendChild(testButton);
    });
</script>
@endpush
