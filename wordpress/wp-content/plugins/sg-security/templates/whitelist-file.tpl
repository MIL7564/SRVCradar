<Files {FILENAME}>
	<IfModule !mod_authz_core.c>
		Allow from all
	</IfModule>
	<IfModule mod_authz_core.c>
		Require all granted
	</IfModule>
</Files>