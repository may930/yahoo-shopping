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
insert into user_account values('1', '松本翔聖', '09016473912', 'arukusandbag@gmail.com', '1645', '歩くサンドバッグ', '男', '2005/07/24', '062-0031', '北海道札幌市豊平区西岡1条10丁目1-1-3', current_timestamp, null);
insert into user_account values('2', '佐藤優奈', '09023456789', 'yuna_satou@example.com', '5678', 'ゆな', '女', '2001/11/03', '100-0001', '東京都千代田区千代田1-1', current_timestamp, null);
insert into user_account values('3', '田中健太', '08098765432', 'kenta_tanaka@example.com', '4321', 'ケンタ', '男', '1998/04/15', '530-0001', '大阪府大阪市北区梅田1丁目1', current_timestamp, null);
insert into user_account values('4', '鈴木美咲', '07011223344', 'misaki_suzuki@example.com', '9876', 'みさき', '女', '1995/12/25', '810-0001', '福岡県福岡市中央区天神1丁目1', current_timestamp, null);

/*producer(出品者ID, 電話番号, メアド, パス, 会社名, 郵便番号, 住所, 代表者名, ストア名, ストア名(フリガナ), ストア紹介, 関連ストア, 備考, 入会日時, 退会日時, ストア営業日)*/
insert into producer values('1', '0118315511', '20247077-matsumotoshoki@hcs.ac.jp', 'killbye0921', '北海道情報専門学校', '003-0806', '北海道札幌市白石区菊水6条3丁目4-28', '宮西哲生', '宮西のかわいいラボ', 'ミヤニシノカワイイラボ', 'かわいいものを販売しています', 'なし', '発送に1週間ほどお時間いただきます。ご了承ください。', current_timestamp, null, '土日のみ');
insert into producer values('2', '0312345678', 'tokyo_farm@example.com', 'farmtokyo2026', '東京オーガニックファーム', '101-0021', '東京都千代田区外神田1丁目2', '鈴木一郎', 'ナチュラルライフ', 'ナチュラルライフ', '新鮮な有機野菜や果物をお届けします', 'なし', 'クール便での発送となります。', current_timestamp, null, '年中無休');
insert into producer values('3', '0665432109', 'osaka_sweets@example.com', 'sweetshonpo', 'なにわスイーツ本舗', '542-0071', '大阪府大阪市中央区道頓堀1丁目3', '山田花子', '道頓堀スイーツ倶楽部', 'ドウトンボリスイーツクラブ', 'こだわりの和洋菓子を職人が手作りしています', 'なし', '水曜日と日曜日は発送業務をお休みしております。', current_timestamp, null, '月火木金土');

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
insert into product values('5', '2', '2', '厳選オーガニック緑茶', '有機栽培された茶葉100%使用の体に優しい緑茶です。');
insert into product values('6', '2', '6', 'こだわり魚沼産コシヒカリ', '産地直送のモチモチで美味しいお米です。');
insert into product values('7', '3', '3', '贅沢濃厚ショコラケーキ', '厳選されたカカオを使用した贅沢な味わい。');
insert into product values('8', '3', '4', '極み特製みたらし団子', 'もちもちのお団子に秘伝のタレを絡めました。');

/*product_attributes(バリエーションID, 商品ID, バリエーション名)*/
insert into product_attributes values('1', '1', '容量');
insert into product_attributes values('2', '1', '味');
insert into product_attributes values('3', '4', 'サイズ');
insert into product_attributes values('4', '4', '味');
insert into product_attributes values('5', '5', '容量');
insert into product_attributes values('6', '6', '容量');
insert into product_attributes values('7', '7', 'サイズ');
insert into product_attributes values('8', '8', '個数');

/*product_attributes_options(選択肢ID, バリエーションID, 選択肢名, 値段, 在庫)*/
insert into product_attributes_options values('1', '1', '500ml', '150', '10000');
insert into product_attributes_options values('2', '1', '750ml', '200', '5000');
insert into product_attributes_options values('3', '1', '1000ml', '250', '7500');
insert into product_attributes_options values('4', '2', '無糖DRY', '0', '7500');
insert into product_attributes_options values('5', '5', '500ml', '160', '3000');
insert into product_attributes_options values('6', '5', '2L', '350', '1500');
insert into product_attributes_options values('7', '6', '2kg', '1200', '800');
insert into product_attributes_options values('8', '6', '5kg', '2800', '500');
insert into product_attributes_options values('9', '7', '4号ホール', '1800', '200');
insert into product_attributes_options values('10', '7', '6号ホール', '3200', '100');
insert into product_attributes_options values('11', '8', '5本入り', '450', '1000');
insert into product_attributes_options values('12', '8', '10本入り', '850', '500');

/* product_images(画像ID, 選択肢ID, 画像URL, 表示順) */
insert into product_images values('1', '1', 'https://example.com/images/strong_cute_500.jpg', '1');
insert into product_images values('2', '2', 'https://example.com/images/strong_cute_750.jpg', '1');
insert into product_images values('3', '4', 'https://example.com/images/strong_cute_dry.jpg', '1');
insert into product_images values('4', '5', 'https://example.com/images/organic_tea_500.jpg', '1');
insert into product_images values('5', '7', 'https://example.com/images/koshihikari_2kg.jpg', '1');
insert into product_images values('6', '9', 'https://example.com/images/chocolat_cake_4.jpg', '1');
insert into product_images values('7', '11', 'https://example.com/images/mitarashi_5.jpg', '1');

/* coupons(クーポンID, 出品者ID, クーポン名, 割引率, 率or実数(is_discount), 最低金額, 上限額, 期限開始, 期限終了, 無制限, 使用回数, 作成日時) */
-- 20%OFFクーポン（最低金額1000円以上、上限500円まで）
insert into coupons values('1', '1', '新規開店セールのキュートなクーポン', '20', true, '1000', '500', '2026-07-01 00:00:00', '2026-08-31 23:59:59', false, '1', current_timestamp);
insert into coupons values('2', '1', '【検証用】10%OFFクーポン(上限なし)', '10', true, '0', '0', '2026-01-01 00:00:00', '2026-12-31 23:59:59', false, '1', current_timestamp);
insert into coupons values('3', '1', '【検証用】500円定額値引きクーポン', '500', false, '1000', '500', '2026-01-01 00:00:00', '2026-12-31 23:59:59', false, '1', current_timestamp);
insert into coupons values('4', '1', '【検証用】半額クーポン(最大300円まで)', '50', true, '0', '300', '2026-01-01 00:00:00', '2026-12-31 23:59:59', false, '1', current_timestamp);
insert into coupons values('5', '2', '新規登録感謝5%OFFクーポン', '5', true, '0', '1000', '2026-01-01 00:00:00', '2026-12-31 23:59:59', false, '1', current_timestamp);
insert into coupons values('6', '3', 'まとめ買い応援1000円割引', '1000', false, '5000', '1000', '2026-04-01 00:00:00', '2026-10-31 23:59:59', false, '1', current_timestamp);

/* user_coupons(ユーザID, クーポンID, 使用判定, 使用時刻, 獲得時刻) */
insert into user_coupons values('1', '1', false, null, current_timestamp);
insert into user_coupons values('1', '2', true, current_timestamp, current_timestamp);
insert into user_coupons values('1', '3', false, null, current_timestamp);
insert into user_coupons values('1', '4', true, current_timestamp, current_timestamp);
insert into user_coupons values('2', '5', false, null, current_timestamp);
insert into user_coupons values('3', '6', false, null, current_timestamp);
insert into user_coupons values('4', '5', true, current_timestamp, current_timestamp);

/* shipping_addresses(配送先ID, ユーザID, お届け先名, 郵便番号, 住所) */
insert into shipping_addresses values('1', '1', '松本翔聖（実家）', '001-0010', '北海道札幌市北区北10条西4丁目');
insert into shipping_addresses values('2', '2', '佐藤優奈（自宅）', '100-0001', '東京都千代田区千代田1-1');
insert into shipping_addresses values('3', '3', '田中健太（自宅）', '530-0001', '大阪府大阪市北区梅田1丁目1');
insert into shipping_addresses values('4', '4', '鈴木美咲（自宅）', '810-0001', '福岡県福岡市中央区天神1丁目1');

/* cart(カートID, ユーザID, 選択肢ID, 数量) */
-- カートに「ストロングキュート 500ml」が2個入っている状態
insert into cart values('1', '1', '1', '2');
insert into cart values('2', '2', '5', '3');
insert into cart values('3', '3', '8', '1');
insert into cart values('4', '4', '11', '2');

/* order_history(注文ID, ユーザID, 配送先名, 郵便番号, 住所, 注文日時, クーポンID, 割引額, 合計金額, 決済方法, 配送状況) */
insert into order_history values('1', '1', '松本翔聖', '062-0031', '北海道札幌市豊平区西岡1条10丁目1-1-3', current_timestamp, null, '0', '450', 'クレジットカード', 'ordered');
insert into order_history values('2','1','松本翔聖', '062-0031', '北海道札幌市豊平区西岡1条10丁目1-1-3', current_timestamp, '2','100','900','クレジットカード', 'ordered');
insert into order_history values('3','1','松本翔聖', '062-0031', '北海道札幌市豊平区西岡1条10丁目1-1-3', current_timestamp, '4','300','700','PayPay', 'ordered');
insert into order_history values('4', '2', '佐藤優奈', '100-0001', '東京都千代田区千代田1-1', current_timestamp, null, '0', '480', 'クレジットカード', 'ordered');
insert into order_history values('5', '3', '田中健太', '530-0001', '大阪府大阪市北区梅田1丁目1', current_timestamp, null, '0', '2800', 'PayPay', 'shipped');
insert into order_history values('6', '4', '鈴木美咲', '810-0001', '福岡県福岡市中央区天神1丁目1', current_timestamp, '5', '90', '1710', 'クレジットカード', 'delivered');

/* order_details(注文明細ID, 注文ID, 選択肢ID, 商品名, 選択肢名, 数量, 単価) */
insert into order_details values('1', '1', '1', 'ストロングキュート', '500ml', '3', '150');
insert into order_details values('2', '2', '2', 'ストロングキュート', '750ml', '5', '200');
insert into order_details values('3', '3', '3', 'ストロングキュート', '1000ml', '4', '250');
insert into order_details values('4', '4', '5', '厳選オーガニック緑茶', '500ml', '3', '160');
insert into order_details values('5', '5', '8', 'こだわり魚沼産コシヒカリ', '5kg', '1', '2800');
insert into order_details values('6', '6', '9', '贅沢濃厚ショコラケーキ', '4号ホール', '1', '1800');

/* product_reviews(レビューID, 注文詳細ID, 選択肢ID, ユーザID, 評価, タイトル, コメント, 画像URL, 投稿日時, 閲覧数, いいね数) */
insert into product_reviews values('1', '1', '1', '1', '5', '最高にキュート！', 'パケ買いしましたが味も最高でした。また買います！', 'https://example.com/reviews/my_cute_drink.jpg', current_timestamp, '12', '3');
insert into product_reviews values('2', '4', '5', '2', '4', 'さっぱりしていて美味しい', '普段使いにちょうどいいお茶です。また注文したいです。', null, current_timestamp, '5', '1');
insert into product_reviews values('3', '5', '8', '3', '5', 'ふっくら美味しいお米', 'モチモチした食感で、冷めても美味しく食べられました。', 'https://example.com/reviews/kome_review.jpg', current_timestamp, '20', '5');

/* product_favorites(お気に入り商品ID, ユーザID, 選択肢ID, お気に入り追加日時) */
insert into product_favorites values('1', '1', '1', current_timestamp);
insert into product_favorites values('2', '2', '5', current_timestamp);
insert into product_favorites values('3', '3', '9', current_timestamp);
insert into product_favorites values('4', '4', '11', current_timestamp);

/* producer_favorites(お気に入り出品者ID, ユーザID, 出品者ID, お気に入り追加日時) */
insert into producer_favorites values('1', '1', '1', current_timestamp);
insert into producer_favorites values('2', '2', '2', current_timestamp);
insert into producer_favorites values('3', '3', '3', current_timestamp);
insert into producer_favorites values('4', '4', '3', current_timestamp);

/* inquiry_category(問い合わせカテゴリID, 問い合わせカテゴリ名) */
insert into inquiry_category values('1', '商品について');
insert into inquiry_category values('2', '配送について');
insert into inquiry_category values('3', 'その他');

/* inquiry(問い合わせID, ユーザID, 出品者ID, 問い合わせカテゴリID, 選択肢ID, タイトル, 問い合わせ内容, 連絡先(購入者メアド), 問い合わせ日時, 公開設定) */
insert into inquiry values('1', '1', '1', '1', '1', '賞味期限について', 'ストロングキュートの賞味期限はどのくらいですか？', 'arukusandbag@gmail.com', current_timestamp, false);
insert into inquiry values('2', '2', '2', '2', '5', '配送日時の指定について', 'お届け日の指定は可能でしょうか？', 'yuna_satou@example.com', current_timestamp, false);
insert into inquiry values('3', '3', '3', '1', '9', 'アレルギー成分について', 'ショコラケーキの小麦粉の使用について教えてください。', 'kenta_tanaka@example.com', current_timestamp, true);

/* inquiry_history(問い合わせ履歴ID, 問い合わせID, 送信内容, 送信写真, 送信者, 既読, 送信時間) */
insert into inquiry_history values('1', '1', 'ストロングキュートの賞味期限はどのくらいですか？', null, true, true, current_timestamp);
insert into inquiry_history values('2', '1', 'お問い合わせありがとうございます！製造から約半年となります。', null, false, false, current_timestamp);
insert into inquiry_history values('3', '2', 'お届け日の指定は可能でしょうか？', null, true, true, current_timestamp);
insert into inquiry_history values('4', '2', 'お問い合わせありがとうございます。ご注文手続きの際に配送希望日時をご指定いただけます。', null, false, true, current_timestamp);
insert into inquiry_history values('5', '3', 'ショコラケーキの小麦粉の使用について教えてください。', null, true, false, current_timestamp);

/* views_history(ユーザID, 商品ID, 閲覧日時) */
insert into views_history values('1', '1', current_timestamp);
insert into views_history values('1', '2', current_timestamp);
insert into views_history values('2', '5', current_timestamp);
insert into views_history values('2', '6', current_timestamp);
insert into views_history values('3', '7', current_timestamp);
insert into views_history values('4', '8', current_timestamp);
