/*テーブルを消す*/
drop table if exists views_history;
drop table if exists inquiry_history;
drop table if exists inquiry;
drop table if exists inquiry_category;
drop table if exists producer_favorites;
drop table if exists product_favorites;
drop table if exists product_reviews;
drop table if exists order_details;
drop table if exists order_history;
drop table if exists cart;
drop table if exists shipping_addresses;
drop table if exists user_coupons;
drop table if exists coupons;
drop table if exists product_images;
drop table if exists product_attributes_options;
drop table if exists product_attributes;
drop table if exists product;
drop table if exists category;
drop table if exists producer;
drop table if exists user_account;

/*ユーザテーブル*/
create table user_account(
    user_id integer auto_increment primary key,
    name varchar(20) not null,
    phone_number varchar(20) not null unique,
    mail_address varchar(255) not null unique,
    password varchar(255) not null,
    user_name varchar(100) not null,
    sex varchar(10),
    birthday date,
    zipcode varchar(10),
    address varchar(255),
    created_at timestamp default current_timestamp,
    deleted_at timestamp null default null
);

/*出品者テーブル*/
create table producer(
    producer_id integer auto_increment primary key,
    phone_number varchar(20) not null, 
    mail_address varchar(255) not null,
    password varchar(255) not null,
    company_name varchar(100) not null,
    zipcode varchar(10),
    address varchar(255),
    representative_name varchar(50),
    store_name varchar(100) not null,
    store_name_kana varchar(100),
    introduction text,
    related_store varchar(100),
    notes text,
    created_at timestamp default current_timestamp,
    deleted_at timestamp null default null,
    store_open_day date
);

/*カテゴリテーブル*/
create table category(
    category_id integer auto_increment primary key,
    category_name varchar(100) not null,
    parent_category_id integer,
    foreign key (parent_category_id) references category(category_id) on delete cascade
);

/*商品テーブル*/
create table product(
    product_id integer auto_increment primary key,
    producer_id integer,
    category_id integer,
    product_name varchar(255) not null,
    information text,
    foreign key (producer_id) references producer(producer_id),
    foreign key (category_id) references category(category_id) on delete cascade
);

/*バリエーション項目テーブル*/
create table product_attributes(
    variation_id bigint auto_increment primary key,
    product_id integer,
    variation_name varchar(50) not null,
    foreign key (product_id) references product(product_id) on delete cascade
);

/*バリエーション選択肢テーブル*/
create table product_attributes_options(
    option_id integer auto_increment primary key,
    variation_id bigint,
    option_name varchar(50) not null,
    price integer not null,
    stock integer default 0,
    foreign key (variation_id) references product_attributes(variation_id) on delete cascade
);

/*商品画像テーブル*/
create table product_images(
    image_id integer auto_increment primary key,
    option_id integer not null,
    image_url varchar(255) not null,
    display_order integer default 0,
    foreign key (option_id) references product_attributes_options(option_id) on delete cascade
);

/* クーポンマスタテーブル */
create table coupons (
    coupon_id integer auto_increment primary key,
    producer_id integer null,
    coupon_name varchar(100) not null,
    discount_rate integer not null,
    is_discount boolean,
    min_order_amount integer default 0,
    maximum_discount integer default 0,
    start_date timestamp not null,
    end_date timestamp not null default current_timestamp,
    is_infinite boolean not null default current_timestamp,
    max_use integer not null,
    created_at timestamp default current_timestamp,
    foreign key (producer_id) references producer(producer_id) on delete cascade
);

/* ユーザクーポン管理テーブル*/
create table user_coupons (
    user_id integer not null,
    coupon_id integer not null,
    is_used boolean default false not null,
    used_at timestamp null default null,
    get_at timestamp default current_timestamp,
    primary key (user_id, coupon_id),
    foreign key (user_id) references user_account(user_id) on delete cascade,
    foreign key (coupon_id) references coupons(coupon_id) on delete cascade
);

/*配送先テーブル*/
create table shipping_addresses(
    address_id integer auto_increment primary key,
    user_id integer,
    shipping_name varchar(100) not null,
    zipcode varchar(10) not null,
    address varchar(255) not null,
    foreign key (user_id) references user_account(user_id) on delete cascade
);

/*カートテーブル*/
create table cart(
    cart_id integer auto_increment primary key, 
    user_id integer unique,
    option_id integer unique,
    quantity integer not null,
    foreign key (user_id) references user_account(user_id) on delete cascade,
    foreign key (option_id) references product_attributes_options(option_id) on delete cascade
);

/*注文履歴テーブル*/
create table order_history(
    order_id integer auto_increment primary key,
    user_id integer,
    shipping_name varchar(100) not null,
    zipcode varchar(10) not null,
    address varchar(255) not null,
    order_date timestamp default current_timestamp,
    coupon_id integer,
    coupon_discount_amount integer default 0,
    total_price integer not null,
    pay varchar(50),
    shipping_status varchar(20) not null default 'ordered',
    foreign key (user_id) references user_account(user_id) on delete cascade
);

/*注文詳細テーブル*/
create table order_details(
    order_detail_id integer auto_increment primary key, 
    order_id integer,
    option_id integer,
    product_name varchar(255) not null,
    option_name varchar(255) not null,
    quantity integer not null,
    price integer not null,
    foreign key (order_id) references order_history(order_id) on delete cascade,
    foreign key (option_id) references product_attributes_options(option_id) on delete cascade
);

/*レビューテーブル*/
create table product_reviews(
    review_id integer auto_increment primary key,
    order_detail_id integer not null,
    option_id integer not null,
    user_id integer not null,
    rating integer not null,
    title varchar(100),
    comment text,
    image_url varchar(255) not null,
    created_at timestamp default current_timestamp,
    views_count integer,
    like_count integer,
    foreign key (order_detail_id) references order_details(order_detail_id) on delete cascade,
    foreign key (option_id) references product_attributes_options(option_id) on delete cascade,
    foreign key (user_id) references user_account(user_id) on delete cascade
);

/* お気に入り商品テーブル */
create table product_favorites (
    product_favorites_id integer auto_increment primary key,
    user_id integer unique,
    option_id integer unique,
    add_at timestamp default current_timestamp,
    foreign key (user_id) references user_account(user_id) on delete cascade,
    foreign key (option_id) references product_attributes_options(option_id) on delete cascade
);

/* お気に入り出品者テーブル */
create table producer_favorites (
    producer_favorites_id integer auto_increment primary key,
    user_id integer unique,
    producer_id integer unique,
    add_at timestamp default current_timestamp,
    foreign key (user_id) references user_account(user_id) on delete cascade,
    foreign key (producer_id) references producer(producer_id) on delete cascade
);

/* 問い合わせカテゴリテーブル */
create table inquiry_category (
    inquiry_category_id integer auto_increment primary key,
    inquiry_category_name varchar(100) not null
);

/* 出品者問い合わせテーブル */
create table inquiry (
    inquiry_id integer auto_increment primary key,
    user_id integer not null,
    producer_id integer not null,
    inquiry_category_id integer,
    option_id integer,
    title varchar(255) not null,
    inquiry_contents text not null,
    user_mailaddress varchar(255) not null,
    inquiry_at timestamp default current_timestamp,
    privacy boolean,
    foreign key (user_id) references user_account(user_id) on delete cascade,
    foreign key (producer_id) references producer(producer_id) on delete cascade,
    foreign key (inquiry_category_id) references inquiry_category(inquiry_category_id) on delete cascade,
    foreign key (option_id) references product_attributes_options(option_id) on delete cascade
);

/* 問い合わせ履歴テーブル */
create table inquiry_history (
    inquiry_history_id integer auto_increment primary key,
    inquiry_id integer not null,
    contents text not null,
    image_url varchar(255) null,
    sender boolean,
    is_read boolean,
    send_time timestamp default current_timestamp,
    foreign key (inquiry_id) references inquiry(inquiry_id) on delete cascade
);

/* 閲覧履歴テーブル */
create table views_history (
    user_id integer,
    product_id integer,
    view_time timestamp default current_timestamp,
    primary key(user_id, product_id),
    foreign key (user_id) references user_account(user_id) on delete cascade,
    foreign key (product_id) references product(product_id) on delete cascade
);
