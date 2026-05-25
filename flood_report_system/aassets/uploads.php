# .htaccess di folder assets/uploads
Options -Indexes
<FilesMatch "\.(jpg|jpeg|png|gif)$">
    Order Allow,Deny
    Allow from all
</FilesMatch>