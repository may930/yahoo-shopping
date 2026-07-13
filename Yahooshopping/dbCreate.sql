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























/*  insert into (テーブル名) values(データ名, データ名, データ名);  */

/*user_account(ユーザID, 名前, 電話番号, メアド, パス, ユーザ名, 性別, 生年月日, 郵便番号, 住所, 入会日時, 退会日時)*/
insert into user_account values('1', '松本翔聖', '090-1647-3912', 'arukusandbag@gmail.com', '1645', '歩くサンドバッグ', '男', '2005/07/24', '062-0031', '北海道札幌市豊平区西岡1条10丁目1-1-3', current_timestamp, null);

/*producer(出品者ID, 電話番号, メアド, パス, 会社名, 郵便番号, 住所, 代表者名, ストア名, ストア名(フリガナ), ストア紹介, 関連ストア, 備考, 入会日時, 退会日時, ストア営業日)*/
insert into producer values('1', '011-831-5511', '20247077-matsumotoshoki@hcs.ac.jp', 'killbye0921', '北海道情報専門学校', '003-0806', '北海道札幌市白石区菊水6条3丁目4-28', '宮西哲生', '宮西のかわいいラボ', 'ミヤニシノカワイイラボ', 'かわいいものを販売しています', 'なし', '発送に1週間ほどお時間いただきます。ご了承ください。', current_timestamp, null, '土日のみ');

/*category(カテゴリID, カテゴリ名, 親カテゴリID)*/
insert into category values('1', '食品', null);
insert into category values('2', 'ドリンク、水、お酒', '1');
insert into category values('3', 'スイーツ、洋菓子', '1');
insert into category values('4', '和菓子、中華菓子', '1');
insert into category values('5', 'スナック、お菓子、おつまみ', '1');
insert into category values('6', '米、穀物、粉類', '1');
insert into category values('7', '魚介類、海産物', '1');
insert into category values('8', '肉、ハム、ソーセージ', '1');

insert into category values('9', 'ソフトドリンク、ジュース', '2');
insert into category values('10', '水、炭酸水', '2');
insert into category values('11', 'コーヒー', '2');
insert into category values('12', 'ビール、発泡酒', '2');
insert into category values('13', 'ハイボール、チューハイ', '2');

insert into category values('14', 'ナッツ類', '5');
insert into category values('15', 'おつまみ珍味', '5');
insert into category values('16', 'チョコスナック、チョコバー', '5');

insert into category values('17', '肉総菜、肉料理', '8');
insert into category values('18', '牛肉、肉ホルモン', '8');
insert into category values('19', 'ハム、ソーセージ', '8');

/*product(商品ID, 出品者ID, カテゴリID, 商品名, 商品紹介)*/
insert into product values('1', '1', '13', 'ストロングキュート', 'とってもかわいいお酒です！');
insert into product values('2', '1', '10', 'ミネラルキュート', 'とってもかわいいお水です！');
insert into product values('3', '1', '15', 'キュートな枝豆', 'とってもかわいいおつまみです！');
insert into product values('4', '1', '17', 'キュートなソーセージ', 'とってもかわいいソーセージです！');

/*product_attributes(バリエーションID, 商品ID, バリエーション名)*/
insert into product_attributes values('1', '1', '容量');
insert into product_attributes values('2', '1', '味');
insert into product_attributes values('3', '4', 'サイズ');
insert into product_attributes values('4', '4', '味');

/*product_attributes_options(選択肢ID, バリエーションID, 選択肢名, 値段, 在庫)*/
insert into product_attributes_options values('1', '1', '500ml', '150', '10000');
insert into product_attributes_options values('2', '1', '750ml', '200', '5000');
insert into product_attributes_options values('3', '1', '1000ml', '250', '7500');
insert into product_attributes_options values('4', '2', '無糖DRY', '0', '7500');

/* product_images(画像ID, 選択肢ID, 画像URL, 表示順) */
insert into product_images values('1', '1', 'https://example.com/images/strong_cute_500.jpg', '1');
insert into product_images values('2', '2', 'https://example.com/images/strong_cute_750.jpg', '1');
insert into product_images values('3', '4', 'https://example.com/images/strong_cute_dry.jpg', '1');

/* coupons(クーポンID, 出品者ID, クーポン名, 割引率, 率or実数(is_discount), 最低金額, 上限額, 期限開始, 期限終了, 無制限, 使用回数, 作成日時) */
-- 20%OFFクーポン（最低金額1000円以上、上限500円まで）
insert into coupons values('1', '1', '新規開店セールのキュートなクーポン', '20', true, '1000', '500', '2026-07-01 00:00:00', '2026-08-31 23:59:59', false, '1', current_timestamp);
insert into coupons values('2', '1', '【検証用】10%OFFクーポン(上限なし)', '10', true, '0', '0', '2026-01-01 00:00:00', '2026-12-31 23:59:59', false, '1', current_timestamp);
insert into coupons values('3', '1', '【検証用】500円定額値引きクーポン', '500', false, '1000', '500', '2026-01-01 00:00:00', '2026-12-31 23:59:59', false, '1', current_timestamp);
insert into coupons values('4', '1', '【検証用】半額クーポン(最大300円まで)', '50', true, '0', '300', '2026-01-01 00:00:00', '2026-12-31 23:59:59', false, '1', current_timestamp);

/* user_coupons(ユーザID, クーポンID, 使用判定, 使用時刻, 獲得時刻) */
insert into user_coupons values('1', '1', false, null, current_timestamp);
insert into user_coupons values('1', '2', true, current_timestamp, current_timestamp);
insert into user_coupons values('1', '3', false, null, current_timestamp);
insert into user_coupons values('1', '4', true, current_timestamp, current_timestamp);

/* shipping_addresses(配送先ID, ユーザID, お届け先名, 郵便番号, 住所) */
insert into shipping_addresses values('1', '1', '松本翔聖（実家）', '001-0010', '北海道札幌市北区北10条西4丁目');

/* cart(カートID, ユーザID, 選択肢ID, 数量) */
-- カートに「ストロングキュート 500ml」が2個入っている状態
insert into cart values('1', '1', '1', '2');

/* order_history(注文ID, ユーザID, 配送先名, 郵便番号, 住所, 注文日時, クーポンID, 割引額, 合計金額, 決済方法, 配送状況) */
insert into order_history values('1', '1', '松本翔聖', '062-0031', '北海道札幌市豊平区西岡1条10丁目1-1-3', current_timestamp, null, '0', '450', 'クレジットカード', 'ordered');
insert into order_history values('2','1','松本翔聖', '062-0031', '北海道札幌市豊平区西岡1条10丁目1-1-3', current_timestamp, '2','100','900','クレジットカード', 'ordered');
insert into order_history values('3','1','松本翔聖', '062-0031', '北海道札幌市豊平区西岡1条10丁目1-1-3', current_timestamp, '4','300','700','PayPay', 'ordered');

/* order_details(注文明細ID, 注文ID, 選択肢ID, 商品名, 選択肢名, 数量, 単価) */
insert into order_details values('1', '1', '1', 'ストロングキュート', '500ml', '3', '150');
insert into order_details values('2', '2', '2', 'ストロングキュート', '750ml', '5', '200');
insert into order_details values('3', '3', '3', 'ストロングキュート', '1000ml', '4', '250');

/* product_reviews(レビューID, 注文詳細ID, 選択肢ID, ユーザID, 評価, タイトル, コメント, 画像URL, 投稿日時, 閲覧数, いいね数) */
insert into product_reviews values('1', '1', '1', '1', '5', '最高にキュート！', 'パケ買いしましたが味も最高でした。また買います！', 'https://example.com/reviews/my_cute_drink.jpg', current_timestamp, '12', '3');

/* product_favorites(お気に入り商品ID, ユーザID, 選択肢ID, お気に入り追加日時) */
insert into product_favorites values('1', '1', '1', current_timestamp);

/* producer_favorites(お気に入り出品者ID, ユーザID, 出品者ID, お気に入り追加日時) */
insert into producer_favorites values('1', '1', '1', current_timestamp);

/* inquiry_category(問い合わせカテゴリID, 問い合わせカテゴリ名) */
insert into inquiry_category values('1', '商品について');
insert into inquiry_category values('2', '配送について');
insert into inquiry_category values('3', 'その他');

/* inquiry(問い合わせID, ユーザID, 出品者ID, 問い合わせカテゴリID, 選択肢ID, タイトル, 問い合わせ内容, 連絡先(購入者メアド), 問い合わせ日時, 公開設定) */
insert into inquiry values('1', '1', '1', '1', '1', '賞味期限について', 'ストロングキュートの賞味期限はどのくらいですか？', 'arukusandbag@gmail.com', current_timestamp, false);

/* inquiry_history(問い合わせ履歴ID, 問い合わせID, 送信内容, 送信写真, 送信者, 既読, 送信時間) */
insert into inquiry_history values('1', '1', 'ストロングキュートの賞味期限はどのくらいですか？', null, true, true, current_timestamp);
insert into inquiry_history values('2', '1', 'お問い合わせありがとうございます！製造から約半年となります。', null, false, false, current_timestamp);

/* views_history(ユーザID, 商品ID, 閲覧日時) */
insert into views_history values('1', '1', current_timestamp);
insert into views_history values('1', '2', current_timestamp);