-- User
-- int id, string name, string email, bool is_admin, string password
--
-- Task
-- int id, int user_id, text description, bool is_done, bool is_edits_by_admin
create table users (
    id int primary key generated always as identity,
    name varchar(255) not null,
    email varchar(255) unique not null,
    is_admin boolean default 0,
    password varchar(255)
                   );

create table tasks (
    id int primary key generated always as identity,
    user_id int not null references users(id),
    description text not null,
    is_done boolean default 0,
    is_edits_by_admin boolean default 0
);

insert into users (name, email, is_admin, password) values ('admin', 'admin@admin.com', true, '$2y$10$wVNJZle/uxGDfAOdS5aEOupPeOd0iTQlJdWuaYtfDjWAXZaCh95q6');