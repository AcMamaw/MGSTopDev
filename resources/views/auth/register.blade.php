<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register - MGS</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="h-screen flex items-center justify-center bg-gray-100">

<div class="flex flex-col md:flex-row w-full max-w-2xl rounded-2xl shadow-xl overflow-hidden">

    <!-- Left: Image + Login Link (now slides from RIGHT) -->
    <div class="hidden md:flex md:w-1/2 bg-sky-200 flex-col items-center justify-center space-y-4 p-8
                transform translate-x-20 opacity-0 transition-all duration-700 ease-out slide-left">
        <img src="{{ asset('images/ace.jpg') }}" alt="Ace" class="object-contain w-45 h-45 -mt-14">
        
        <a href="{{ route('login') }}" 
           class="text-black underline font-semibold hover:text-sky-600 transition">
            Login
        </a>
    </div>

    <!-- Right: Register Form (now slides from LEFT) -->
    <div class="w-full md:w-1/2 p-8 flex flex-col justify-center bg-white
                transform -translate-x-20 opacity-0 transition-all duration-700 ease-out slide-right">
        <h2 class="text-2xl md:text-3xl font-bold text-gray-800 mb-6 text-center">Create Account</h2>

        <form action="{{ route('register.submit') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                <input type="text" name="username" id="username" placeholder="Create your username"
                    class="mt-1 block w-full px-4 py-2 border rounded-lg focus:ring-sky-500 focus:border-sky-500 border-gray-300"
                    value="{{ old('username') }}">
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" name="password" id="password" placeholder="••••••••"
                    class="mt-1 block w-full px-4 py-2 border rounded-lg focus:ring-sky-500 focus:border-sky-500 border-gray-300">
            </div>

            <button type="submit"
                class="w-full py-2 px-4 bg-sky-300 hover:bg-sky-700 text-black font-semibold rounded-lg transition">
                Register
            </button>
        </form>

        <p class="mt-6 text-sm text-gray-500 text-center">
            &copy; 2025 Marviles Graphic Studio
        </p>
    </div>

</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll(".slide-right, .slide-left").forEach(el => {
        el.classList.remove("-translate-x-20", "translate-x-20", "opacity-0");
    });
});
</script>

</body>
</html>
