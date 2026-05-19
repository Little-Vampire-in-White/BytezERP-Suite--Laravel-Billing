# Database Migration Commands & Changes

## Overview
Three new migrations were created to support the three new features. Below are the exact changes made to the database schema.

---

## Migration 1: Add Logo to Users Table

### File
`database/migrations/2025_12_05_000001_add_logo_to_users_table.php`

### What It Does
Adds a new column `logo_path` to the `users` table to store the path to each user's organization logo.

### SQL Equivalent
```sql
ALTER TABLE users ADD COLUMN logo_path VARCHAR(255) NULL AFTER password;
```

### Rollback SQL
```sql
ALTER TABLE users DROP COLUMN logo_path;
```

### Column Details
| Column    | Type        | Nullable | Default | After   |
|-----------|------------|----------|---------|---------|
| logo_path | VARCHAR(255) | YES     | NULL    | password |

---

## Migration 2: Add Currency to Invoices Table

### File
`database/migrations/2025_12_05_000002_add_currency_to_invoices_table.php`

### What It Does
Adds a new column `currency` to the `invoices` table to store the currency code for each invoice (USD, PKR, EUR, GBP, AED).

### SQL Equivalent
```sql
ALTER TABLE invoices ADD COLUMN currency VARCHAR(3) DEFAULT 'USD' AFTER total;
```

### Rollback SQL
```sql
ALTER TABLE invoices DROP COLUMN currency;
```

### Column Details
| Column   | Type        | Nullable | Default | After |
|----------|------------|----------|---------|-------|
| currency | VARCHAR(3) | NO       | 'USD'   | total |

### Allowed Values
- USD (US Dollar)
- PKR (Pakistani Rupee)
- EUR (Euro)
- GBP (British Pound)
- AED (UAE Dirham)

---

## Migration 3: Add User ID to Invoices Table

### File
`database/migrations/2025_12_05_000003_add_user_id_to_invoices_table.php`

### What It Does
Adds a new column `user_id` to the `invoices` table to link each invoice to the user who created it. This enables user-specific invoice viewing and authorization.

### SQL Equivalent
```sql
ALTER TABLE invoices ADD COLUMN user_id BIGINT UNSIGNED NULLABLE AFTER id;
ALTER TABLE invoices ADD FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE;
ALTER TABLE invoices ADD INDEX(user_id);
```

### Rollback SQL
```sql
ALTER TABLE invoices DROP FOREIGN KEY invoices_user_id_foreign;
ALTER TABLE invoices DROP COLUMN user_id;
```

### Column Details
| Column  | Type                | Nullable | Default | After |
|---------|-------------------|----------|---------|-------|
| user_id | BIGINT UNSIGNED   | YES      | NULL    | id    |

### Foreign Key
```
Constraint Name: invoices_user_id_foreign
References: users(id)
On Delete: CASCADE
On Update: RESTRICT
```

### Index
```
Index Name: invoices_user_id_index
Column: user_id
Type: BTREE
```

---

## Before & After Schema

### Users Table - BEFORE
```
id (bigint)
name (string)
email (string)
email_verified_at (timestamp)
password (string)
remember_token (string)
created_at (timestamp)
updated_at (timestamp)
```

### Users Table - AFTER
```
id (bigint)
name (string)
email (string)
email_verified_at (timestamp)
password (string)
remember_token (string)
logo_path (string) ← NEW
created_at (timestamp)
updated_at (timestamp)
```

---

### Invoices Table - BEFORE
```
id (bigint)
invoice_number (string)
client_id (bigint, foreign key)
invoice_date (date)
due_date (date)
status (enum)
subtotal (decimal)
tax (decimal)
total (decimal)
notes (text)
created_at (timestamp)
updated_at (timestamp)
```

### Invoices Table - AFTER
```
id (bigint)
user_id (bigint, foreign key) ← NEW
invoice_number (string)
client_id (bigint, foreign key)
invoice_date (date)
due_date (date)
status (enum)
subtotal (decimal)
tax (decimal)
total (decimal)
currency (string) ← NEW
notes (text)
created_at (timestamp)
updated_at (timestamp)
```

---

## Running the Migrations

### Command
```bash
php artisan migrate
```

### What Happens
1. Creates `migrations` table if not exists
2. Checks which migrations haven't been run
3. Executes the three new migration files in order:
   - 2025_12_05_000001_add_logo_to_users_table
   - 2025_12_05_000002_add_currency_to_invoices_table
   - 2025_12_05_000003_add_user_id_to_invoices_table
4. Records in `migrations` table that they ran
5. Returns success message

### Expected Output
```
Migrating: 2025_12_05_000001_add_logo_to_users_table
Migrated:  2025_12_05_000001_add_logo_to_users_table (0.05 seconds)
Migrating: 2025_12_05_000002_add_currency_to_invoices_table
Migrated:  2025_12_05_000002_add_currency_to_invoices_table (0.04 seconds)
Migrating: 2025_12_05_000003_add_user_id_to_invoices_table
Migrated:  2025_12_05_000003_add_user_id_to_invoices_table (0.06 seconds)
```

---

## Rolling Back Migrations

### All Three
```bash
php artisan migrate:rollback
```

### Just the Last One
```bash
php artisan migrate:rollback --step=1
```

### All Migrations (Nuclear Option)
```bash
php artisan migrate:reset
```

---

## Verifying Migrations Ran

### Check Migration Status
```bash
php artisan migrate:status
```

### SQL Query to Check Tables
```sql
-- Check users table
DESCRIBE users;
-- Should show logo_path column

-- Check invoices table
DESCRIBE invoices;
-- Should show user_id and currency columns

-- Check foreign keys
SELECT CONSTRAINT_NAME, TABLE_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
WHERE TABLE_NAME='invoices' AND COLUMN_NAME='user_id';
```

### SQL Query to Check Data
```sql
-- Count columns in users table
SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='users';
-- Should be 9 (was 8)

-- Count columns in invoices table
SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='invoices';
-- Should be 13 (was 11)
```

---

## Data Integrity

### Foreign Key Constraint
The foreign key constraint on `invoices.user_id` ensures:
- Each invoice_id must reference a valid user_id
- If a user is deleted, all their invoices are also deleted (CASCADE)
- You cannot assign an invoice to a non-existent user

### Default Values
- `users.logo_path` defaults to NULL (no logo)
- `invoices.currency` defaults to 'USD' (backward compatible)
- `invoices.user_id` can be NULL (for existing invoices from before migration)

---

## Existing Data Handling

### For Existing Invoices
```sql
-- Existing invoices will have:
-- user_id = NULL (need to populate manually or via script)
-- currency = 'USD' (default)

-- To assign all invoices to a user (example: user_id = 1):
UPDATE invoices SET user_id = 1 WHERE user_id IS NULL;

-- Or per-user if you know the relationships:
UPDATE invoices SET user_id = 1 WHERE client_id IN (SELECT id FROM clients WHERE user_id = 1);
```

### For Existing Users
```sql
-- Existing users will have:
-- logo_path = NULL (no logo)
-- This is fine - invoices will display without logo
```

---

## Performance Considerations

### Indexes
```sql
-- New index created on invoices.user_id for query performance
SELECT * FROM invoices WHERE user_id = ?  -- Fast (indexed)

-- Existing indexes unchanged
-- client_id still indexed for fast client lookups
```

### Query Performance
```
Before: SELECT * FROM invoices (returns ALL invoices)
After:  SELECT * FROM invoices WHERE user_id = ? (fast, uses index)
```

### Storage Impact
- `users.logo_path`: VARCHAR(255) ≈ 255 bytes per user
- `invoices.currency`: VARCHAR(3) ≈ 3 bytes per invoice
- `invoices.user_id`: BIGINT ≈ 8 bytes per invoice
- **Total minimal impact** (typical table gains <1MB for 10,000 records)

---

## Troubleshooting

### Error: "Column already exists"
```bash
# Migrations already ran
# Check migration status:
php artisan migrate:status

# Should show as "Ran" for all three migrations
```

### Error: "Foreign key constraint fails"
```sql
-- Check if users table has id column
-- Check if any invoices have non-existent user_ids
SELECT DISTINCT user_id FROM invoices WHERE user_id NOT IN (SELECT id FROM users);

-- Should return empty result
```

### Error: "Syntax error near ON DELETE CASCADE"
```bash
# Your MySQL version may not support foreign keys
# Ensure MySQL 5.7+ or MariaDB 10.2+
# Check version:
mysql --version

# Or check in Laravel:
php artisan tinker
>>> DB::connection()->getDoctrineConnection()->getWrappedConnection()->getServerVersion();
```

### Rollback Issues
```bash
# If rollback fails, manually reset:
php artisan migrate:reset
php artisan migrate

# Or manually drop columns:
# ALTER TABLE users DROP COLUMN logo_path;
# ALTER TABLE invoices DROP COLUMN currency;
# ALTER TABLE invoices DROP FOREIGN KEY invoices_user_id_foreign;
# ALTER TABLE invoices DROP COLUMN user_id;
```

---

## MySQL Version Requirements

### Minimum Requirements
- MySQL 5.7.8+ (for foreign key support)
- OR MariaDB 10.2+ (for foreign key support)

### Check Your Version
```bash
mysql --version
```

Or in your Laravel app:
```php
php artisan tinker
>>> DB::select('select version()')[0]->version()
```

---

## Backup Before Migration

### Full Database Backup
```bash
# MySQL
mysqldump -u username -p database_name > backup.sql

# With Docker
docker-compose exec mysql mysqldump -u username -p database_name > backup.sql
```

### Restore from Backup
```bash
# MySQL
mysql -u username -p database_name < backup.sql

# With Docker
docker-compose exec mysql mysql -u username -p database_name < backup.sql
```

---

## Summary Table

| Feature | Migration File | Database Changes | Affected Tables |
|---------|---|---|---|
| Logo Upload | 2025_12_05_000001 | Add logo_path column | users |
| Currency | 2025_12_05_000002 | Add currency column | invoices |
| User Authorization | 2025_12_05_000003 | Add user_id FK + column | invoices |

---

## Next Steps

After migrations run:

1. ✅ Migrations complete
2. Create storage symlink: `php artisan storage:link`
3. Test logo upload functionality
4. Test invoice creation with currency
5. Test user isolation and authorization
6. Verify PDFs display correctly

All database changes are complete and ready for application code!
