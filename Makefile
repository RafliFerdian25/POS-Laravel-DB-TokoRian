serve:
	@git pull 
	@php artisan serve --host=0.0.0.0

ip: 
	@ipconfig | findstr IPv4