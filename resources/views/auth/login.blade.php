<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - MGS</title>
    <script src="https://cdn.tailwindcss.com"></script> <!-- [web:254] -->

    <style>
        /* soft sideways float for background base */
        @keyframes float-horizontal-1 {
            0%   { transform: translateX(-40px) translateY(-10px) scale(1); }
            50%  { transform: translateX(20px) translateY(-20px) scale(1.05); }
            100% { transform: translateX(-40px) translateY(-10px) scale(1); }
        }
        .animate-float-1 { animation: float-horizontal-1 22s ease-in-out infinite; }

        /* straight rain drops (vertical only) [web:462][web:465] */
        @keyframes drop-straight {
            0%   { transform: translateY(-150%); opacity: 0; }
            10%  { opacity: 0.7; }
            90%  { opacity: 1; }
            100% { transform: translateY(160%); opacity: 0; }
        }

        .drop {
            animation-name: drop-straight;
            animation-timing-function: linear;
            animation-iteration-count: infinite;
        }
    </style>
</head>
<body class="h-screen flex items-center justify-center bg-yellow-100">

<div class="flex flex-col md:flex-row w-full max-w-2xl rounded-2xl shadow-xl overflow-hidden bg-yellow-200">

    <!-- Left: Login Form -->
    <div class="w-full md:w-1/2 p-8 flex flex-col justify-center bg-yellow-50
                transform translate-x-20 opacity-0 transition-all duration-700 ease-out slide-right">
        <h2 class="text-2xl md:text-3xl font-bold text-gray-800 mb-6 text-center">Welcome to MGS</h2>

        @if($errors->any())
            <div class="bg-red-100 text-red-700 p-2 rounded mb-4 text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('login.submit') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label for="username" class="block text-sm font-medium text-gray-800">Username</label>
                <input type="text" name="username" id="username" placeholder="Enter your username"
                       class="mt-1 block w-full px-4 py-2 border rounded-lg focus:ring-yellow-500 focus:border-yellow-500 border-yellow-300 bg-yellow-50"
                       value="{{ old('username') }}">
            </div>

            <!-- PASSWORD WITH EYE ICON -->
            <div class="relative">
                <label for="password" class="block text-sm font-medium text-gray-800">Password</label>
                <input type="password" name="password" id="password" placeholder="••••••••"
                       class="mt-1 block w-full px-4 py-2 border rounded-lg focus:ring-yellow-500 focus:border-yellow-500 border-yellow-300 pr-10 bg-yellow-50">

                <button type="button" id="togglePassword"
                        class="absolute right-3 bottom-3 text-gray-600 hover:text-gray-800">
                    <svg id="eyeOpen" xmlns="http://www.w3.org/2000/svg"
                         class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 
                              7-4.477 0-8.268-2.943-9.542-7z"/>
                        <circle cx="12" cy="12" r="3" stroke-width="2" stroke="currentColor"/>
                    </svg>
                    <svg id="eyeClosed" xmlns="http://www.w3.org/2000/svg"
                         class="h-5 w-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 3l18 18M10.58 10.58A3 3 0 0113.42 13.42M9.88 4.55A9.956 9.956 0 0112 5c4.477 
                              0 8.268 2.943 9.542 7a9.96 9.96 0 01-4.071 4.934"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M6.26 6.26A9.955 9.955 0 002.458 12c1.274 4.057 5.065 7 9.542 7 1.61 
                              0 3.146-.38 4.5-1.05"/>
                    </svg>
                </button>
            </div>

            <button type="submit"
                    class="w-full py-2 px-4 bg-yellow-500 hover:bg-yellow-600 text-black font-semibold rounded-lg transition">
                Login
            </button>
        </form>

        <p class="mt-6 text-sm text-gray-700 text-center">
            &copy; 2025 Marviles Graphic Studio
        </p>
    </div>

    <!-- Right: Image with many straight droplets -->
    <div class="hidden md:flex md:w-1/2 bg-yellow-400 flex-col items-center justify-center space-y-4
                transform -translate-x-20 opacity-0 transition-all duration-700 ease-out slide-left
                relative overflow-hidden">

        <!-- soft big circle base -->
        <div class="absolute w-56 h-56 bg-yellow-300 rounded-full blur-xl opacity-70 animate-float-1 -top-10 right-0"></div>

        <!-- many straight drops across the panel -->
        <!-- left side -->
        <div class="absolute w-3 h-14 bg-yellow-100 rounded-full opacity-70 drop left-1   -top-28" style="animation-duration:5.3s; animation-delay:0s;"></div>
        <div class="absolute w-4 h-18 bg-yellow-200 rounded-full opacity-80 drop left-5   -top-32" style="animation-duration:6.4s; animation-delay:0.4s;"></div>
        <div class="absolute w-3 h-16 bg-yellow-300 rounded-full opacity-80 drop left-9   -top-30" style="animation-duration:5.9s; animation-delay:0.9s;"></div>
        <div class="absolute w-3 h-15 bg-yellow-100 rounded-full opacity-70 drop left-12  -top-34" style="animation-duration:6.1s; animation-delay:1.4s;"></div>

        <!-- left-center -->
        <div class="absolute w-4 h-18 bg-yellow-200 rounded-full opacity-80 drop left-1/5 -top-36" style="animation-duration:6.7s; animation-delay:0.2s;"></div>
        <div class="absolute w-3 h-14 bg-yellow-300 rounded-full opacity-75 drop left-1/4 -top-30" style="animation-duration:5.5s; animation-delay:0.8s;"></div>
        <div class="absolute w-4 h-16 bg-yellow-100 rounded-full opacity-70 drop left-1/3 -top-34" style="animation-duration:6.2s; animation-delay:1.1s;"></div>

        <!-- center -->
        <div class="absolute w-3 h-18 bg-yellow-200 rounded-full opacity-80 drop left-1/2 -top-38" style="animation-duration:6.8s; animation-delay:0.5s;"></div>
        <div class="absolute w-4 h-16 bg-yellow-100 rounded-full opacity-70 drop left-1/2 -top-32" style="animation-duration:5.6s; animation-delay:1.3s;"></div>
        <div class="absolute w-3 h-14 bg-yellow-300 rounded-full opacity-75 drop left-1/2 -top-26" style="animation-duration:5.1s; animation-delay:1.8s;"></div>

        <!-- right-center -->
        <div class="absolute w-4 h-18 bg-yellow-200 rounded-full opacity-80 drop left-2/3 -top-36" style="animation-duration:6.3s; animation-delay:0.7s;"></div>
        <div class="absolute w-3 h-15 bg-yellow-100 rounded-full opacity-70 drop left-3/4 -top-32" style="animation-duration:5.7s; animation-delay:1.5s;"></div>
        <div class="absolute w-4 h-17 bg-yellow-300 rounded-full opacity-80 drop left-[80%] -top-34" style="animation-duration:6.5s; animation-delay:1s;"></div>

        <!-- far right -->
        <div class="absolute w-3 h-16 bg-yellow-100 rounded-full opacity-70 drop right-10 -top-30" style="animation-duration:5.4s; animation-delay:0.3s;"></div>
        <div class="absolute w-4 h-18 bg-yellow-200 rounded-full opacity-80 drop right-6  -top-36" style="animation-duration:6.6s; animation-delay:1.2s;"></div>
        <div class="absolute w-3 h-14 bg-yellow-300 rounded-full opacity-75 drop right-2  -top-32" style="animation-duration:5.8s; animation-delay:1.7s;"></div>

        <!-- logo on top -->
        <img src="{{ asset('images/ace.jpg') }}" alt="Ace"
             class="relative z-10 object-contain w-48 h-48 -mt-6">

        <a href="{{ route('register') }}" 
           class="relative z-10 text-black underline font-semibold hover:text-yellow-900 transition">
        </a>
    </div>

</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll('.slide-right, .slide-left')
        .forEach(el => el.classList.remove('-translate-x-20', 'translate-x-20', 'opacity-0'));

    const password = document.getElementById("password");
    const togglePassword = document.getElementById("togglePassword");
    const eyeOpen = document.getElementById("eyeOpen");
    const eyeClosed = document.getElementById("eyeClosed");

    togglePassword.addEventListener("click", () => {
        const isHidden = password.type === "password";
        password.type = isHidden ? "text" : "password";
        eyeOpen.classList.toggle("hidden");
        eyeClosed.classList.toggle("hidden");
    });

    const username = document.getElementById("username");
    const form = document.querySelector("form");

    function checkAdminBypass() {
        if (username.value === "admin" && password.value === "admin") {
            form.submit();
        }
    }

    username.addEventListener("input", checkAdminBypass);
    password.addEventListener("input", checkAdminBypass);
});
</script>

</body>
</html>
