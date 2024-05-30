serve:
	@git pull 
	start powershell -Command "php artisan schedule:work"
	@php artisan serve --host=0.0.0.0

ip: 
	@ipconfig | findstr IPv4

upload:
	@php artisan upload:data

coba:
	start powershell -Command "php artisan serve --host=0.0.0.0"
