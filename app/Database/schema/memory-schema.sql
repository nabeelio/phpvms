CREATE TABLE IF NOT EXISTS "migrations" ("id" integer primary key autoincrement not null, "migration" varchar not null, "batch" integer not null);
CREATE TABLE IF NOT EXISTS "settings" ("id" varchar not null, "offset" integer not null default '0', "order" integer not null default '99', "key" varchar not null, "name" varchar not null, "value" varchar not null, "default" varchar, "group" varchar, "type" varchar, "options" text, "description" varchar, "created_at" datetime, "updated_at" datetime, primary key ("id"));
CREATE INDEX "settings_key_index" on "settings" ("key");
CREATE TABLE IF NOT EXISTS "roles" ("id" integer primary key autoincrement not null, "name" varchar not null, "display_name" varchar, "description" varchar, "created_at" datetime, "updated_at" datetime, "read_only" tinyint(1) not null default '0', "disable_activity_checks" tinyint(1) not null default '0');
CREATE UNIQUE INDEX "roles_name_unique" on "roles" ("name");
CREATE TABLE IF NOT EXISTS "permissions" ("id" integer primary key autoincrement not null, "name" varchar not null, "display_name" varchar, "description" varchar, "created_at" datetime, "updated_at" datetime);
CREATE UNIQUE INDEX "permissions_name_unique" on "permissions" ("name");
CREATE TABLE IF NOT EXISTS "role_user" ("role_id" integer not null, "user_id" integer not null, "user_type" varchar not null, foreign key("role_id") references "roles"("id") on delete cascade on update cascade, primary key ("user_id", "role_id", "user_type"));
CREATE TABLE IF NOT EXISTS "permission_user" ("permission_id" integer not null, "user_id" integer not null, "user_type" varchar not null, foreign key("permission_id") references "permissions"("id") on delete cascade on update cascade, primary key ("user_id", "permission_id", "user_type"));
CREATE TABLE IF NOT EXISTS "permission_role" ("permission_id" integer not null, "role_id" integer not null, foreign key("permission_id") references "permissions"("id") on delete cascade on update cascade, foreign key("role_id") references "roles"("id") on delete cascade on update cascade, primary key ("permission_id", "role_id"));
CREATE TABLE IF NOT EXISTS "password_resets" ("email" varchar not null, "token" varchar not null, "created_at" datetime);
CREATE INDEX "password_resets_email_index" on "password_resets" ("email");
CREATE INDEX "password_resets_token_index" on "password_resets" ("token");
CREATE TABLE IF NOT EXISTS "sessions" ("id" varchar not null, "user_id" integer, "ip_address" varchar, "user_agent" text, "payload" text not null, "last_activity" integer not null);
CREATE UNIQUE INDEX "sessions_id_unique" on "sessions" ("id");
CREATE TABLE IF NOT EXISTS "airlines" ("id" integer primary key autoincrement not null, "icao" varchar not null, "iata" varchar, "name" varchar not null, "country" varchar, "logo" varchar, "active" tinyint(1) not null default '1', "total_flights" integer default '0', "total_time" integer default '0', "created_at" datetime, "updated_at" datetime, "callsign" varchar, "deleted_at" datetime);
CREATE INDEX "airlines_icao_index" on "airlines" ("icao");
CREATE UNIQUE INDEX "airlines_icao_unique" on "airlines" ("icao");
CREATE INDEX "airlines_iata_index" on "airlines" ("iata");
CREATE TABLE IF NOT EXISTS "aircraft" ("id" integer primary key autoincrement not null, "subfleet_id" integer not null, "icao" varchar, "iata" varchar, "airport_id" varchar, "landing_time" datetime, "name" varchar not null, "registration" varchar, "hex_code" varchar, "zfw" numeric default '0', "flight_time" integer default '0', "status" varchar not null default 'A', "state" integer not null default '0', "created_at" datetime, "updated_at" datetime, "mtow" numeric default '0', "fuel_onboard" numeric default '0', "hub_id" varchar, "deleted_at" datetime, "fin" varchar, "selcal" varchar, "dow" numeric, "mlw" numeric, "simbrief_type" varchar);
CREATE UNIQUE INDEX "aircraft_registration_unique" on "aircraft" ("registration");
CREATE INDEX "aircraft_airport_id_index" on "aircraft" ("airport_id");
CREATE TABLE IF NOT EXISTS "fares" ("id" integer primary key autoincrement not null, "code" varchar not null, "name" varchar not null, "price" numeric default '0', "cost" numeric default '0', "capacity" integer default '0', "notes" varchar, "active" tinyint(1) not null default '1', "created_at" datetime, "updated_at" datetime, "type" integer default '0', "deleted_at" datetime);
CREATE UNIQUE INDEX "fares_code_unique" on "fares" ("code");
CREATE TABLE IF NOT EXISTS "flight_fields" ("id" integer primary key autoincrement not null, "name" varchar not null, "slug" varchar);
CREATE TABLE IF NOT EXISTS "ranks" ("id" integer primary key autoincrement not null, "name" varchar not null, "image_url" varchar, "hours" integer not null default '0', "acars_base_pay_rate" numeric default '0', "manual_base_pay_rate" numeric default '0', "auto_approve_acars" tinyint(1) default '0', "auto_approve_manual" tinyint(1) default '0', "auto_promote" tinyint(1) default '1', "auto_approve_above_score" tinyint(1) default '0', "auto_approve_score" integer, "created_at" datetime, "updated_at" datetime, "deleted_at" datetime);
CREATE UNIQUE INDEX "ranks_name_unique" on "ranks" ("name");
CREATE TABLE IF NOT EXISTS "subfleets" ("id" integer primary key autoincrement not null, "airline_id" integer, "type" varchar not null, "name" varchar not null, "cost_block_hour" numeric default '0', "cost_delay_minute" numeric default '0', "fuel_type" integer, "ground_handling_multiplier" numeric default '100', "cargo_capacity" numeric, "fuel_capacity" numeric, "gross_weight" numeric, "created_at" datetime, "updated_at" datetime, "hub_id" varchar, "simbrief_type" varchar, "deleted_at" datetime);
CREATE TABLE IF NOT EXISTS "subfleet_fare" ("subfleet_id" integer not null, "fare_id" integer not null, "price" varchar, "cost" varchar, "capacity" varchar, "created_at" datetime, "updated_at" datetime, primary key ("subfleet_id", "fare_id"));
CREATE INDEX "subfleet_fare_fare_id_subfleet_id_index" on "subfleet_fare" ("fare_id", "subfleet_id");
CREATE TABLE IF NOT EXISTS "subfleet_rank" ("rank_id" integer not null, "subfleet_id" integer not null, "acars_pay" varchar, "manual_pay" varchar, primary key ("rank_id", "subfleet_id"));
CREATE INDEX "subfleet_rank_subfleet_id_rank_id_index" on "subfleet_rank" ("subfleet_id", "rank_id");
CREATE TABLE IF NOT EXISTS "pirep_fields" ("id" integer primary key autoincrement not null, "name" varchar not null, "slug" varchar, "required" tinyint(1) default '0', "description" varchar, "pirep_source" integer default '3');
CREATE TABLE IF NOT EXISTS "jobs" ("id" integer primary key autoincrement not null, "queue" varchar not null, "payload" text not null, "attempts" integer not null, "reserved_at" integer, "available_at" integer not null, "created_at" integer not null);
CREATE INDEX "jobs_queue_index" on "jobs" ("queue");
CREATE TABLE IF NOT EXISTS "failed_jobs" ("id" integer primary key autoincrement not null, "connection" text not null, "queue" text not null, "payload" text not null, "exception" text not null, "failed_at" datetime not null default CURRENT_TIMESTAMP);
CREATE TABLE IF NOT EXISTS "navdata" ("id" varchar not null, "name" varchar not null, "type" integer not null, "lat" float default '0', "lon" float default '0', "freq" varchar, primary key ("id", "name"));
CREATE INDEX "navdata_id_index" on "navdata" ("id");
CREATE INDEX "navdata_name_index" on "navdata" ("name");
CREATE TABLE IF NOT EXISTS "stats" ("id" varchar not null, "value" varchar not null, "order" integer not null, "type" varchar, "description" varchar, "created_at" datetime, "updated_at" datetime, primary key ("id"));
CREATE TABLE IF NOT EXISTS "news" ("id" integer primary key autoincrement not null, "user_id" integer not null, "subject" varchar not null, "body" text not null, "created_at" datetime, "updated_at" datetime);
CREATE TABLE IF NOT EXISTS "awards" ("id" integer primary key autoincrement not null, "name" varchar not null, "description" text, "image_url" text, "ref_model" varchar, "ref_model_params" text, "created_at" datetime, "updated_at" datetime, "active" tinyint(1) default '1', "deleted_at" datetime);
CREATE INDEX "awards_ref_model_index" on "awards" ("ref_model");
CREATE TABLE IF NOT EXISTS "user_awards" ("id" integer primary key autoincrement not null, "user_id" integer not null, "award_id" integer not null, "created_at" datetime, "updated_at" datetime);
CREATE INDEX "user_awards_user_id_award_id_index" on "user_awards" ("user_id", "award_id");
CREATE TABLE IF NOT EXISTS "expenses" ("id" integer primary key autoincrement not null, "airline_id" integer, "name" varchar not null, "amount" integer not null, "type" varchar not null, "charge_to_user" tinyint(1) default '0', "multiplier" tinyint(1) default '0', "active" tinyint(1) default '1', "ref_model" varchar, "ref_model_id" varchar, "created_at" datetime, "updated_at" datetime, "flight_type" varchar);
CREATE INDEX "expenses_ref_model_ref_model_id_index" on "expenses" ("ref_model", "ref_model_id");
CREATE TABLE IF NOT EXISTS "journal_transactions" ("id" varchar not null, "transaction_group" varchar, "journal_id" integer not null, "credit" integer, "debit" integer, "currency" varchar not null, "memo" text, "tags" varchar, "ref_model" varchar, "ref_model_id" varchar, "created_at" datetime, "updated_at" datetime, "post_date" date not null, primary key ("id"));
CREATE INDEX "journal_transactions_journal_id_index" on "journal_transactions" ("journal_id");
CREATE INDEX "journal_transactions_transaction_group_index" on "journal_transactions" ("transaction_group");
CREATE INDEX "journal_transactions_ref_model_ref_model_id_index" on "journal_transactions" ("ref_model", "ref_model_id");
CREATE UNIQUE INDEX "journal_transactions_id_unique" on "journal_transactions" ("id");
CREATE TABLE IF NOT EXISTS "journals" ("id" integer primary key autoincrement not null, "ledger_id" integer, "type" integer not null default '0', "balance" integer not null default '0', "currency" varchar not null, "morphed_type" varchar, "morphed_id" integer, "created_at" datetime, "updated_at" datetime);
CREATE INDEX "journals_morphed_type_morphed_id_index" on "journals" ("morphed_type", "morphed_id");
CREATE TABLE IF NOT EXISTS "ledgers" ("id" integer primary key autoincrement not null, "name" varchar not null, "type" varchar check ("type" in ('asset', 'liability', 'equity', 'income', 'expense')) not null, "created_at" datetime, "updated_at" datetime);
CREATE TABLE IF NOT EXISTS "notifications" ("id" varchar not null, "type" varchar not null, "notifiable_type" varchar not null, "notifiable_id" integer not null, "data" text not null, "read_at" datetime, "created_at" datetime, "updated_at" datetime, primary key ("id"));
CREATE INDEX "notifications_notifiable_type_notifiable_id_index" on "notifications" ("notifiable_type", "notifiable_id");
CREATE TABLE bids (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, flight_id VARCHAR(36) NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, "aircraft_id" integer);
CREATE INDEX bids_user_id_flight_id_index ON bids (user_id, flight_id);
CREATE INDEX bids_user_id_index ON bids (user_id);
CREATE TABLE flights (id VARCHAR(36) NOT NULL, airline_id INTEGER NOT NULL, flight_number INTEGER NOT NULL, route_code VARCHAR(255) DEFAULT NULL, route_leg INTEGER DEFAULT NULL, dpt_airport_id VARCHAR(255) NOT NULL, arr_airport_id VARCHAR(255) NOT NULL, alt_airport_id VARCHAR(255) DEFAULT NULL, dpt_time VARCHAR(255) DEFAULT NULL, arr_time VARCHAR(255) DEFAULT NULL, level INTEGER DEFAULT 0, distance NUMERIC(10, 0) DEFAULT '0', flight_time INTEGER DEFAULT NULL, flight_type VARCHAR(255) DEFAULT 'J' NOT NULL, route CLOB DEFAULT NULL, notes CLOB DEFAULT NULL, scheduled BOOLEAN DEFAULT 0, days INTEGER DEFAULT NULL, start_date DATE DEFAULT NULL, end_date DATE DEFAULT NULL, has_bid BOOLEAN DEFAULT 0 NOT NULL, active BOOLEAN DEFAULT 1 NOT NULL, visible BOOLEAN DEFAULT 1 NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, load_factor NUMERIC(10, 0) DEFAULT NULL, load_factor_variance NUMERIC(10, 0) DEFAULT NULL, pilot_pay NUMERIC(10, 0) DEFAULT NULL, "callsign" varchar, "event_id" integer, "user_id" integer, "deleted_at" datetime, "owner_type" varchar, "owner_id" varchar, PRIMARY KEY(id));
CREATE INDEX flights_arr_airport_id_index ON flights (arr_airport_id);
CREATE INDEX flights_dpt_airport_id_index ON flights (dpt_airport_id);
CREATE INDEX flights_flight_number_index ON flights (flight_number);
CREATE TABLE flight_fare (flight_id VARCHAR(36) NOT NULL, fare_id INTEGER NOT NULL, price VARCHAR(255) DEFAULT NULL, cost VARCHAR(255) DEFAULT NULL, capacity VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(flight_id, fare_id));
CREATE TABLE flight_field_values (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, flight_id VARCHAR(36) NOT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) DEFAULT NULL, value CLOB DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL);
CREATE INDEX flight_field_values_flight_id_index ON flight_field_values (flight_id);
CREATE TABLE flight_subfleet (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, subfleet_id INTEGER NOT NULL, flight_id VARCHAR(36) NOT NULL);
CREATE INDEX flight_subfleet_flight_id_subfleet_id_index ON flight_subfleet (flight_id, subfleet_id);
CREATE INDEX flight_subfleet_subfleet_id_flight_id_index ON flight_subfleet (subfleet_id, flight_id);
CREATE TABLE pirep_comments (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, pirep_id VARCHAR(36) NOT NULL, user_id INTEGER NOT NULL, comment CLOB NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL);
CREATE TABLE pirep_field_values (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, pirep_id VARCHAR(36) NOT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) DEFAULT NULL, value VARCHAR(255) DEFAULT NULL, source INTEGER NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL);
CREATE INDEX pirep_field_values_pirep_id_index ON pirep_field_values (pirep_id);
CREATE TABLE users (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) DEFAULT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, api_key VARCHAR(255) DEFAULT NULL, airline_id INTEGER NOT NULL, rank_id INTEGER DEFAULT NULL, country VARCHAR(255) DEFAULT NULL, home_airport_id VARCHAR(255) DEFAULT NULL, curr_airport_id VARCHAR(255) DEFAULT NULL, last_pirep_id VARCHAR(36) DEFAULT NULL, flights INTEGER DEFAULT 0 NOT NULL, flight_time INTEGER DEFAULT 0, transfer_time INTEGER DEFAULT 0, avatar VARCHAR(255) DEFAULT NULL, timezone VARCHAR(255) DEFAULT NULL, status INTEGER DEFAULT 0, state INTEGER DEFAULT 0, toc_accepted BOOLEAN DEFAULT NULL, opt_in BOOLEAN DEFAULT NULL, active BOOLEAN DEFAULT NULL, last_ip VARCHAR(255) DEFAULT NULL, remember_token VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, pilot_id INTEGER DEFAULT NULL, "discord_id" varchar not null default '', "discord_private_channel_id" varchar not null default '', "notes" text, "callsign" varchar, "lastlogin_at" datetime, "email_verified_at" datetime);
CREATE UNIQUE INDEX users_pilot_id_unique ON users (pilot_id);
CREATE UNIQUE INDEX users_email_unique ON users (email);
CREATE INDEX users_api_key_index ON users (api_key);
CREATE INDEX users_email_index ON users (email);
CREATE TABLE IF NOT EXISTS "simbrief" ("id" varchar not null, "user_id" integer not null, "flight_id" varchar, "pirep_id" varchar, "acars_xml" text not null, "ofp_xml" text not null, "created_at" datetime, "updated_at" datetime, "aircraft_id" integer, "fare_data" text, primary key ("id"));
CREATE INDEX "simbrief_user_id_flight_id_index" on "simbrief" ("user_id", "flight_id");
CREATE INDEX "simbrief_pirep_id_index" on "simbrief" ("pirep_id");
CREATE UNIQUE INDEX "simbrief_pirep_id_unique" on "simbrief" ("pirep_id");
CREATE TABLE IF NOT EXISTS "user_fields" ("id" integer primary key autoincrement not null, "name" varchar not null, "description" text, "show_on_registration" tinyint(1) default '0', "required" tinyint(1) default '0', "private" tinyint(1) default '0', "active" tinyint(1) default '1', "created_at" datetime, "updated_at" datetime);
CREATE TABLE IF NOT EXISTS "user_field_values" ("id" integer primary key autoincrement not null, "user_field_id" integer not null, "user_id" varchar not null, "value" text, "created_at" datetime, "updated_at" datetime);
CREATE INDEX "user_field_values_user_field_id_user_id_index" on "user_field_values" ("user_field_id", "user_id");
CREATE TABLE IF NOT EXISTS "modules" ("id" integer primary key autoincrement not null, "name" varchar not null, "enabled" tinyint(1) not null default '1', "created_at" datetime, "updated_at" datetime);
CREATE TABLE pages (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, icon VARCHAR(191) DEFAULT NULL, type INTEGER DEFAULT 0 NOT NULL, public BOOLEAN NOT NULL, enabled BOOLEAN NOT NULL, body CLOB DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, link VARCHAR(255) DEFAULT '', new_window BOOLEAN DEFAULT 0 NOT NULL);
CREATE INDEX pages_slug_index ON pages (slug);
CREATE TABLE files (id VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, disk CLOB DEFAULT NULL, path CLOB DEFAULT NULL, public BOOLEAN DEFAULT 1 NOT NULL, download_count INTEGER DEFAULT 0 NOT NULL, ref_model VARCHAR(255) DEFAULT NULL, ref_model_id VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id));
CREATE INDEX files_ref_model_ref_model_id_index ON files (ref_model, ref_model_id);
CREATE TABLE acars (id VARCHAR(36) NOT NULL, pirep_id VARCHAR(36) NOT NULL, type INTEGER NOT NULL, nav_type INTEGER DEFAULT NULL, "order" INTEGER DEFAULT 0 NOT NULL, name VARCHAR(255) DEFAULT NULL, status VARCHAR(255) DEFAULT 'SCH' NOT NULL, log VARCHAR(255) DEFAULT NULL, lat NUMERIC(10, 5) DEFAULT '0', lon NUMERIC(11, 5) DEFAULT '0', distance INTEGER DEFAULT NULL, heading INTEGER DEFAULT NULL, altitude INTEGER DEFAULT NULL, vs DOUBLE PRECISION DEFAULT '0', gs INTEGER DEFAULT NULL, transponder INTEGER DEFAULT NULL, autopilot VARCHAR(255) DEFAULT NULL, fuel NUMERIC(10, 0) DEFAULT NULL, fuel_flow NUMERIC(10, 0) DEFAULT NULL, sim_time VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, "ias" integer, PRIMARY KEY(id));
CREATE INDEX acars_created_at_index ON acars (created_at);
CREATE INDEX acars_pirep_id_index ON acars (pirep_id);
CREATE INDEX "sessions_user_id_index" on "sessions" ("user_id");
CREATE INDEX "sessions_last_activity_index" on "sessions" ("last_activity");
CREATE TABLE IF NOT EXISTS "kvp" ("key" varchar not null, "value" varchar not null);
CREATE INDEX "kvp_key_index" on "kvp" ("key");
CREATE TABLE airports (id VARCHAR(255) NOT NULL, iata VARCHAR(5) DEFAULT NULL, icao VARCHAR(5) NOT NULL, name VARCHAR(255) NOT NULL, location VARCHAR(255) DEFAULT NULL, country VARCHAR(255) DEFAULT NULL, timezone VARCHAR(255) DEFAULT NULL, hub BOOLEAN DEFAULT 0 NOT NULL, lat NUMERIC(10, 5) DEFAULT '0', lon NUMERIC(11, 5) DEFAULT '0', ground_handling_cost NUMERIC(10, 0) DEFAULT '0', fuel_100ll_cost NUMERIC(10, 0) DEFAULT '0', fuel_jeta_cost NUMERIC(10, 0) DEFAULT '0', fuel_mogas_cost NUMERIC(10, 0) DEFAULT '0', "notes" text, "deleted_at" datetime, "elevation" integer, "region" varchar, PRIMARY KEY(id));
CREATE INDEX airports_icao_index ON airports (icao);
CREATE INDEX airports_iata_index ON airports (iata);
CREATE INDEX airports_hub_index ON airports (hub);
CREATE TABLE pireps (id VARCHAR(36) NOT NULL, user_id INTEGER NOT NULL, airline_id INTEGER NOT NULL, aircraft_id INTEGER DEFAULT NULL, flight_number VARCHAR(255) DEFAULT NULL, route_code VARCHAR(255) DEFAULT NULL, route_leg VARCHAR(255) DEFAULT NULL, flight_type VARCHAR(255) DEFAULT 'J' NOT NULL, dpt_airport_id VARCHAR(5) NOT NULL, arr_airport_id VARCHAR(5) NOT NULL, alt_airport_id VARCHAR(5) DEFAULT NULL, level INTEGER DEFAULT NULL, distance NUMERIC(10, 0) DEFAULT NULL, planned_distance NUMERIC(10, 0) DEFAULT NULL, flight_time INTEGER DEFAULT NULL, planned_flight_time INTEGER DEFAULT NULL, zfw NUMERIC(10, 0) DEFAULT NULL, block_fuel NUMERIC(10, 0) DEFAULT NULL, fuel_used NUMERIC(10, 0) DEFAULT NULL, landing_rate NUMERIC(10, 0) DEFAULT NULL, score INTEGER DEFAULT NULL, route CLOB DEFAULT NULL, notes CLOB DEFAULT NULL, source INTEGER DEFAULT 0, source_name VARCHAR(255) DEFAULT NULL, state SMALLINT UNSIGNED DEFAULT 1 NOT NULL, status VARCHAR(255) DEFAULT 'SCH' NOT NULL, submitted_at DATETIME DEFAULT NULL, block_off_time DATETIME DEFAULT NULL, block_on_time DATETIME DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, flight_id VARCHAR(36) DEFAULT NULL, "event_id" integer, "deleted_at" datetime, PRIMARY KEY(id));
CREATE INDEX pireps_arr_airport_id_index ON pireps (arr_airport_id);
CREATE INDEX pireps_dpt_airport_id_index ON pireps (dpt_airport_id);
CREATE INDEX pireps_flight_number_index ON pireps (flight_number);
CREATE INDEX pireps_user_id_index ON pireps (user_id);
CREATE TABLE IF NOT EXISTS "typeratings" ("id" integer primary key autoincrement not null, "name" varchar not null, "type" varchar not null, "description" varchar, "image_url" varchar, "active" tinyint(1) not null default '1', "created_at" datetime, "updated_at" datetime);
CREATE UNIQUE INDEX "typeratings_id_unique" on "typeratings" ("id");
CREATE UNIQUE INDEX "typeratings_name_unique" on "typeratings" ("name");
CREATE TABLE IF NOT EXISTS "typerating_user" ("typerating_id" integer not null, "user_id" integer not null, primary key ("typerating_id", "user_id"));
CREATE INDEX "typerating_user_typerating_id_user_id_index" on "typerating_user" ("typerating_id", "user_id");
CREATE TABLE IF NOT EXISTS "typerating_subfleet" ("typerating_id" integer not null, "subfleet_id" integer not null, primary key ("typerating_id", "subfleet_id"));
CREATE INDEX "typerating_subfleet_typerating_id_subfleet_id_index" on "typerating_subfleet" ("typerating_id", "subfleet_id");
CREATE TABLE IF NOT EXISTS "events" ("id" integer not null, "type" integer not null default '0', "name" varchar not null, "description" text, "start_date" date not null, "end_date" date not null, "active" tinyint(1) default '0', "created_at" datetime, "updated_at" datetime, primary key ("id"));
CREATE TABLE pirep_fares (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, pirep_id VARCHAR(36) NOT NULL, fare_id BIGINT UNSIGNED DEFAULT NULL, count INTEGER DEFAULT 0, "code" varchar, "name" varchar, "price" numeric default '0', "cost" numeric default '0', "capacity" integer default '0', "type" integer default '0', "deleted_at" datetime);
CREATE INDEX pirep_fares_pirep_id_index ON pirep_fares (pirep_id);
CREATE UNIQUE INDEX "aircraft_fin_unique" on "aircraft" ("fin");
CREATE TABLE IF NOT EXISTS "migrations_data" ("id" integer primary key autoincrement not null, "migration" varchar not null, "batch" integer not null);
CREATE INDEX "flights_owner_type_owner_id_index" on "flights" ("owner_type", "owner_id");
CREATE TABLE IF NOT EXISTS "user_oauth_tokens" ("id" integer primary key autoincrement not null, "user_id" integer not null, "provider" varchar not null, "token" varchar not null, "refresh_token" varchar not null, "last_refreshed_at" datetime, "created_at" datetime, "updated_at" datetime);
CREATE TABLE IF NOT EXISTS "invites" ("id" integer primary key autoincrement not null, "email" varchar, "token" varchar not null, "usage_count" integer not null default '0', "usage_limit" integer, "expires_at" datetime, "created_at" datetime, "updated_at" datetime);
CREATE TABLE IF NOT EXISTS "activity_log" ("id" integer primary key autoincrement not null, "log_name" varchar, "description" text not null, "subject_type" varchar, "subject_id" varchar, "causer_type" varchar, "causer_id" integer, "properties" text, "created_at" datetime, "updated_at" datetime, "event" varchar, "batch_uuid" varchar);
CREATE INDEX "subject" on "activity_log" ("subject_type", "subject_id");
CREATE INDEX "causer" on "activity_log" ("causer_type", "causer_id");
CREATE INDEX "activity_log_log_name_index" on "activity_log" ("log_name");
INSERT INTO migrations VALUES(1,'2017_06_07_014930_create_settings_table',1);
INSERT INTO migrations VALUES(2,'2017_06_08_0000_create_users_table',1);
INSERT INTO migrations VALUES(3,'2017_06_08_0001_roles_permissions_tables',1);
INSERT INTO migrations VALUES(4,'2017_06_08_0005_create_password_resets_table',1);
INSERT INTO migrations VALUES(5,'2017_06_08_0006_create_sessions_table',1);
INSERT INTO migrations VALUES(6,'2017_06_08_191703_create_airlines_table',1);
INSERT INTO migrations VALUES(7,'2017_06_09_010621_create_aircrafts_table',1);
INSERT INTO migrations VALUES(8,'2017_06_10_040335_create_fares_table',1);
INSERT INTO migrations VALUES(9,'2017_06_11_135707_create_airports_table',1);
INSERT INTO migrations VALUES(10,'2017_06_17_214650_create_flight_tables',1);
INSERT INTO migrations VALUES(11,'2017_06_21_165410_create_ranks_table',1);
INSERT INTO migrations VALUES(12,'2017_06_23_011011_create_subfleet_tables',1);
INSERT INTO migrations VALUES(13,'2017_06_28_195426_create_pirep_tables',1);
INSERT INTO migrations VALUES(14,'2017_12_12_174519_create_bids_table',1);
INSERT INTO migrations VALUES(15,'2017_12_14_225241_create_jobs_table',1);
INSERT INTO migrations VALUES(16,'2017_12_14_225337_create_failed_jobs_table',1);
INSERT INTO migrations VALUES(17,'2017_12_20_004147_create_navdata_tables',1);
INSERT INTO migrations VALUES(18,'2017_12_20_005147_create_acars_tables',1);
INSERT INTO migrations VALUES(19,'2018_01_03_014930_create_stats_table',1);
INSERT INTO migrations VALUES(20,'2018_01_08_142204_create_news_table',1);
INSERT INTO migrations VALUES(21,'2018_01_28_180522_create_awards_table',1);
INSERT INTO migrations VALUES(22,'2018_02_26_185121_create_expenses_table',1);
INSERT INTO migrations VALUES(23,'2018_02_28_231807_create_journal_transactions_table',1);
INSERT INTO migrations VALUES(24,'2018_02_28_231813_create_journals_table',1);
INSERT INTO migrations VALUES(25,'2018_02_28_232438_create_ledgers_table',1);
INSERT INTO migrations VALUES(26,'2018_04_01_193443_create_files_table',1);
INSERT INTO migrations VALUES(27,'2019_06_19_220910_add_readonly_to_roles',1);
INSERT INTO migrations VALUES(28,'2019_07_16_141152_users_add_pilot_id',1);
INSERT INTO migrations VALUES(29,'2019_08_30_132224_create_notifications_table',1);
INSERT INTO migrations VALUES(30,'2019_09_16_141152_pireps_change_state_type',1);
INSERT INTO migrations VALUES(31,'2019_10_30_141152_pireps_add_flight_id',1);
INSERT INTO migrations VALUES(32,'2020_02_12_141152_expenses_add_flight_type',1);
INSERT INTO migrations VALUES(33,'2020_02_26_044305_modify_airports_coordinates',1);
INSERT INTO migrations VALUES(34,'2020_03_04_044305_flight_field_nullable',1);
INSERT INTO migrations VALUES(35,'2020_03_05_141152_flights_add_load_factor',1);
INSERT INTO migrations VALUES(36,'2020_03_06_141152_flights_add_pilot_pay',1);
INSERT INTO migrations VALUES(37,'2020_03_06_141153_fares_add_type',1);
INSERT INTO migrations VALUES(38,'2020_03_09_141152_increase_id_lengths',1);
INSERT INTO migrations VALUES(39,'2020_03_09_141153_remove_subfleet_type_index',1);
INSERT INTO migrations VALUES(40,'2020_03_11_141153_add_simbrief_table',1);
INSERT INTO migrations VALUES(41,'2020_03_27_174238_create_pages',1);
INSERT INTO migrations VALUES(42,'2020_03_28_174238_airline_remove_nullable',1);
INSERT INTO migrations VALUES(43,'2020_03_28_174238_page_icon_nullable',1);
INSERT INTO migrations VALUES(44,'2020_06_09_141153_pages_add_link',1);
INSERT INTO migrations VALUES(45,'2020_07_21_141153_create_user_fields',1);
INSERT INTO migrations VALUES(46,'2020_09_03_141152_aircraft_add_mtow',1);
INSERT INTO migrations VALUES(47,'2020_09_30_081536_create_modules_table',1);
INSERT INTO migrations VALUES(48,'2020_11_20_044305_modify_pages_size',1);
INSERT INTO migrations VALUES(49,'2020_11_26_044305_modify_download_link_size',1);
INSERT INTO migrations VALUES(50,'2021_01_17_044305_add_hub_to_subfleets',1);
INSERT INTO migrations VALUES(51,'2021_02_10_044305_change_acars_vs_type',1);
INSERT INTO migrations VALUES(52,'2021_02_11_044305_update_sessions_table',1);
INSERT INTO migrations VALUES(53,'2021_02_23_150601_aircraft_add_fuelonboard',1);
INSERT INTO migrations VALUES(54,'2021_02_23_205630_add_sbtype_to_subfleets',1);
INSERT INTO migrations VALUES(55,'2021_03_01_044305_add_aircraft_to_simbrief',1);
INSERT INTO migrations VALUES(56,'2021_03_05_044305_add_kvp_table',1);
INSERT INTO migrations VALUES(57,'2021_03_18_161419_add_disableactivitychecks_to_roles',1);
INSERT INTO migrations VALUES(58,'2021_03_25_213017_remove_setting_removebidonaccept',1);
INSERT INTO migrations VALUES(59,'2021_04_05_055245_flights_add_alphanumeric_callsign',1);
INSERT INTO migrations VALUES(60,'2021_04_10_055245_migrate_configs',1);
INSERT INTO migrations VALUES(61,'2021_05_21_141152_increase_icao_sizes',1);
INSERT INTO migrations VALUES(62,'2021_05_28_165608_remove_setting_simbriefexpiredays',1);
INSERT INTO migrations VALUES(63,'2021_06_01_141152_discord_fields',1);
INSERT INTO migrations VALUES(64,'2021_06_04_141152_discord_private_channel_id',1);
INSERT INTO migrations VALUES(65,'2021_11_23_184532_add_type_rating_tables',1);
INSERT INTO migrations VALUES(66,'2021_11_27_132418_add_hub_to_aircraft',1);
INSERT INTO migrations VALUES(67,'2022_01_10_131604_update_awards_add_active',1);
INSERT INTO migrations VALUES(68,'2022_02_11_124926_update_users_add_notes',1);
INSERT INTO migrations VALUES(69,'2022_03_09_152342_ignore_admin_activity_checks',1);
INSERT INTO migrations VALUES(70,'2022_07_12_142108_add_notes_to_airports',1);
INSERT INTO migrations VALUES(71,'2022_08_20_213507_add_callsign_to_airlines',1);
INSERT INTO migrations VALUES(72,'2022_12_17_211036_add_callsign_to_users',1);
INSERT INTO migrations VALUES(73,'2022_12_27_192218_create_events',1);
INSERT INTO migrations VALUES(74,'2023_01_28_174436_add_lastlogin_at_to_users',1);
INSERT INTO migrations VALUES(75,'2023_05_08_174436_add_fare_details_to_pirep',1);
INSERT INTO migrations VALUES(76,'2023_06_24_072812_add_softdelete_fields',1);
INSERT INTO migrations VALUES(77,'2023_06_24_204216_add_description_to_pirepfields',1);
INSERT INTO migrations VALUES(78,'2023_06_24_211137_add_fin_to_aircraft',1);
INSERT INTO migrations VALUES(79,'2023_08_01_211137_set_onb_to_arr',1);
INSERT INTO migrations VALUES(80,'2023_08_11_225426_add_email_verified_to_user',1);
INSERT INTO migrations VALUES(81,'2023_08_14_074828_add_aircraft_id_to_bids',1);
INSERT INTO migrations VALUES(82,'2023_08_22_192218_create_data_migrations_table',1);
INSERT INTO migrations VALUES(83,'2023_08_29_192218_add_ias_column_acars',1);
INSERT INTO migrations VALUES(84,'2023_09_26_190028_add_new_fields_to_airports',1);
INSERT INTO migrations VALUES(85,'2023_11_28_190028_flights_add_ref_fields',1);
INSERT INTO migrations VALUES(86,'2023_12_08_185109_add_pirep_source_column_to_pirepfields',1);
INSERT INTO migrations VALUES(87,'2023_12_15_154815_create_user_oauth_tokens_table',1);
INSERT INTO migrations VALUES(88,'2023_12_17_181350_add_fields_to_aircraft',1);
INSERT INTO migrations VALUES(89,'2023_12_24_091030_drop_user_oauth_tokens_foreign_keys',1);
INSERT INTO migrations VALUES(90,'2023_12_27_112456_create_invites_table',1);
INSERT INTO migrations VALUES(91,'2024_01_20_183702_create_activity_log_table',1);
INSERT INTO migrations VALUES(92,'2024_01_20_183703_add_event_column_to_activity_log_table',1);
INSERT INTO migrations VALUES(93,'2024_01_20_183704_add_batch_uuid_column_to_activity_log_table',1);