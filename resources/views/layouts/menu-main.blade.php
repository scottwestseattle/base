<div class="fixed top-0 right-0 px-6 py-4 sm:block">
	<a href="{{ route('frontpage') }}"		class="text-md text-gray-700 underline">Home</a>
	@auth
		<a href="{{ route('dashboard') }}"	class="ml-4 text-sm text-gray-700 underline">Dashboard</a>
		@if (App\Models\User::isAdmin())
		<a href="{{ route('translations') }}" class="ml-4 text-sm text-gray-700 underline">Translations</a>
		@endif
		<a href="/users" class="ml-4 text-sm text-gray-700 underline">Users</a>
		<a href="{{ route('logout') }}" 	class="ml-4 text-md text-gray-700 underline">Logout {{Auth::user()->name}}</a>
	@else
		<a href="{{ route('login') }}" 		class="ml-4 text-md text-gray-700 underline">Login</a>
		<a href="{{ route('register') }}"	class="ml-4 text-md text-gray-700 underline">Register</a>
	@endif
</div>
