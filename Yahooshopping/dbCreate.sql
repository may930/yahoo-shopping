/*ユーザテーブル*/
create table user(
    user_id integer auto_increment primary key,
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
    phone_number varchar(20) not null unique, 
    mail_address varchar(255) not null unique,
    password varchar(255) not null,
    company_name varchar(100) not null,
    zipcode varchar(10),
    address varchar(255),
    representative varchar(50),
    store_name varchar(100) not null,
    store_name_kana varchar(100),
    introduction text,
    related_store varchar(100),
    notes text,
    created_at timestamp default current_timestamp,
    deleted_at timestamp null default null
);

/*大カテゴリテーブル*/
create table large_category(
    large_category_number integer auto_increment primary key,
    large_category_name varchar(100) not null
);

/*中カテゴリテーブル*/
create table medium_category(
    medium_category_number integer auto_increment primary key,
    large_category_number integer not null,
    medium_category_name varchar(100) not null,
    foreign key (large_category_number) references large_category(large_category_number)
);

/*小カテゴリテーブル*/
create table small_category(
    small_category_number integer auto_increment primary key,
    medium_category_number integer not null,
    small_category_name varchar(100) not null,
    foreign key (medium_category_number) references medium_category(medium_category_number)
);


/*商品テーブル*/
create table product(
    product_number integer auto_increment primary key,
    producer_id integer,
    small_category_number integer,
    product_name varchar(255) not null,
    information text,
    foreign key (producer_id) references producer(producer_id),
    foreign key (small_category_number) references small_category(small_category_number) on delete cascade
);

/*バリエーション項目テーブル*/
create table product_attributes(
    variation_id integer auto_increment primary key,
    product_number integer,
    variation_name varchar(50) not null,
    foreign key (product_number) references product(product_number)
);

/*バリエーション選択肢テーブル*/
create table product_attributes_options(
    option_id integer auto_increment primary key,
    variation_id integer,
    option_name varchar(50) not null,
    foreign key (variation_id) references product_attributes(variation_id)
);

/*商品SKUテーブル*/
create table product_sku (
    sku_id integer auto_increment primary key,
    product_number integer not null,
    price integer not null,
    stock integer default 0,
    foreign key (product_number) references product(product_number) on delete cascade
);

/*SKUと選択肢の紐付けテーブル*/
create table sku_option_mapping (
    sku_id integer not null,
    option_id integer not null,
    primary key (sku_id, option_id),
    foreign key (sku_id) references product_sku(sku_id),
    foreign key (option_id) references product_attributes_options(option_id)
);

/*商品画像テーブル*/
create table product_images(
    image_id integer auto_increment primary key,
    product_number integer not null,
    image_url varchar(255) not null,
    display_order integer default 0,
    foreign key (product_number) references product(product_number)
);

/*配送先テーブル*/
create table shipping_addresses(
    address_id integer auto_increment primary key,
    user_id integer,
    shipping_name varchar(100) not null,
    zipcode varchar(10) not null,
    address varchar(255) not null,
    foreign key (user_id) references user(user_id)
);

/*カートテーブル*/
create table cart(
    cart_id integer auto_increment primary key, 
    user_id integer,
    sku_id integer,
    quantity integer not null,
    foreign key (user_id) references user(user_id),
    foreign key (sku_id) references product_sku(sku_id),
    unique(user_id, sku_id)
);

/*注文履歴テーブル*/
create table order_history(
    order_number integer auto_increment primary key,
    user_id integer,
    shipping_name varchar(100) not null,
    zipcode varchar(10) not null,
    address varchar(255) not null,
    order_date timestamp default current_timestamp,
    total_price integer not null,
    pay varchar(50),
    shipping_status varchar(20) not null default 'ordered',
    foreign key (user_id) references user(user_id)
);
/*注文詳細テーブル*/
create table order_details(
    order_detail_id integer auto_increment primary key, 
    order_number integer,
    sku_id integer,
    product_name varchar(255) not null,
    option_name varchar(255) not null,
    quantity integer not null,
    price integer not null,
    foreign key (order_number) references order_history(order_number),
    foreign key (sku_id) references product_sku(sku_id)
);

/*レビューテーブル*/
create table product_reviews(
    review_id integer auto_increment primary key,
    order_detail_id integer not null,
    product_number integer not null,
    user_id integer not null,
    rating integer not null,
    title varchar(100),
    comment text,
    created_at timestamp default current_timestamp,
    foreign key (order_detail_id) references order_details(order_detail_id),
    foreign key (user_id) references user(user_id),
    foreign key (product_number) references product(product_number)
);