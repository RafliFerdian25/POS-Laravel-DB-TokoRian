serve:
	@git pull 
	@php artisan download:data
	@php artisan upload:data
	@php artisan serve --host=0.0.0.0

ip: 
	@ipconfig | findstr IPv4

upload:
	@php artisan upload:data

coba:
	start powershell -Command "php artisan serve --host=0.0.0.0"
