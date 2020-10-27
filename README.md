# Goldpillar

## Homologation server

IP: 104.248.216.49

## Branches

- master
- dev


## Init data

```sql
-- pass 123456
INSERT INTO security_users (id, email, phone, name, created_at, confirmed_at, password, roles) VALUES
(uuid(), 'admin@admin.com','1', '', now(), now(), '$argon2id$v=19$m=65536,t=4,p=1$29rdoYU07/Bt29c8SVuzgg$NgV+1PnvS8vCePza/2jnnmVH66lzF3fxbBES0rU+Sgk', '["ROLE_ADMIN"]');

INSERT INTO pages (id, name, title, description, banners) VALUES
(uuid(), 'sales', 'You dream Home', '', '[]'),
(uuid(), 'renting', 'Location! Location! Location!', '', '[]'),
(uuid(), 'projects', 'Hassle free Investments all over UK.', '', '[]'),
(uuid(), 'assets', 'Here you money work hard!', '', '[]');

INSERT INTO plans (id, created_at, active, name, code, price_currency, price_amount) VALUES
(uuid(), now(), true, 'Agent', 'agent', 'GBP', '0'),
(uuid(), now(), true, 'Investor', 'investor', 'GBP', '0'),
(uuid(), now(), true, 'Broker', 'broker', 'GBP', '0');
```