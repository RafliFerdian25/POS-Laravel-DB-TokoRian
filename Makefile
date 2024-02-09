serve:
	ipconfig | findstr IPv4
	@php artisan serve --host=0.0.0.0