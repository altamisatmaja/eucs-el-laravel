<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Aplikasi Perhitungan | @yield('title')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="shortcut icon" href="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRafGZ3XFeeYTanFCPKKzChCX_1rbFcI6AswPrJewraEWR_6a8OTad1ohdh2VjIbaRnyIg&usqp=CAU" type="image/x-icon">
    <style>
        .notification {
            animation: slideIn 0.3s ease-out;
        }

        .notification.fade-out {
            animation: fadeOut 0.3s ease-out forwards;
        }

        @keyframes slideIn {
            from {
                transform: translate(100%, 0);
                opacity: 0;
            }

            to {
                transform: translate(0, 0);
                opacity: 1;
            }
        }

        @keyframes fadeOut {
            from {
                transform: translate(0, 0);
                opacity: 1;
            }

            to {
                transform: translate(100%, 0);
                opacity: 0;
            }
        }
    </style>
</head>

<body>
    <!-- Notification Container - Fixed position bottom-right -->
    <div id="notification-container" class="fixed bottom-4 right-4 z-50 space-y-3 max-w-sm w-full">

        @if (session('error'))
            <div
                class="notification bg-red-500 text-white px-6 py-4 rounded-lg shadow-lg border-l-4 border-red-700 flex items-center justify-between">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle mr-3 text-lg"></i>
                    <span class="font-medium">{{ session('error') }}</span>
                </div>
                <button onclick="closeNotification(this)" class="ml-4 text-white hover:text-red-200 focus:outline-none">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
        @endif

        @if (session('success'))
            <div
                class="notification bg-green-500 text-white px-6 py-4 rounded-lg shadow-lg border-l-4 border-green-700 flex items-center justify-between">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-3 text-lg"></i>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
                <button onclick="closeNotification(this)"
                    class="ml-4 text-white hover:text-green-200 focus:outline-none">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
        @endif

        @if (session('message'))
            <div
                class="notification bg-blue-500 text-white px-6 py-4 rounded-lg shadow-lg border-l-4 border-blue-700 flex items-center justify-between">
                <div class="flex items-center">
                    <i class="fas fa-info-circle mr-3 text-lg"></i>
                    <span class="font-medium">{{ session('message') }}</span>
                </div>
                <button onclick="closeNotification(this)"
                    class="ml-4 text-white hover:text-blue-200 focus:outline-none">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
        @endif

        @if ($errors->any())
            <div class="notification bg-red-500 text-white px-6 py-4 rounded-lg shadow-lg border-l-4 border-red-700">
                <div class="flex items-start justify-between">
                    <div class="flex items-start">
                        <i class="fas fa-exclamation-circle mr-3 text-lg mt-0.5"></i>
                        <div>
                            <div class="font-medium mb-2">Please fix the following errors:</div>
                            <ul class="list-disc list-inside space-y-1 text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <button onclick="closeNotification(this)"
                        class="ml-4 text-white hover:text-red-200 focus:outline-none flex-shrink-0">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
            </div>
        @endif

    </div>

    @include('layouts.navbar')
    @yield('content')

    <script src="https://unpkg.com/@heroicons/react@1.0.5/outline.js" crossorigin></script>
    <script>
        const mobileMenuButton = document.querySelector('.mobile-menu-button');
        const mobileMenu = document.querySelector('.mobile-menu');

        if (mobileMenuButton && mobileMenu) {
            mobileMenuButton.addEventListener('click', function() {
                mobileMenu.classList.toggle('hidden');
            });
        }


        function closeNotification(button) {
            const notification = button.closest('.notification');
            notification.classList.add('fade-out');

            setTimeout(() => {
                notification.remove();
            }, 300);
        }


        function autoCloseNotifications() {
            const notifications = document.querySelectorAll('.notification');

            notifications.forEach(notification => {
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.classList.add('fade-out');
                        setTimeout(() => {
                            if (notification.parentNode) {
                                notification.remove();
                            }
                        }, 300);
                    }
                }, 5000);
            });
        }


        document.addEventListener('DOMContentLoaded', function() {
            autoCloseNotifications();
        });


        function addProgressBar(notification) {
            const progressBar = document.createElement('div');
            progressBar.className =
                'absolute bottom-0 left-0 h-1 bg-white bg-opacity-30 transition-all duration-5000 ease-linear';
            progressBar.style.width = '100%';

            notification.style.position = 'relative';
            notification.appendChild(progressBar);


            setTimeout(() => {
                progressBar.style.width = '0%';
            }, 100);
        }


        document.addEventListener('DOMContentLoaded', function() {
            const notifications = document.querySelectorAll('.notification');

            notifications.forEach(notification => {
                addProgressBar(notification);

                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.classList.add('fade-out');
                        setTimeout(() => {
                            if (notification.parentNode) {
                                notification.remove();
                            }
                        }, 300);
                    }
                }, 5000);
            });
        });
    </script>

    @yield('scripts')
</body>

</html>
