@extends('layouts.app')
@section('title', __('base.Site Map'))
@section('content')

<h1>{{__('base.Site Map')}}</h1>

<div class="sm:px-4 lg:px-8">

	<p><a href="/about">{{__('base.About')}}</a></p>
	<p><a href="/password/request-reset">{{__('base.Forgot Password')}}</a></p>
	<p><a href="/">{{__('base.Front Page')}}</a></p>
	<p><a href="/login">{{__('base.Log-in')}}</a></p>
	<p><a href="/users/register">{{__('base.Register')}}</a></p>

	<hr/>
	<h4>English</h4>
	<p><a href="/en/about">About</a></p>
	<p><a href="/en/contact">Contact</a></p>
	<p><a href="/en/privacy">Privacy Policy</a></p>
	<p><a href="/en/terms">Terms of Use</a></p>
	<p><a href="/en/password/request-reset">Forgot Password</a></p>
	<p><a href="/en/">Front Page</a></p>
	<p><a href="/en/login">Log-in</a></p>
	<p><a href="/en/users/register">Register</a></p>

	<hr/>
	<h4>Español</h4>
	<p><a href="/es/about">Sobre</a></p>
	<p><a href="/es/contact">Contacto</a></p>
	<p><a href="/es/privacy">Política de privacidad</a></p>
	<p><a href="/es/terms">Términos de uso</a></p>
	<p><a href="/es/password/request-reset">Recuperar contraseña</a></p>
	<p><a href="/es/">Página de inicio</a></p>
	<p><a href="/es/login">Iniciar sesión</a></p>
	<p><a href="/es/users/register">Registrarse</a></p>

	<hr/>
	<h4>中文</h4>
	<p><a href="/zh/about">关于</a></p>
	<p><a href="/zh/contact">联系</a></p>
	<p><a href="/zh/privacy">隐私政策</a></p>
	<p><a href="/zh/terms">使用条款</a></p>
	<p><a href="/zh/password/request-reset">忘记密码</a></p>
	<p><a href="/zh/">首页</a></p>
	<p><a href="/zh/login">登录</a></p>
	<p><a href="/zh/users/register">寄存器</a></p>

	@auth
	<hr/>
	<h4>{{trans_choice('base.User', 1)}}</h4>
	<p><a href="/dashboard">{{__('base.Dashboard')}}</a></p>
	<p><a href="/users/edit/{{Auth::id()}}">{{__('base.Edit Profile')}}</a></p>
	<p><a href="/users/view/{{Auth::id()}}">{{__('base.Profile')}}</a></p>
	<p><a href="/password/edit/{{Auth::id()}}">{{__('base.Update Password')}}</a></p>
	<p><a href="/logout">{{__('base.Log-out')}}</a></p>

	@if (isAdmin())
		<hr/>
		<h4>{{__('base.Admin')}}</h4>
		<p><a href="/events/confirmdelete">{{__('base.Delete Events')}}</a></p>
		<p><a href="/events">{{trans_choice('base.Event', 2)}}</a></p>
		<p><a href="/translations">{{trans_choice('base.Translation', 2)}}</a></p>
		<p><a href="/users">{{trans_choice('base.User', 2)}}</a></p>
	@endif

	@endauth
</div>

@endsection
