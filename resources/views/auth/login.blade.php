<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login - MGS</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="h-screen flex items-center justify-center bg-gray-100">

<div class="flex flex-col md:flex-row w-full max-w-2xl rounded-2xl shadow-xl overflow-hidden">

    <!-- Left: Login Form -->
    <div class="w-full md:w-1/2 p-8 flex flex-col justify-center bg-white
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
                <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                <input type="text" name="username" id="username" placeholder="Enter your username"
                    class="mt-1 block w-full px-4 py-2 border rounded-lg focus:ring-sky-500 focus:border-sky-500 border-gray-300"
                    value="{{ old('username') }}">
            </div>

            <!-- PASSWORD WITH EYE ICON -->
            <div class="relative">
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" name="password" id="password" placeholder="••••••••"
                    class="mt-1 block w-full px-4 py-2 border rounded-lg focus:ring-sky-500 focus:border-sky-500 border-gray-300 pr-10">

                <!-- Eye Icon Button -->
                <button type="button" id="togglePassword"
                    class="absolute right-3 bottom-3 text-gray-500 hover:text-gray-700">
                    <!-- Eye open -->
                    <svg id="eyeOpen" xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 
                            7-4.477 0-8.268-2.943-9.542-7z"/>
                        <circle cx="12" cy="12" r="3" stroke-width="2" stroke="currentColor"/>
                    </svg>

                    <!-- Eye closed -->
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
                class="w-full py-2 px-4 bg-sky-300 hover:bg-sky-700 text-white font-semibold rounded-lg transition">
                Login
            </button>
        </form>

        <p class="mt-6 text-sm text-gray-500 text-center">
            &copy; 2025 Marviles Graphic Studio
        </p>
    </div>

    <!-- Right: Image -->
    <div class="hidden md:flex md:w-1/2 bg-sky-200 flex-col items-center justify-center space-y-4
                transform -translate-x-20 opacity-0 transition-all duration-700 ease-out slide-left">
        <img src="{{ asset('images/ace.jpg') }}" alt="Ace" class="object-contain w-45 h-45 -mt-14">
        
        <a href="{{ route('register') }}" 
           class="text-black underline font-semibold hover:text-sky-600 transition">
        
        </a>
    </div>

</div>

<script>
document.addEventListener("DOMContentLoaded", () => {

    // Slide animation
    document.querySelectorAll('.slide-right, .slide-left')
        .forEach(el => el.classList.remove('-translate-x-20', 'translate-x-20', 'opacity-0'));

    // Password eye toggle
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

    // Admin bypass
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
