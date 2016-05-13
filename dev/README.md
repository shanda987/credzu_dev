#Local Development

In the Root folder run this for file uploads:
```
sudo chgrp -R www-data *
```

If you import jesse.sql the logins are as follows:

    admin:admin
    user:user
    company:company
    staff:staff

Anytime metadata is added to `class-mjobProfile.php`, place this in a view file to add to
the DB:

    init_user_roles()
