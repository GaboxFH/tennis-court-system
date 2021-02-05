---
id: databaseDesign
title: Database Design
---

## Database Design
Reservations currently have these attributes: title, date, court, user_id
Users currently have have these attributes: name, email, password, password, remember_token, email_verified_at

There exists a many-to-many relationship between the Users and Reservations tables. 

A pivot table named reservations_users was created as a result of the many-to-many relationship. The pivot table has two columns, a Reservation ID and a User ID, to demonstrate which Reservation a User is a part of. Functions in both the User and Reservation Model were created to establish the many-to-many relationship.

One discrepancy arose from the idea that a User can be either a creator of a Reservation or a participant. To resolve this, a foreign key for a User (the creator) was added to the Reservation model.

If for any particular reason a User is deleted, it is ensured that any Reservation created by that specific User will also be deleted.

The onDelete('cascade') command achieves this.
```$table->foreignId('user_id')->constrained()->onDelete('cascade');```

Learn about inserting and removing data to pivot table
https://5balloons.info/pivot-table-and-many-to-many-relationship-in-laraval/

### Working with the Database in Laravel
```php artisan migrate:refresh```
This combines the migrate and rollback commands into one command.
As a result, the tables will be updated. However, the data that was in the table will be deleted. Therefore, if the data is important, the table should be backed up before a refresh is done.

Let's say a modification to a migration has to be made, such as the addition of a new column.
There are two choices to do this:
1. Directly edit the migration from before. This requires the migrations to be rolled back and migrated again (essentially refreshed). The is due to the act of migrating not automatically detecting modifications.
*Do not do this in production as it will delete all data in the database.*
2. Make a new migration and add, modify, remove the attributes of the table. Then, migrate this migration using artisan.
```php artisan migrate```

If a column needs to be modified (not changed or deleted), it requires a package doctrine/dbal. append ->change() to a column.
with foreign keys, this gets more tricky. The foreign key needs to be dropped first before a change is made.