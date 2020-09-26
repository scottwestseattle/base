<div class="fixed top-0 right-0 px-6 py-4 sm:block">
	<a href="{{ route('frontpage') }}"		class="text-sm text-gray-700 underline">Home</a>
	@auth
		<a href="{{ route('dashboard') }}"	class="ml-4 text-sm text-gray-700 underline">Dashboard</a>
		@if (is_admin())
			<a href="{{ route('translations') }}" class="ml-4 text-sm text-gray-700 underline">Translations</a>
			<a href="/users" class="ml-4 text-sm text-gray-700 underline">Users</a>
		@endif
		<a href="{{ route('logout') }}" 	class="ml-4 text-sm text-gray-700 underline">{{Auth::user()->name}} (logout)</a>
	@else
		<a href="{{ route('login') }}" 		class="ml-4 text-sm text-gray-700 underline">Login</a>
		<a href="{{ route('register') }}"	class="ml-4 text-sm text-gray-700 underline">Register</a>
	@endif
	<a href="/about" class="ml-4 text-sm text-gray-700 underline">About</a>
</div>
