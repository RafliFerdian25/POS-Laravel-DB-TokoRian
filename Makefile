serve:
	@git pull
	@powershell -Command "Start-Process cmd -ArgumentList '/c php artisan schedule:work' -NoNewWindow"
	@php artisan serve --host=0.0.0.0

ip: 
	@ipconfig | findstr IPv4

upload:
	@php artisan upload:data
