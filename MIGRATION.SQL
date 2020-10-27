CREATE TABLE user_balance (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, description VARCHAR(255) NOT NULL, amount DOUBLE PRECISION NOT NULL, created_at DATETIME NOT NULL, processed_at DATETIME DEFAULT NULL, INDEX IDX_F4F901F4A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB;
CREATE TABLE application_settings (id INT AUTO_INCREMENT NOT NULL, application_id INT NOT NULL, comission_rate DOUBLE PRECISION NOT NULL, INDEX IDX_5A7E7FD63E030ACD (application_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB;
ALTER TABLE user_balance ADD CONSTRAINT FK_F4F901F4A76ED395 FOREIGN KEY (user_id) REFERENCES security_user (id);
ALTER TABLE application_settings ADD CONSTRAINT FK_5A7E7FD63E030ACD FOREIGN KEY (application_id) REFERENCES security_application (id);

 The following SQL statements will be executed:

     ALTER TABLE agent ADD comission_rate DOUBLE PRECISION NOT NULL;

 The following SQL statements will be executed:

     ALTER TABLE property ADD comission_rate DOUBLE PRECISION NOT NULL;
     ALTER TABLE agent DROP comission_rate;

 The following SQL statements will be executed:

     CREATE TABLE plan (id INT AUTO_INCREMENT NOT NULL, current_price DOUBLE PRECISION NOT NULL, created_at DATETIME NOT NULL, active TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB;
     CREATE TABLE plan_application (plan_id INT NOT NULL, application_id INT NOT NULL, INDEX IDX_50E205AAE899029B (plan_id), INDEX IDX_50E205AA3E030ACD (application_id), PRIMARY KEY(plan_id, application_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB;
     ALTER TABLE plan_application ADD CONSTRAINT FK_50E205AAE899029B FOREIGN KEY (plan_id) REFERENCES plan (id) ON DELETE CASCADE;
     ALTER TABLE plan_application ADD CONSTRAINT FK_50E205AA3E030ACD FOREIGN KEY (application_id) REFERENCES security_application (id) ON DELETE CASCADE;

 The following SQL statements will be executed:

     ALTER TABLE plan ADD role_id INT NOT NULL;
     ALTER TABLE plan ADD CONSTRAINT FK_DD5A5B7DD60322AC FOREIGN KEY (role_id) REFERENCES security_role (id);
     CREATE INDEX IDX_DD5A5B7DD60322AC ON plan (role_id);

 The following SQL statements will be executed:

     ALTER TABLE plan ADD description VARCHAR(255) NOT NULL;

 The following SQL statements will be executed:

     ALTER TABLE plan ADD application_id INT NOT NULL;
     ALTER TABLE plan ADD CONSTRAINT FK_DD5A5B7D3E030ACD FOREIGN KEY (application_id) REFERENCES security_application (id);
     CREATE INDEX IDX_DD5A5B7D3E030ACD ON plan (application_id);

 The following SQL statements will be executed:

     CREATE TABLE subscription (id INT AUTO_INCREMENT NOT NULL, plano_id INT NOT NULL, subscribed_at DATETIME NOT NULL, unsubscribed_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, valid_to DATETIME NOT NULL, INDEX IDX_A3C664D39A8B86CC (plano_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB;
     ALTER TABLE subscription ADD CONSTRAINT FK_A3C664D39A8B86CC FOREIGN KEY (plano_id) REFERENCES plan (id);

 The following SQL statements will be executed:

     ALTER TABLE subscription DROP FOREIGN KEY FK_A3C664D39A8B86CC;
     DROP INDEX IDX_A3C664D39A8B86CC ON subscription;
     ALTER TABLE subscription CHANGE plano_id plan_id INT NOT NULL;
     ALTER TABLE subscription ADD CONSTRAINT FK_A3C664D3E899029B FOREIGN KEY (plan_id) REFERENCES plan (id);
     CREATE INDEX IDX_A3C664D3E899029B ON subscription (plan_id);

 The following SQL statements will be executed:

     ALTER TABLE plan DROP FOREIGN KEY FK_DD5A5B7DD60322AC;
     DROP INDEX IDX_DD5A5B7DD60322AC ON plan;
     ALTER TABLE plan CHANGE role_id plan_type_id INT NOT NULL;
     ALTER TABLE plan ADD CONSTRAINT FK_DD5A5B7D7BF3C49B FOREIGN KEY (plan_type_id) REFERENCES table_value (id);
     CREATE INDEX IDX_DD5A5B7D7BF3C49B ON plan (plan_type_id);

 The following SQL statements will be executed:

     ALTER TABLE plan DROP FOREIGN KEY FK_DD5A5B7D7BF3C49B;
     DROP INDEX IDX_DD5A5B7D7BF3C49B ON plan;
     ALTER TABLE plan DROP plan_type_id, CHANGE description name VARCHAR(255) NOT NULL;

 [OK] Nothing to update - your database is already in sync with the current entity metadata.                            


 The following SQL statements will be executed:

     ALTER TABLE plan ADD code VARCHAR(255) NOT NULL;

 The following SQL statements will be executed:

     DROP INDEX IDX_8BF21CDE3414710B ON property;
     ALTER TABLE property ADD user_id INT NOT NULL, DROP agent_id;
     ALTER TABLE property ADD CONSTRAINT FK_8BF21CDEA76ED395 FOREIGN KEY (user_id) REFERENCES security_user (id);
     CREATE INDEX IDX_8BF21CDEA76ED395 ON property (user_id);

 The following SQL statements will be executed:

     CREATE TABLE plan (id INT AUTO_INCREMENT NOT NULL, application_id INT NOT NULL, current_price DOUBLE PRECISION NOT NULL, created_at DATETIME NOT NULL, active TINYINT(1) NOT NULL, name VARCHAR(255) NOT NULL, code VARCHAR(255) NOT NULL, INDEX IDX_DD5A5B7D3E030ACD (application_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB;
     CREATE TABLE subscription (id INT AUTO_INCREMENT NOT NULL, plan_id INT NOT NULL, subscribed_at DATETIME NOT NULL, unsubscribed_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, valid_to DATETIME NOT NULL, INDEX IDX_A3C664D3E899029B (plan_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB;
     ALTER TABLE plan ADD CONSTRAINT FK_DD5A5B7D3E030ACD FOREIGN KEY (application_id) REFERENCES security_application (id);
     ALTER TABLE subscription ADD CONSTRAINT FK_A3C664D3E899029B FOREIGN KEY (plan_id) REFERENCES plan (id);
     DROP INDEX IDX_8BF21CDE3414710B ON property;
     ALTER TABLE property ADD user_id INT NOT NULL, DROP agent_id;
     ALTER TABLE property ADD CONSTRAINT FK_8BF21CDEA76ED395 FOREIGN KEY (user_id) REFERENCES security_user (id);
     CREATE INDEX IDX_8BF21CDEA76ED395 ON property (user_id);
     ALTER TABLE log_changes ADD field_type VARCHAR(255) NOT NULL, CHANGE created_at created_at DATETIME NOT NULL;
     ALTER TABLE transaction ADD month_ref INT NOT NULL, ADD year_ref INT NOT NULL;

 The following SQL statements will be executed:

     ALTER TABLE subscription ADD user_id INT NOT NULL;
     ALTER TABLE subscription ADD CONSTRAINT FK_A3C664D3A76ED395 FOREIGN KEY (user_id) REFERENCES security_user (id);
     CREATE INDEX IDX_A3C664D3A76ED395 ON subscription (user_id);

 The following SQL statements will be executed:

     CREATE TABLE invoice (id INT AUTO_INCREMENT NOT NULL, subscription_id INT NOT NULL, plan_history_id INT NOT NULL, user_data VARCHAR(500) NOT NULL, start_at DATETIME NOT NULL, end_at DATETIME NOT NULL, description VARCHAR(255) NOT NULL, amount DOUBLE PRECISION NOT NULL, due_date DATETIME NOT NULL, paid_at DATETIME DEFAULT NULL, INDEX IDX_906517449A1887DC (subscription_id), INDEX IDX_906517447368BE44 (plan_history_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB;
     ALTER TABLE invoice ADD CONSTRAINT FK_906517449A1887DC FOREIGN KEY (subscription_id) REFERENCES subscription (id);
     ALTER TABLE invoice ADD CONSTRAINT FK_906517447368BE44 FOREIGN KEY (plan_history_id) REFERENCES plan_history (id);
     ALTER TABLE subscription ADD CONSTRAINT FK_A3C664D3A76ED395 FOREIGN KEY (user_id) REFERENCES security_user (id);
     CREATE INDEX IDX_A3C664D3A76ED395 ON subscription (user_id);

 The following SQL statements will be executed:

     ALTER TABLE subscription ADD CONSTRAINT FK_A3C664D3A76ED395 FOREIGN KEY (user_id) REFERENCES security_user (id);
     ALTER TABLE invoice ADD start_date DATETIME NOT NULL, ADD end_date DATETIME NOT NULL, DROP start_at, DROP end_at;

 The following SQL statements will be executed:

     ALTER TABLE plan_history ADD start_date DATETIME NOT NULL, ADD end_date DATETIME NOT NULL, DROP start_at, DROP end_at;
     ALTER TABLE subscription ADD CONSTRAINT FK_A3C664D3A76ED395 FOREIGN KEY (user_id) REFERENCES security_user (id);

 The following SQL statements will be executed:

     ALTER TABLE subscription ADD CONSTRAINT FK_A3C664D3A76ED395 FOREIGN KEY (user_id) REFERENCES security_user (id);

 The following SQL statements will be executed:

     ALTER TABLE plan DROP FOREIGN KEY FK_DD5A5B7D3E030ACD;
     DROP INDEX IDX_DD5A5B7D3E030ACD ON plan;
     ALTER TABLE plan DROP application_id;
     ALTER TABLE subscription ADD CONSTRAINT FK_A3C664D3A76ED395 FOREIGN KEY (user_id) REFERENCES security_user (id);

 The following SQL statements will be executed:

     ALTER TABLE plan ADD application_setting_id INT DEFAULT NULL;
     ALTER TABLE plan ADD CONSTRAINT FK_DD5A5B7D99B68C9E FOREIGN KEY (application_setting_id) REFERENCES application_settings (id);
     CREATE INDEX IDX_DD5A5B7D99B68C9E ON plan (application_setting_id);
     ALTER TABLE subscription ADD CONSTRAINT FK_A3C664D3A76ED395 FOREIGN KEY (user_id) REFERENCES security_user (id);

 The following SQL statements will be executed:

     ALTER TABLE application_settings DROP INDEX IDX_5A7E7FD63E030ACD, ADD UNIQUE INDEX UNIQ_5A7E7FD63E030ACD (application_id);
     ALTER TABLE application_settings CHANGE application_id application_id INT DEFAULT NULL;
     ALTER TABLE subscription ADD CONSTRAINT FK_A3C664D3A76ED395 FOREIGN KEY (user_id) REFERENCES security_user (id);

 The following SQL statements will be executed:

     ALTER TABLE subscription ADD CONSTRAINT FK_A3C664D3A76ED395 FOREIGN KEY (user_id) REFERENCES security_user (id);

 The following SQL statements will be executed:

     ALTER TABLE subscription ADD CONSTRAINT FK_A3C664D3A76ED395 FOREIGN KEY (user_id) REFERENCES security_user (id);

 The following SQL statements will be executed:

     CREATE TABLE executive_club (id INT AUTO_INCREMENT NOT NULL, application_settings_id INT DEFAULT NULL, description VARCHAR(50) NOT NULL, min_sales INT NOT NULL, max_sales INT NOT NULL, fee DOUBLE PRECISION NOT NULL, INDEX IDX_C6167F52F05FB5EA (application_settings_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB;
     ALTER TABLE executive_club ADD CONSTRAINT FK_C6167F52F05FB5EA FOREIGN KEY (application_settings_id) REFERENCES application_settings (id);

 The following SQL statements will be executed:

     ALTER TABLE executive_club CHANGE max_sales max_sales INT DEFAULT NULL;


-----23112919
ALTER TABLE asset ADD addr_address_line1 VARCHAR(64) DEFAULT NULL, ADD addr_address_line2 VARCHAR(64) DEFAULT NULL, ADD addr_town VARCHAR(128) DEFAULT NULL, ADD addr_city VARCHAR(128) DEFAULT NULL, ADD addr_postcode VARCHAR(12) DEFAULT NULL, ADD addr_country VARCHAR(10) DEFAULT NULL;






select * from asset_market_value where asset_id = 2;
select transaction_object_id, transaction_object_class from company_transaction_id = 2;
select * from asset_investment_transaction where account_id = 2;
select * asset_transaction where account_id = 2;
select * from asset_account;
select * from asset;

--clear asset
delete from asset_market_value where asset_id = 2;
delete from company_transaction where transaction_object_id = 2;
delete from user_transaction where transaction_object_id = 2;
delete from asset_investment_transaction where account_id = 2;
delete from asset_transaction where account_id = 2;
delete from asset_account where asset_id = 2;
delete from asset_investment_account where asset_id = 2;
delete from asset_photo where asset_id = 2;
delete from asset where id = 2;


--clear project
delete from company_transaction where transaction_object_id = 1 and transaction_object_class='App\Entity\Project\Project'; 
delete from user_transaction where transaction_object_id =1  and transaction_object_class='App\Entity\Project\Project'; 
delete from project_investment_transaction where account_id = 1;
delete from project_transaction where account_id = 1;
delete from project_account where project_id = 1;
delete from project_investment_account where project_id = 1;
delete from project_photo where project_id = 1;
delete from project where id = 1;


----
ALTER TABLE asset ADD addr_address_line1 VARCHAR(64) DEFAULT NULL, ADD addr_address_line2 VARCHAR(64) DEFAULT NULL, ADD addr_town VARCHAR(128) DEFAULT NULL, ADD addr_city VARCHAR(128) DEFAULT NULL, ADD addr_postcode VARCHAR(12) DEFAULT NULL, ADD addr_country VARCHAR(10) DEFAULT NULL;
ALTER TABLE property ADD addr_address_line1 VARCHAR(64) DEFAULT NULL, ADD addr_address_line2 VARCHAR(64) DEFAULT NULL, ADD addr_town VARCHAR(128) DEFAULT NULL, ADD addr_city VARCHAR(128) DEFAULT NULL, ADD addr_postcode VARCHAR(12) DEFAULT NULL, ADD addr_country VARCHAR(10) DEFAULT NULL;
ALTER TABLE project ADD addr_address_line1 VARCHAR(64) DEFAULT NULL, ADD addr_address_line2 VARCHAR(64) DEFAULT NULL, ADD addr_town VARCHAR(128) DEFAULT NULL, ADD addr_city VARCHAR(128) DEFAULT NULL, ADD addr_postcode VARCHAR(12) DEFAULT NULL, ADD addr_country VARCHAR(10) DEFAULT NULL;
-----


CREATE TABLE project_investment_transaction (id INT AUTO_INCREMENT NOT NULL, type_id INT NOT NULL, user_id INT DEFAULT NULL, account_id INT DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, amount DOUBLE PRECISION NOT NULL, created_at DATETIME NOT NULL, processed_at DATETIME DEFAULT NULL, fee DOUBLE PRECISION DEFAULT NULL, ref_value DOUBLE PRECISION DEFAULT NULL, transaction_id VARCHAR(32) DEFAULT NULL, date_ref DATETIME DEFAULT NULL, is_paid TINYINT(1) DEFAULT NULL, transaction_ref VARCHAR(22) DEFAULT NULL, INDEX IDX_636939DEC54C8C93 (type_id), INDEX IDX_636939DEA76ED395 (user_id), INDEX IDX_636939DE9B6B5FBA (account_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB;
CREATE TABLE project_investment_account (id INT AUTO_INCREMENT NOT NULL, project_id INT NOT NULL, account_number VARCHAR(15) DEFAULT NULL, UNIQUE INDEX UNIQ_740E7626166D1F9C (project_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB;
ALTER TABLE project_investment_transaction ADD CONSTRAINT FK_636939DEC54C8C93 FOREIGN KEY (type_id) REFERENCES table_value (id);
ALTER TABLE project_investment_transaction ADD CONSTRAINT FK_636939DEA76ED395 FOREIGN KEY (user_id) REFERENCES security_user (id);
ALTER TABLE project_investment_transaction ADD CONSTRAINT FK_636939DE9B6B5FBA FOREIGN KEY (account_id) REFERENCES project_investment_account (id);
ALTER TABLE project_investment_account ADD CONSTRAINT FK_740E7626166D1F9C FOREIGN KEY (project_id) REFERENCES project (id);


--delete user
select id from subscription where user_id = 22;
delete from invoice where subscription_id = 18;
delete from plan_history where subscription_id = 18;
delete from subscription where user_id = 22;
delete from security_permission where user_id = 22;
delete from user_account where user_id = 22;
delete from security_user where id = 22;



ALTER TABLE project_transaction CHANGE amount amount NUMERIC(10, 2) NOT NULL;
ALTER TABLE asset_transaction CHANGE amount amount NUMERIC(10, 2) NOT NULL;
ALTER TABLE project_investment_transaction CHANGE amount amount NUMERIC(10, 2) NOT NULL;
ALTER TABLE user_transaction CHANGE amount amount NUMERIC(10, 2) NOT NULL;
ALTER TABLE property_transaction CHANGE amount amount NUMERIC(10, 2) NOT NULL;
ALTER TABLE asset_investment_transaction CHANGE amount amount NUMERIC(10, 2) NOT NULL;


ALTER TABLE asset ADD bedrooms INT DEFAULT NULL, ADD bathrooms INT DEFAULT NULL, ADD leisure_area TINYINT(1) DEFAULT NULL;
ALTER TABLE asset ADD year_income NUMERIC(10, 2) DEFAULT NULL, ADD monthly_income NUMERIC(10, 2) DEFAULT NULL;


CREATE TABLE asset_recurring_expense (id INT AUTO_INCREMENT NOT NULL, type_id INT NOT NULL, asset_id INT NOT NULL, INDEX IDX_A42B984FC54C8C93 (type_id), INDEX IDX_A42B984F5DA1941 (asset_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB;
ALTER TABLE asset_recurring_expense ADD CONSTRAINT FK_A42B984FC54C8C93 FOREIGN KEY (type_id) REFERENCES table_value (id);
ALTER TABLE asset_recurring_expense ADD CONSTRAINT FK_A42B984F5DA1941 FOREIGN KEY (asset_id) REFERENCES asset (id);


ALTER TABLE asset ADD birthday DATE DEFAULT NULL;
ALTER TABLE asset ADD youtube_url VARCHAR(100) DEFAULT NULL;


--mv /Users/laertejjunior/.symfony/bin/symfony /usr/
local/bin/symfony

ALTER TABLE project ADD construction_cost NUMERIC(10, 2) DEFAULT NULL;
ALTER TABLE project ADD bedrooms INT DEFAULT NULL, ADD bathrooms INT DEFAULT NULL;
ALTER TABLE project ADD youtube_url VARCHAR(100) DEFAULT NULL;




ALTER TABLE project CHANGE purchase_price purchase_price NUMERIC(10, 2) DEFAULT NULL, CHANGE sale_price_projection sale_price_projection NUMERIC(10, 2) DEFAULT NULL, CHANGE sale_real_price sale_real_price NUMERIC(10, 2) DEFAULT NULL;

CREATE TABLE broker (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, description VARCHAR(255) NOT NULL, INDEX IDX_F6AAF03BA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;
ALTER TABLE broker ADD CONSTRAINT FK_F6AAF03BA76ED395 FOREIGN KEY (user_id) REFERENCES security_user (id);
ALTER TABLE asset ADD broker_id INT DEFAULT NULL;
ALTER TABLE asset ADD CONSTRAINT FK_2AF5A5C6CC064FC FOREIGN KEY (broker_id) REFERENCES broker (id);
CREATE INDEX IDX_2AF5A5C6CC064FC ON asset (broker_id);
ALTER TABLE project ADD broker_id INT DEFAULT NULL;
ALTER TABLE project ADD CONSTRAINT FK_2FB3D0EE6CC064FC FOREIGN KEY (broker_id) REFERENCES broker (id);
CREATE INDEX IDX_2FB3D0EE6CC064FC ON project (broker_id);



ALTER TABLE project_transaction ADD global_transaction_id VARCHAR(32) DEFAULT NULL;
ALTER TABLE asset_transaction ADD global_transaction_id VARCHAR(32) DEFAULT NULL;
ALTER TABLE project_investment_transaction ADD global_transaction_id VARCHAR(32) DEFAULT NULL;
ALTER TABLE user_transaction ADD global_transaction_id VARCHAR(32) DEFAULT NULL;
ALTER TABLE company_transaction ADD global_transaction_id VARCHAR(32) DEFAULT NULL;
ALTER TABLE property_transaction ADD global_transaction_id VARCHAR(32) DEFAULT NULL;
ALTER TABLE asset_investment_transaction ADD global_transaction_id VARCHAR(32) DEFAULT NULL;






ALTER TABLE agent ADD description VARCHAR(255) NOT NULL, DROP phone, DROP subscribed_at;
ALTER TABLE property ADD agent_id INT DEFAULT NULL;
ALTER TABLE property ADD CONSTRAINT FK_8BF21CDE3414710B FOREIGN KEY (agent_id) REFERENCES agent (id);
CREATE INDEX IDX_8BF21CDE3414710B ON property (agent_id);
ALTER TABLE project DROP FOREIGN KEY FK_2FB3D0EE3414710B;
ALTER TABLE project ADD CONSTRAINT FK_2FB3D0EE3414710B FOREIGN KEY (agent_id) REFERENCES agent (id);



ALTER TABLE asset DROP FOREIGN KEY FK_2AF5A5C3414710B;
ALTER TABLE asset ADD CONSTRAINT FK_2AF5A5C3414710B FOREIGN KEY (agent_id) REFERENCES agent (id);
ALTER TABLE executive_club ADD company_fee DOUBLE PRECISION NOT NULL;
 ALTER TABLE executive_club ADD distributable_value DOUBLE PRECISION NOT NULL;












 ALTER TABLE user_transaction CHANGE amount amount NUMERIC(10, 2) NOT NULL;
     ALTER TABLE property_transaction CHANGE amount amount NUMERIC(10, 2) NOT NULL;
     ALTER TABLE asset_transaction CHANGE amount amount NUMERIC(10, 2) NOT NULL;
     ALTER TABLE asset_investment_transaction CHANGE amount amount NUMERIC(10, 2) NOT NULL;
     ALTER TABLE project_transaction CHANGE amount amount NUMERIC(10, 2) NOT NULL;
     ALTER TABLE project_investment_transaction CHANGE amount amount NUMERIC(10, 2) NOT NULL;
     ALTER TABLE project DROP FOREIGN KEY FK_2FB3D0EE3414710B;
     ALTER TABLE project ADD CONSTRAINT FK_2FB3D0EE3414710B FOREIGN KEY (agent_id) REFERENCES agent (id);
     ALTER TABLE asset DROP FOREIGN KEY FK_2AF5A5C3414710B;
     ALTER TABLE asset ADD CONSTRAINT FK_2AF5A5C3414710B FOREIGN KEY (agent_id) REFERENCES agent (id);
     ALTER TABLE property ADD agent_id INT DEFAULT NULL;
     ALTER TABLE property ADD CONSTRAINT FK_8BF21CDE3414710B FOREIGN KEY (agent_id) REFERENCES agent (id);
     CREATE INDEX IDX_8BF21CDE3414710B ON property (agent_id);
     ALTER TABLE agent ADD description VARCHAR(255) NOT NULL, DROP phone, DROP subscribed_at;
     ALTER TABLE executive_club ADD distributable_value DOUBLE PRECISION NOT NULL, ADD company_fee DOUBLE PRECISION NOT NULL;



     ALTER TABLE asset CHANGE purchase_price market_value DOUBLE PRECISION NOT NULL;


     CREATE TABLE asset_equity (id INT AUTO_INCREMENT NOT NULL, asset_id INT NOT NULL, month_ref INT NOT NULL, year_r
ef INT NOT NULL, price DOUBLE PRECISION NOT NULL, INDEX IDX_BC09AC435DA1941 (asset_id), PRIMARY KEY(id)) DEFAULT CHAR
ACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;
     ALTER TABLE asset_equity ADD CONSTRAINT FK_BC09AC435DA1941 FOREIGN KEY (asset_id) REFERENCES asset (id);





       CREATE TABLE investor (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, INDEX IDX_8BBAED26A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;

     ALTER TABLE investor ADD CONSTRAINT FK_8BBAED26A76ED395 FOREIGN KEY (user_id) REFERENCES security_user (id);




CREATE TABLE accommodation_photo (id INT AUTO_INCREMENT NOT NULL, accommodation_id INT NOT NULL, path VARCHAR(1000) NOT NULL
, INDEX IDX_1808A7E28F3692CD (accommodation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;

CREATE TABLE accommodation (id INT AUTO_INCREMENT NOT NULL, post_status_id INT DEFAULT NULL, user_id INT NOT NULL, agent_id INT DEFAULT NULL, property_type_id INT DEFAULT NULL, removal_reason_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, price DOUBLE PRECISION NOT NULL, description LONGTEXT NOT NULL, hits INT NOT NULL, path_main_photo VARCHAR(1000) NOT NULL, deadline DATETIME DEFAULT NULL, youtube_url VARCHAR(100) DEFAULT NULL, comission_rate DOUBLE PRECISION NOT NULL, tag VARCHAR(20) DEFAULT NULL, slug VARCHAR(300) DEFAULT NULL, property_status VARCHAR(20) NOT NULL COMMENT '(DC2Type:App\\Enum\\PropertyStatus)', removal_details VARCHAR(1000) DEFAULT NULL, removed_at DATETIME DEFAULT NULL, sold_at DATETIME DEFAULT NULL, created_at DATETIME DEFAULT NULL, bedrooms INT DEFAULT NULL, bathrooms INT DEFAULT NULL, square_foot DOUBLE PRECISION DEFAULT NULL, addr_address_line1 VARCHAR(64) DEFAULT NULL, addr_address_line2 VARCHAR(64) DEFAULT NULL, addr_town VARCHAR(128) DEFAULT NULL, addr_city VARCHAR(128) DEFAULT NULL,
 addr_postcode VARCHAR(12) DEFAULT NULL, addr_country VARCHAR(10) DEFAULT NULL, INDEX IDX_2D385412CA997E4A (post_status_id), INDEX IDX_2D385412A76ED395 (user_id), INDEX IDX_2D3854123414710B (agent_id), INDEX IDX_2D3854129C81C6EB (property_type_id), INDEX IDX_2D3854125A83A890 (removal_reason_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;

CREATE TABLE accommodation_account (id INT AUTO_INCREMENT NOT NULL, property_id INT NOT NULL, account_number VARCHAR(15) DEFAULT NULL, UNIQUE INDEX UNIQ_3102225E549213EC (property_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;
ALTER TABLE accommodation_photo ADD CONSTRAINT FK_1808A7E28F3692CD FOREIGN KEY (accommodation_id) REFERENCES accommodation (id);
ALTER TABLE accommodation ADD CONSTRAINT FK_2D385412CA997E4A FOREIGN KEY (post_status_id) REFERENCES table_value (id);
ALTER TABLE accommodation ADD CONSTRAINT FK_2D385412A76ED395 FOREIGN KEY (user_id) REFERENCES security_user (id);
ALTER TABLE accommodation ADD CONSTRAINT FK_2D3854123414710B FOREIGN KEY (agent_id) REFERENCES agent (id);
ALTER TABLE accommodation ADD CONSTRAINT FK_2D3854129C81C6EB FOREIGN KEY (property_type_id) REFERENCES table_value (id);
ALTER TABLE accommodation ADD CONSTRAINT FK_2D3854125A83A890 FOREIGN KEY (removal_reason_id) REFERENCES table_value (id);
ALTER TABLE accommodation_account ADD CONSTRAINT FK_3102225E549213EC FOREIGN KEY (property_id) REFERENCES property (id);


 CREATE TABLE accommodation_transaction (id INT AUTO_INCREMENT NOT NULL, type_id INT NOT NULL, user_id INT DEFAULT NULL, account_id INT DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, amount NUMERIC(10, 2) NOT NULL, created_at DATETIME NOT NULL, processed_at DATETIME DEFAULT NULL, fee DOUBLE PRECISION DEFAULT NULL, ref_value DOUBLE PRECISION DEFAULT NULL, transaction_id VARCHAR(32) DEFAULT NULL, global_transaction_id VARCHAR(32) DEFAULT NULL, date_ref DATETIME DEFAULT NULL, is_paid TINYINT(1) DEFAULT NULL, transaction_ref VARCHAR(22) DEFAULT NULL, INDEX IDX_C54CDD2C54C8C93 (type_id), INDEX IDX_C54CDD2A76ED395 (user_id), INDEX IDX_C54CDD29B6B5FBA (account_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;
     ALTER TABLE accommodation_transaction ADD CONSTRAINT FK_C54CDD2C54C8C93 FOREIGN KEY (type_id) REFERENCES table_value (id);
     ALTER TABLE accommodation_transaction ADD CONSTRAINT FK_C54CDD2A76ED395 FOREIGN KEY (user_id) REFERENCES security_user (id);
     ALTER TABLE accommodation_transaction ADD CONSTRAINT FK_C54CDD29B6B5FBA FOREIGN KEY (account_id) REFERENCES accommodation_account (id);
     ALTER TABLE property DROP FOREIGN KEY FK_8BF21CDE1A180200;
     DROP INDEX IDX_8BF21CDE1A180200 ON property;
     ALTER TABLE property DROP property_status_id;
     ALTER TABLE security_application CHANGE description description VARCHAR(255) NOT NULL;
     ALTER TABLE accommodation_account DROP FOREIGN KEY FK_3102225E549213EC;
     DROP INDEX UNIQ_3102225E549213EC ON accommodation_account;
     ALTER TABLE accommodation_account CHANGE property_id accommodation_id INT NOT NULL;
     ALTER TABLE accommodation_account ADD CONSTRAINT FK_3102225E8F3692CD FOREIGN KEY (accommodation_id) REFERENCES accommodation (id);
     CREATE UNIQUE INDEX UNIQ_3102225E8F3692CD ON accommodation_account (accommodation_id);

ALTER TABLE accommodation_account DROP INDEX FK_3102225E8F3692CD, ADD UNIQUE INDEX UNIQ_3102225E8F3692CD (accommodation_id);
     ALTER TABLE company_account DROP FOREIGN KEY FK_5D644C733E030ACD;
     DROP INDEX UNIQ_5D644C733E030ACD ON company_account;
     ALTER TABLE company_account DROP application_id;
     ALTER TABLE accommodation_transaction ADD CONSTRAINT FK_C54CDD29B6B5FBA FOREIGN KEY (account_id) REFERENCES accommodation_account 
(id);


ALTER TABLE accommodation ADD rent DOUBLE PRECISION DEFAULT NULL, ADD deposit DOUBLE PRECISION DEFAULT NULL, ADD terms INT DEFAULT NULL, ADD term_type VARCHAR(255) DEFAULT NULL, ADD let_available_for INT DEFAULT NULL, ADD available_at DATETIME DEFAULT NULL;




alter table event rename to calendar_events;
alter table calendar_events change name title varchar(150);
rename table event_user to calendar_event_users;
ALTER TABLE calendar_events CHANGE title title VARCHAR(150) NOT NULL;
     ALTER TABLE calendar_events RENAME INDEX idx_3bae0aa77e3c61f9 TO IDX_F9E14F167E3C61F9;
     ALTER TABLE calendar_event_users RENAME INDEX idx_92589ae271f7e88b TO IDX_2564AA8E71F7E88B;
     ALTER TABLE calendar_event_users RENAME INDEX idx_92589ae2a76ed395 TO IDX_2564AA8EA76ED395;





     ALTER TABLE companies ADD created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, ADD updated_at DATETIME DEFAULT NULL, ADD removed_at DATETIME DEFAULT NULL, ADD settings_company_fee DOUBLE PRECISION DEFAULT NULL, ADD settings_comission_rate DOUBLE PRECISION NOT NULL, ADD settings_monthly_fee DOUBLE PRECISION NOT NULL;




     CREATE TABLE transaction_log (id CHAR(36) NOT NULL COMMENT '(DC2Type:uuid)', order_id VARCHAR(30) NOT NULL, amount NUMERIC(20, 0) DEFAULT NULL, amount_major NUMERIC(20, 2) NOT NULL, currency VARCHAR(10) NOT NULL, cross_reference VARCHAR(30) NOT NULL, status_code INT DEFAULT NULL, type VARCHAR(40) DEFAULT NULL, gateway_message VARCHAR(255) DEFAULT NULL, transaction_datetime DATETIME DEFAULT NULL, transaction_datetime_text VARCHAR(100) DEFAULT NULL, integration_type VARCHAR(50) DEFAULT NULL, timestamp INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;

     ALTER TABLE finance_investments ADD reference_code VARCHAR(12) NOT NULL;
     ALTER TABLE finance_entryFee ADD reference_code VARCHAR(12) NOT NULL;