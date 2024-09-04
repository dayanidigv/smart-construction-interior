<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-US" lang="en-US">

<head>
    <meta charset="utf-8">
    <title>Smart Construction And Interiors</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="handheldfriendly" content="true" />
    <meta name="MobileOptimized" content="width" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Theme Style -->
    <link rel="stylesheet" type="text/css" href="{{ asset('css/style.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/notify.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/icons/themify-icons/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('/libs/sweetalert2/dist/sweetalert2.min.css') }}">
    <!-- Include jQuery and jQuery UI (for date picker) -->
    <!-- <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css"> -->
    <!-- <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.5.0/css/bootstrap-datepicker.css" rel="stylesheet">   -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">


    @stack('style')
    <!-- Favicon and Touch Icons  -->
    <link rel="shortcut icon" href="{{ asset('images\logo\logo-2.png') }}">
    <link rel="apple-touch-icon-precomposed" href="{{ asset('images\logo\logo-2.png') }}">
</head>

<body class="body">

    <!-- Preloader -->
    <div class="preloader">
        <img src="{{ asset('images\logo\logo-2.png') }}" alt="loader" class="lds-ripple img-fluid" />
    </div>


    @yield('content')

    <!-- Javascript -->
    <!--  Import Js Files -->
    <script src="{{ asset('libs/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{ asset('libs/simplebar/dist/simplebar.min.js') }}"></script>
    <script src="{{ asset('libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
    
    <!-- <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script> -->
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.5.0/js/bootstrap-datepicker.js"></script>   -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>


    <!--  core files -->
    <script src="{{ asset('js/app.min.js') }}"></script>
    <script src="{{ asset('js/app.init.js') }}"></script>
    <script src="{{ asset('js/sidebarmenu.js') }}"></script>
    <script src="{{ asset('js/custom.js') }}"></script>
    <script src="{{ asset('js/notify.js') }}"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var alertList = document.querySelectorAll('.alert');
        alertList.forEach(function(alert) {
            new bootstrap.Alert(alert);
        });
    });
    </script>
    @stack('script')

    @use('Illuminate\Support\Facades\Auth;')

    @if (Auth::check())
    @if (!$displayReminder->isEmpty())

    <script src="{{url('/libs/sweetalert2/dist/sweetalert2.min.js')}}"></script>

@php
if (!empty($displayReminder)) {
    $reminders = [];
    foreach ($displayReminder as $reminder) {
        $reminderTime = strtotime($reminder->reminder_time);
        $currentTime = time();
        $timeDifference = max($reminderTime - $currentTime, 0);
        $timeInMilliseconds = $timeDifference * 1000;
        
        $reminders[] = [
            'id' => base64_encode($reminder->id),
            'title' => htmlspecialchars($reminder->title, ENT_QUOTES, 'UTF-8'),
            'description' => htmlspecialchars($reminder->description, ENT_QUOTES, 'UTF-8'),
            'timeInMilliseconds' => $timeInMilliseconds,
        ];
    }
@endphp

<script>
document.addEventListener('DOMContentLoaded', () => {
    const reminders = @json($reminders);

    reminders.forEach(reminder => {
        setTimeout(() => {
            Swal.fire({
                title: '<span class="text-warning">Reminder Alert!</span>',
                html: `<h4>${reminder.title}</h4><p>${reminder.description}</p>`,
                showCancelButton: true,
                confirmButtonColor: '#DD6B55',
                confirmButtonText: 'Mark as Completed',
                cancelButtonText: 'Snooze',
                allowOutsideClick: false
            }).then((result) => {
                if (result.isConfirmed) {
                    completeReminder(reminder.id);
                }
            }).catch(error => {
                console.error('Error displaying the reminder:', error);
            });
        }, reminder.timeInMilliseconds);
    });
});

function completeReminder(id) {
    fetch("{{ url('/reminder/is_completed') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ id })
    }).then(response => response.json()).then(data => {
        if (data.success) {
            Swal.fire({
                title: 'Completed!',
                text: 'The reminder has been marked as completed.',
                icon: 'success',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'OK'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.reload();
                }
            });
        } else {
            Swal.fire('Error!', 'There was an error marking the reminder as completed.', 'error');
        }
    }).catch(error => {
        console.error('Error marking the reminder as completed:', error);
    });
}
</script>

@php
}
@endphp


    @endif
    @endif

    <!-- Display Notify -->
    <!-- @php
    $notifyConfigs = [
    'message' => ['status' => 'success', 'title' => session('message'), 'text' => '', 'autoclose' => false],
    'success' => ['status' => 'success', 'title' => session('success'), 'text' => '', 'autoclose' => false],
    'error' => ['status' => 'error', 'title' => session('error'), 'text' => '', 'autoclose' => false]
    ];
    @endphp -->

    <!-- @foreach($notifyConfigs as $key => $config)
    @if(session($key))
    <script>
    new Notify({
        status: "{{ $config['status'] }}",
        title: "{{ $config['title'] }}",
        autoclose: true,
        autotimeout: 10000,
        effect: "slide",
        speed: 300,
        position: "right bottom"
    });

    
    </script>
    @endif
    @endforeach -->

</body>

</html>