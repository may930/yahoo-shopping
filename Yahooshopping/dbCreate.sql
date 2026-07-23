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
    name_kana varchar(20) not null,
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
    user_id integer,
    option_id integer,
    quantity integer not null,
    foreign key (user_id) references user_account(user_id) on delete cascade,
    foreign key (option_id) references product_attributes_options(option_id) on delete cascade,
    unique key unique_user_product(user_id, option_id)
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
    user_id integer not null,
    option_id integer not null,
    add_at timestamp default current_timestamp,
    foreign key (user_id) references user_account(user_id) on delete cascade,
    foreign key (option_id) references product_attributes_options(option_id) on delete cascade,
    unique key unique_user_product(user_id, option_id)
);

/* お気に入り出品者テーブル */
create table producer_favorites (
    producer_favorites_id integer auto_increment primary key,
    user_id integer not null,
    producer_id integer not null,
    add_at timestamp default current_timestamp,
    foreign key (user_id) references user_account(user_id) on delete cascade,
    foreign key (producer_id) references producer(producer_id) on delete cascade,
    unique key unique_user_producer (user_id, producer_id)

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




















/*user_account(ユーザID, 名前, 名前(カナ), 電話番号, メアド, パス, ユーザ名, 性別, 生年月日, 郵便番号, 住所, 入会日時, 退会日時)*/

insert into user_account values('1', '松本翔聖', 'マツモトショウキ', '09016473912', 'arukusandbag@gmail.com', '1645', '歩くサンドバッグ', '男', '2005/07/24', '062-0031', '北海道札幌市豊平区西岡1条10丁目1-1-3', current_timestamp, null);

insert into user_account values('2', '佐藤優奈', 'サトウユウナ', '09023456789', 'yuna_satou@example.com', '5678', 'ゆな', '女', '2001/11/03', '100-0001', '東京都千代田区千代田1-1', current_timestamp, null);

insert into user_account values('3', '田中健太', 'タナカケンタ', '08098765432', 'kenta_tanaka@example.com', '4321', 'ケンタ', '男', '1998/04/15', '530-0001', '大阪府大阪市北区梅田1丁目1', current_timestamp, null);

insert into user_account values('4', '鈴木美咲', 'スズキミサキ', '07011223344', 'misaki_suzuki@example.com', '9876', 'みさき', '女', '1995/12/25', '810-0001', '福岡県福岡市中央区天神1丁目1', current_timestamp, null);



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



insert into category values('20', 'レディースファッション', null);

insert into category values('21', 'コート、ジャケット', '20');

insert into category values('22', 'トップス', '20');

insert into category values('23', 'ボトムス、パンツ', '20');

insert into category values('24', 'テーラード、ブレザー', '21');

insert into category values('25', 'レインコート、レインウェア', '21');

insert into category values('26', 'ブルゾン、ジャンパー', '21');

insert into category values('27', 'Tシャツ、カットソー', '22');

insert into category values('28', 'シャツ、ブラウス', '22');

insert into category values('29', 'カーディガン、ボレロ', '22');

insert into category values('30', 'ワイド、バギーパンツ', '23');

insert into category values('31', 'スカート', '23');

insert into category values('32', 'ジーンズ、デニム', '23');



insert into category values('33', 'メンズファッション', null);

insert into category values('34', 'コート、ジャケット', '33');

insert into category values('35', 'トップス', '33');

insert into category values('36', 'ボトムス、パンツ', '33');

insert into category values('37', 'テーラード、ブレザー', '34');

insert into category values('38', 'Gジャン、デニム', '34');

insert into category values('39', 'マウンテンパーカー', '34');

insert into category values('40', 'Tシャツ、カットソー', '35');

insert into category values('41', 'シャツ、カジュアルシャツ', '35');

insert into category values('42', 'ポロシャツ', '35');

insert into category values('43', 'ショート、ハーフパンツ', '36');

insert into category values('44', 'ジーンズ、デニム', '36');

insert into category values('45', 'チノパンツ', '36');



insert into category values('46', '腕時計、アクセサリー', null);

insert into category values('47', '腕時計、懐中時計', '46');

insert into category values('48', 'ペアウォッチ', '46');

insert into category values('49', '腕時計用品', '46');

insert into category values('50', 'メンズ腕時計', '47');

insert into category values('51', 'レディース腕時計', '47');

insert into category values('52', '腕時計用ベルト、バンド', '49');

insert into category values('53', '腕時計パーツ', '49');

insert into category values('54', '工具、メンテナンス用品', '49');



insert into category values('55', 'メンズファッション', null);

insert into category values('56', 'コート、ジャケット', '55');

insert into category values('57', 'トップス', '55');

insert into category values('58', 'ボトムス、パンツ', '55');

insert into category values('59', 'テーラード、ブレザー', '56');

insert into category values('60', 'Gジャン、デニム', '56');

insert into category values('61', 'マウンテンパーカー', '56');

insert into category values('62', 'Tシャツ、カットソー', '57');

insert into category values('63', 'シャツ、カジュアルシャツ', '57');

insert into category values('64', 'ポロシャツ', '57');

insert into category values('65', 'ショート、ハーフパンツ', '58');

insert into category values('66', 'ジーンズ、デニム', '58');

insert into category values('67', 'チノパンツ', '58');



insert into category values('68', 'ダイエット、健康', null);

insert into category values('69', '暑さ対策、冷却グッズ', '67');





/*product(商品ID, 出品者ID, カテゴリID, 商品名, 商品紹介)*/

insert into product values('1', '1', '13', 'ストロングキュート', 'とってもかわいいお酒です！');

insert into product values('2', '1', '10', 'ミネラルキュート', 'とってもかわいいお水です！');

insert into product values('3', '1', '15', 'キュートな枝豆', 'とってもかわいいおつまみです！');

insert into product values('4', '1', '19', 'キュートなソーセージ', 'とってもかわいいソーセージです！');

insert into product values('5', '2', '2', '厳選オーガニック緑茶', '有機栽培された茶葉100%使用の体に優しい緑茶です。');

insert into product values('6', '2', '6', 'こだわり魚沼産コシヒカリ', '産地直送のモチモチで美味しいお米です。');

insert into product values('7', '3', '3', '贅沢濃厚ショコラケーキ', '厳選されたカカオを使用した贅沢な味わい。');

insert into product values('8', '3', '4', '極み特製みたらし団子', 'もちもちのお団子に秘伝のタレを絡めました。');



insert into product values('9', '3', '3', '十勝白い牧場アイス', '北海道の豊かな自然で育った牛乳をふんだんに使用したプレミアムアイスです。');

insert into product values('10', '2', '7', '釜石 中村家 岩手丸', 'いくら、あわび、数の子など海の幸を贅沢に漬け込んだ、ご飯が進む逸品です。');

insert into product values('11', '2', '7', '極上 豪華海鮮セット', '産地直送！新鮮なカニやホタテ、エビを詰め合わせた特製海鮮パックです。');

insert into product values('12', '2', '18', '極上 霜降り黒毛和牛ステーキ', '極上の柔らかさと、とろけるような脂の甘みをお楽しみいただける黒毛和牛です。');



insert into product values('13', '1', '37', 'スタイリッシュ ダブルブレストコート', '洗練されたシルエットでビジネスからカジュアルまで使える定番コート。');

insert into product values('14', '1', '37', 'スマート テーラードジャケット', '軽やかな着心地できちんと感を演出する、ヘビロテ間違いなしのジャケット。');

insert into product values('15', '1', '39', 'ドロップショルダー ショートジャケット', 'トレンドのルーズなシルエットが特徴的な、防風性に優れたショートジャケット。');

insert into product values('16', '1', '34', 'アーバンスタイル ミリタリージャケット', 'タフな素材感と機能美を兼ね備えた、大人のためのミリタリーアウター。');



insert into product values('17', '1', '22', 'オープンショルダー ニットワンピース', '肩見せデザインがフェミニンな、大人可愛いを叶える主役級ワンピース。');

insert into product values('18', '1', '28', 'ビッグカラー フリルブラウス', '大きな襟が顔周りを華やかに見せる、今年らしいクラシカルなブラウス。');

insert into product values('19', '1', '27', 'フロントタック プルオーバーブラウス', '上品なドレープ感が魅力の、オフィスにも使えるきれいめトップス。');

insert into product values('20', '1', '22', 'リブ ヘンリーネックカットソー', '程よいフィット感で着回し力抜群の、カジュアルなヘンリーネック。');

insert into product values('21', '1', '29', 'ボリュームスリーブ ニットカーディガン', 'ふんわりとした袖のボリュームが愛らしい、羽織るだけでサマになる一枚。');



insert into product values('22', '2', '69', '極冷 爽快汗拭きシート', '一枚で全身すっきり！圧倒的な冷涼感が持続する大判シートです。');

insert into product values('23', '2', '69', 'ポータブル手持ち扇風機 Aero Breeze', '静音設計＆パワフル風量。お出かけに便利な軽量ハンディファン。');

insert into product values('24', '2', '69', '超軽量 遮光率100% 晴雨兼用日傘', '強い日差しを完全にシャットアウト。熱中症対策に最適なコンパクト日傘。');

insert into product values('25', '2', '69', 'プレミアム真空断熱スポーツ魔法瓶', '長時間の保冷力に優れた、アウトドアやスポーツに最適な大容量ボトル。');

insert into product values('26', '2', '69', '冷却ジェルシート 冷えピタ大人用', '急な発熱や、暑くて寝苦しい夜のクールダウンに最適な冷却シート。');



/*product_attributes(バリエーションID, 商品ID, バリエーション名)*/

insert into product_attributes values('1', '1', '容量');

insert into product_attributes values('2', '1', '味');

insert into product_attributes values('3', '2', '容量');

insert into product_attributes values('4', '3', 'パック数');

insert into product_attributes values('5', '4', 'サイズ');

insert into product_attributes values('6', '5', '容量');

insert into product_attributes values('7', '6', '容量');

insert into product_attributes values('8', '7', 'サイズ');

insert into product_attributes values('9', '8', '個数');

insert into product_attributes values('10', '9', 'セット内容');

insert into product_attributes values('11', '10', '内容量');

insert into product_attributes values('12', '11', 'セット内容');

insert into product_attributes values('13', '12', '内容量');



-- メンズファッション系 (商品13〜16)

insert into product_attributes values('14', '13', 'サイズ');

insert into product_attributes values('15', '14', 'サイズ');

insert into product_attributes values('16', '15', 'サイズ');

insert into product_attributes values('17', '16', 'サイズ');



-- レディースファッション系 (商品17〜21)

insert into product_attributes values('18', '17', 'サイズ');

insert into product_attributes values('19', '18', 'サイズ');

insert into product_attributes values('20', '19', 'サイズ');

insert into product_attributes values('21', '20', 'サイズ');

insert into product_attributes values('22', '21', 'カラー');



-- 暑さ対策・冷却グッズ系 (商品22〜26)

insert into product_attributes values('23', '22', 'パック数');

insert into product_attributes values('24', '23', 'カラー');

insert into product_attributes values('25', '24', 'カラー');

insert into product_attributes values('26', '25', '容量');

insert into product_attributes values('27', '26', 'パック数');



/*product_attributes_options(選択肢ID, バリエーションID, 選択肢名, 値段, 在庫)*/

insert into product_attributes_options values('1', '1', '500ml', '150', '10000');

insert into product_attributes_options values('2', '1', '750ml', '200', '5000');

insert into product_attributes_options values('3', '2', '無糖DRY', '0', '7500');

insert into product_attributes_options values('4', '3', '550ml', '110', '8000');

insert into product_attributes_options values('5', '3', '2L', '280', '4000');

insert into product_attributes_options values('6', '4', '1パック(200g)', '300', '2000');

insert into product_attributes_options values('7', '4', 'お得用3パックセット', '850', '1000');

insert into product_attributes_options values('8', '5', '通常パック', '400', '1500');

insert into product_attributes_options values('9', '6', '500ml', '160', '3000');

insert into product_attributes_options values('10', '6', '2L', '350', '1500');

insert into product_attributes_options values('11', '7', '2kg', '1200', '800');

insert into product_attributes_options values('12', '7', '5kg', '2800', '500');

insert into product_attributes_options values('13', '8', '4号ホール', '1800', '200');

insert into product_attributes_options values('14', '8', '6号ホール', '3200', '100');

insert into product_attributes_options values('15', '9', '5本入り', '450', '1000');

insert into product_attributes_options values('16', '9', '10本入り', '850', '500');

insert into product_attributes_options values('17', '10', '8個入り詰め合わせ', '3500', '300');

insert into product_attributes_options values('18', '11', '400g（化粧箱入り）', '4800', '150');

insert into product_attributes_options values('19', '12', '特選3種盛り（カニ・ホタテ・エビ）', '8800', '100');

insert into product_attributes_options values('20', '13', '極厚ステーキ 200g×2枚', '7500', '80');

insert into product_attributes_options values('21', '14', 'Mサイズ', '12800', '50');

insert into product_attributes_options values('22', '14', 'Lサイズ', '12800', '45');

insert into product_attributes_options values('23', '15', 'Mサイズ', '8900', '60');

insert into product_attributes_options values('24', '15', 'Lサイズ', '8900', '55');

insert into product_attributes_options values('25', '16', 'Mサイズ', '9800', '40');

insert into product_attributes_options values('26', '16', 'Lサイズ', '9800', '40');

insert into product_attributes_options values('27', '17', 'Mサイズ', '11000', '30');

insert into product_attributes_options values('28', '17', 'Lサイズ', '11000', '30');

insert into product_attributes_options values('29', '18', 'Mサイズ（フリー）', '6900', '70');

insert into product_attributes_options values('30', '19', 'Mサイズ（フリー）', '4500', '80');

insert into product_attributes_options values('31', '20', 'Mサイズ（フリー）', '3900', '100');

insert into product_attributes_options values('32', '21', 'Mサイズ', '2900', '120');

insert into product_attributes_options values('33', '22', 'ベージュ', '5400', '60');

insert into product_attributes_options values('34', '22', 'ブラウン', '5400', '40');

insert into product_attributes_options values('35', '23', '30枚入り×3パックセット', '1200', '500');

insert into product_attributes_options values('36', '24', 'スノーホワイト', '1980', '250');

insert into product_attributes_options values('37', '24', 'ミントグリーン', '1980', '200');

insert into product_attributes_options values('38', '25', 'クラシックブラック', '2480', '300');

insert into product_attributes_options values('39', '26', '800ml スポーツブルー', '3200', '150');

insert into product_attributes_options values('40', '27', '12枚入り×2箱パック', '980', '400');



/* product_images(画像ID, 選択肢ID, 画像URL, 表示順) */

insert into product_images values('1', '1', 'Image/ストゼロ500.png', '1');

insert into product_images values('2', '2', 'Image/ストゼロ750.png', '1');

insert into product_images values('3', '3', 'Image/ストゼロドライ.png', '1');

insert into product_images values('4', '4', 'Image/ミネラル550.png', '1');

insert into product_images values('5', '5', 'Image/ミネラル2000.png', '1');

insert into product_images values('6', '6', 'Image/枝豆1.jpg', '1');

insert into product_images values('7', '7', 'Image/枝豆3.jpg', '1');

insert into product_images values('8', '8', 'Image/ソーセージ.webp', '1');

insert into product_images values('9', '9', 'Image/お茶500.jpg', '1');

insert into product_images values('10', '10', 'Image/お茶2000.jpg', '1');

insert into product_images values('11', '11', 'Image/コシヒカリ2.png', '1');

insert into product_images values('12', '12', 'Image/コシヒカリ5.png', '1');

insert into product_images values('13', '13', 'Image/チョコケーキ4.png', '1');

insert into product_images values('14', '14', 'Image/チョコケーキ6.png', '1');

insert into product_images values('15', '15', 'Image/みたらし5.png', '1');

insert into product_images values('16', '16', 'Image/みたらし10.png', '1');

insert into product_images values('17', '17', 'Image/アイス.webp', '1');

insert into product_images values('18', '18', 'Image/岩手丸.png', '1');

insert into product_images values('19', '19', 'Image/海鮮.webp', '1');

insert into product_images values('20', '20', 'Image/肉.webp', '1');

insert into product_images values('21', '21', 'Image/衣類メンズ ダブルブレスト.png', '1');

insert into product_images values('22', '22', 'Image/衣類メンズ ダブルブレスト.png', '1');

insert into product_images values('23', '23', 'Image/衣類メンズ テーラードジャケット.png', '1');

insert into product_images values('24', '24', 'Image/衣類メンズ テーラードジャケット.png', '1');

insert into product_images values('25', '25', 'Image/衣類メンズ ドロップショルダー・ショートジャケット.png', '1');

insert into product_images values('26', '26', 'Image/衣類メンズ ドロップショルダー・ショートジャケット.png', '1');

insert into product_images values('27', '27', 'Image/衣類メンズ ミリタリージャケット.png', '1');

insert into product_images values('28', '28', 'Image/衣類メンズ ミリタリージャケット.png', '1');

insert into product_images values('29', '29', 'Image/衣類レディース オープンショルダーニットワンピース.png', '1');

insert into product_images values('30', '30', 'Image/衣類レディース ビッグカラー.png', '1');

insert into product_images values('31', '31', 'Image/衣類レディース フロントタック.png', '1');

insert into product_images values('32', '32', 'Image/衣類レディース ヘンリーネック.png', '1');

insert into product_images values('33', '33', 'Image/衣類レディース ボリュームスリーブカーディガン.png', '1');

insert into product_images values('34', '34', 'Image/衣類レディース ボリュームスリーブカーディガン.png', '1');

insert into product_images values('35', '35', 'Image/汗拭きシート.webp', '1');

insert into product_images values('36', '36', 'Image/手持ち扇風機.png', '1');

insert into product_images values('37', '37', 'Image/手持ち扇風機.png', '1');

insert into product_images values('38', '38', 'Image/日傘.webp', '1');

insert into product_images values('39', '39', 'Image/魔法瓶.webp', '1');

insert into product_images values('40', '40', 'Image/冷えピタ.webp', '1');



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

insert into cart values('5', '1', '32', '1');

insert into cart values('6', '2', '25', '1');

insert into cart values('7', '3', '15', '1');

insert into cart values('8', '4', '34', '1');



/* order_history(注文ID, ユーザID, 配送先名, 郵便番号, 住所, 注文日時, クーポンID, 割引額, 合計金額, 決済方法, 配送状況) */

insert into order_history values('1', '1', '松本翔聖', '062-0031', '北海道札幌市豊平区西岡1条10丁目1-1-3', current_timestamp, null, '0', '450', 'クレジットカード', 'ordered');

insert into order_history values('2','1','松本翔聖', '062-0031', '北海道札幌市豊平区西岡1条10丁目1-1-3', current_timestamp, '2','100','900','クレジットカード', 'ordered');

insert into order_history values('3','1','松本翔聖', '062-0031', '北海道札幌市豊平区西岡1条10丁目1-1-3', current_timestamp, '4','300','700','PayPay', 'ordered');

insert into order_history values('4', '1', '松本翔聖', '062-0031', '北海道札幌市豊平区西岡1条10丁目1-1-3', current_timestamp, '1', '500', '12300', 'クレジットカード', 'shipped');

insert into order_history values('5', '2', '佐藤優奈（自宅）', '100-0001', '東京都千代田区千代田1-1', current_timestamp, null, '0', '3500', 'PayPay', 'delivered');

insert into order_history values('6', '3', '田中健太', '530-0001', '大阪府大阪市北区梅田1丁目1', current_timestamp, null, '0', '7950', 'クレジットカード', 'ordered');

insert into order_history values('7', '4', '鈴木美咲', '810-0001', '福岡県福岡市中央区天神1丁目1', current_timestamp, '5', '109', '2071', 'キャリア決済', 'ordered');



/* order_details(注文明細ID, 注文ID, 選択肢ID, 商品名, 選択肢名, 数量, 単価) */

insert into order_details values('1', '1', '1', 'ストロングキュート', '500ml', '3', '150');

insert into order_details values('2', '2', '2', 'ストロングキュート', '750ml', '5', '200');

insert into order_details values('3', '3', '3', 'ストロングキュート', '1000ml', '4', '250');

insert into order_details values('4', '4', '5', '厳選オーガニック緑茶', '500ml', '3', '160');

insert into order_details values('5', '5', '8', 'こだわり魚沼産コシヒカリ', '5kg', '1', '2800');

insert into order_details values('6', '6', '9', '贅沢濃厚ショコラケーキ', '4号ホール', '1', '1800');

insert into order_details values('7', '4', '17', 'スタイリッシュ ダブルブレストコート', 'Mサイズ', '1', '12800');

insert into order_details values('8', '5', '13', '十勝白い牧場アイス', '8個入り詰め合わせ', '1', '3500');

insert into order_details values('9', '6', '16', '極上 霜降り黒毛和牛ステーキ', '極厚ステーキ 200g×2枚', '1', '7500');

insert into order_details values('10', '6', '11', '極み特製みたらし団子', '5本入り', '1', '450');

insert into order_details values('11', '7', '36', '冷却ジェルシート 冷えピタ大人用', '12枚入り×2箱パック', '1', '980');

insert into order_details values('12', '7', '31', '極冷 爽快汗拭きシート', '30枚入り×3パックセット', '1', '1200');



/* product_reviews(レビューID, 注文詳細ID, 選択肢ID, ユーザID, 評価, タイトル, コメント, 画像URL, 投稿日時, 閲覧数, いいね数) */

insert into product_reviews values('1', '1', '1', '1', '5', '最高にキュート！', 'パケ買いしましたが味も最高でした。また買います！', 'https://example.com/reviews/my_cute_drink.jpg', current_timestamp, '12', '3');

insert into product_reviews values('2', '4', '5', '2', '4', 'さっぱりしていて美味しい', '普段使いにちょうどいいお茶です。また注文したいです。', 'まだ画像ないです', current_timestamp, '5', '1');

insert into product_reviews values('3', '5', '8', '3', '5', 'ふっくら美味しいお米', 'モチモチした食感で、冷めても美味しく食べられました。', 'https://example.com/reviews/kome_review.jpg', current_timestamp, '20', '5');

insert into product_reviews values('4', '8', '13', '2', '5', '濃厚で本当に美味しい！', '牧場の牛乳の味がしっかりしていて、これ以上の美味しいアイスはないです！家族大満足。', 'Image/アイス.webp', current_timestamp, '45', '15');

insert into product_reviews values('5', '7', '17', '1', '4', 'シルエットが綺麗です', 'ビジネス用に買いました。ダブルブレストのラインがスタイリッシュで気に入っています。少し薄手なので春秋向けですね。', 'Image/衣類メンズ ダブルブレスト.png', current_timestamp, '18', '2');

insert into product_reviews values('6', '11', '36', '4', '5', '夏の必需品です', '寝苦しい夜に貼ると一気に涼しくなり、安眠できます。リピ買い決定です！', 'Image/冷えピタ.webp', current_timestamp, '8', '0');



/* product_favorites(お気に入り商品ID, ユーザID, 選択肢ID, お気に入り追加日時) */

insert into product_favorites values('1', '1', '1', current_timestamp);

insert into product_favorites values('2', '2', '5', current_timestamp);

insert into product_favorites values('3', '3', '9', current_timestamp);

insert into product_favorites values('4', '4', '11', current_timestamp);

insert into product_favorites values('5', '1', '32', current_timestamp);

insert into product_favorites values('6', '1', '16', current_timestamp);

insert into product_favorites values('7', '2', '26', current_timestamp);

insert into product_favorites values('8', '3', '14', current_timestamp);

insert into product_favorites values('9', '4', '29', current_timestamp);



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

insert into inquiry values('4', '1', '1', '1', '17', 'コートのサイズ感について', '身長178cm、体重70kgですが、MサイズとLサイズどちらがおすすめですか？', 'arukusandbag@gmail.com', current_timestamp, false);

insert into inquiry values('5', '4', '2', '1', '34', '日傘のカラー展開について', '日傘のクラシックブラック以外の色が入荷する予定はありますでしょうか？', 'misaki_suzuki@example.com', current_timestamp, true);



/* inquiry_history(問い合わせ履歴ID, 問い合わせID, 送信内容, 送信写真, 送信者, 既読, 送信時間) */

insert into inquiry_history values('1', '1', 'ストロングキュートの賞味期限はどのくらいですか？', null, true, true, current_timestamp);

insert into inquiry_history values('2', '1', 'お問い合わせありがとうございます！製造から約半年となります。', null, false, false, current_timestamp);

insert into inquiry_history values('3', '2', 'お届け日の指定は可能でしょうか？', null, true, true, current_timestamp);

insert into inquiry_history values('4', '2', 'お問い合わせありがとうございます。ご注文手続きの際に配送希望日時をご指定いただけます。', null, false, true, current_timestamp);

insert into inquiry_history values('5', '3', 'ショコラケーキの小麦粉の使用について教えてください。', null, true, false, current_timestamp);

insert into inquiry_history values('6', '4', '身長178cm、体重70kgですが、MサイズとLサイズどちらがおすすめですか？', null, true, true, current_timestamp);

insert into inquiry_history values('7', '4', 'お問い合わせありがとうございます！少しゆったりめに羽織られたい場合はLサイズ、ジャストサイズでスタイリッシュに着こなしたい場合はMサイズを推奨しております。', null, false, false, current_timestamp);

insert into inquiry_history values('8', '5', '日傘のクラシックブラック以外の色が入荷する予定はありますでしょうか？', null, true, true, current_timestamp);

insert into inquiry_history values('9', '5', 'お問い合わせありがとうございます。来月上旬に「ミントブルー」および「シェルピンク」の2色を追加販売する予定でございます。今しばらくお待ちください。', null, false, false, current_timestamp);



/* views_history(ユーザID, 商品ID, 閲覧日時) */

insert into views_history values('1', '1', current_timestamp);

insert into views_history values('1', '2', current_timestamp);

insert into views_history values('2', '5', current_timestamp);

insert into views_history values('2', '6', current_timestamp);

insert into views_history values('3', '7', current_timestamp);

insert into views_history values('4', '8', current_timestamp);

insert into views_history values('1', '13', current_timestamp);

insert into views_history values('1', '23', current_timestamp);

insert into views_history values('2', '24', current_timestamp);

insert into views_history values('2', '17', current_timestamp);

insert into views_history values('3', '12', current_timestamp);

insert into views_history values('3', '9', current_timestamp);

insert into views_history values('4', '18', current_timestamp);

insert into views_history values('4', '26', current_timestamp);




-- /*  insert into (テーブル名) values(データ名, データ名, データ名);  */

-- /*user_account(ユーザID, 名前, 名前(カナ), 電話番号, メアド, パス, ユーザ名, 性別, 生年月日, 郵便番号, 住所, 入会日時, 退会日時)*/
-- /* =========================================================
--    user_account (ユーザーアカウント)
--    ========================================================= */
-- insert into user_account values('1', '松本翔聖', 'マツモトショウキ', '09016473912', 'arukusandbag@gmail.com', '1645', '歩くサンドバッグ', '男', '2005/07/24', '062-0031', '北海道札幌市豊平区西岡1条10丁目1-1-3', current_timestamp, null);
-- insert into user_account values('2', '佐藤優奈', 'サトウユウナ', '09023456789', 'yuna_satou@example.com', '5678', 'ゆな', '女', '2001/11/03', '100-0001', '東京都千代田区千代田1-1', current_timestamp, null);
-- insert into user_account values('3', '田中健太', 'タナカケンタ', '08098765432', 'kenta_tanaka@example.com', '4321', 'ケンタ', '男', '1998/04/15', '530-0001', '大阪府大阪市北区梅田1丁目1', current_timestamp, null);
-- insert into user_account values('4', '鈴木美咲', 'スズキミサキ', '07011223344', 'misaki_suzuki@example.com', '9876', 'みさき', '女', '1995/12/25', '810-0001', '福岡県福岡市中央区天神1丁目1', current_timestamp, null);

-- /* =========================================================
--    producer (出品者)
--    ========================================================= */
-- insert into producer values('1', '0118315511', '20247077-matsumotoshoki@hcs.ac.jp', 'killbye0921', '北海道情報専門学校', '003-0806', '北海道札幌市白石区菊水6条3丁目4-28', '宮西哲生', '宮西のかわいいラボ', 'ミヤニシノカワイイラボ', 'かわいいものを販売しています', 'なし', '発送に1週間ほどお時間いただきます。ご了承ください。', current_timestamp, null, '土日のみ');
-- insert into producer values('2', '0312345678', 'tokyo_farm@example.com', 'farmtokyo2026', '東京オーガニックファーム', '101-0021', '東京都千代田区外神田1丁目2', '鈴木一郎', 'ナチュラルライフ', 'ナチュラルライフ', '新鮮な有機野菜や果物をお届けします', 'なし', 'クール便での発送となります。', current_timestamp, null, '年中無休');
-- insert into producer values('3', '0665432109', 'osaka_sweets@example.com', 'sweetshonpo', 'なにわスイーツ本舗', '542-0071', '大阪府大阪市中央区道頓堀1丁目3', '山田花子', '道頓堀スイーツ倶楽部', 'ドウトンボリスイーツクラブ', 'こだわりの和洋菓子を職人が手作りしています', 'なし', '水曜日と日曜日は発送業務をお休みしております。', current_timestamp, null, '月火木金土');

-- /* =========================================================
--    category (カテゴリ) ※暑さ対策の親ID不整合を68に修正済み
--    ========================================================= */
-- insert into category values('1', '食品', null);
-- insert into category values('2', 'ドリンク、水、お酒', '1');
-- insert into category values('3', 'スイーツ、洋菓子', '1');
-- insert into category values('4', '和菓子、中華菓子', '1');
-- insert into category values('5', 'スナック、お菓子、おつまみ', '1');
-- insert into category values('6', '米、穀物、粉類', '1');
-- insert into category values('7', '魚介類、海産物', '1');
-- insert into category values('8', '肉、ハム、ソーセージ', '1');
-- insert into category values('9', 'ソフトドリンク、ジュース', '2');
-- insert into category values('10', '水、炭酸水', '2');
-- insert into category values('11', 'コーヒー', '2');
-- insert into category values('12', 'ビール、発泡酒', '2');
-- insert into category values('13', 'ハイボール、チューハイ', '2');
-- insert into category values('14', 'ナッツ類', '5');
-- insert into category values('15', 'おつまみ珍味', '5');
-- insert into category values('16', 'チョコスナック、チョコバー', '5');
-- insert into category values('17', '肉総菜、肉料理', '8');
-- insert into category values('18', '牛肉、肉ホルモン', '8');
-- insert into category values('19', 'ハム、ソーセージ', '8');

-- insert into category values('20', 'レディースファッション', null);
-- insert into category values('21', 'コート、ジャケット', '20');
-- insert into category values('22', 'トップス', '20');
-- insert into category values('23', 'ボトムス、パンツ', '20');
-- insert into category values('24', 'テーラード、ブレザー', '21');
-- insert into category values('25', 'レインコート、レインウェア', '21');
-- insert into category values('26', 'ブルゾン、ジャンパー', '21');
-- insert into category values('27', 'Tシャツ、カットソー', '22');
-- insert into category values('28', 'シャツ、ブラウス', '22');
-- insert into category values('29', 'カーディガン、ボレロ', '22');
-- insert into category values('30', 'ワイド、バギーパンツ', '23');
-- insert into category values('31', 'スカート', '23');
-- insert into category values('32', 'ジーンズ、デニム', '23');

-- insert into category values('33', 'メンズファッション', null);
-- insert into category values('34', 'コート、ジャケット', '33');
-- insert into category values('35', 'トップス', '33');
-- insert into category values('36', 'ボトムス、パンツ', '33');
-- insert into category values('37', 'テーラード、ブレザー', '34');
-- insert into category values('38', 'Gジャン、デニム', '34');
-- insert into category values('39', 'マウンテンパーカー', '34');
-- insert into category values('40', 'Tシャツ、カットソー', '35');
-- insert into category values('41', 'シャツ、カジュアルシャツ', '35');
-- insert into category values('42', 'ポロシャツ', '35');
-- insert into category values('43', 'ショート、ハーフパンツ', '36');
-- insert into category values('44', 'ジーンズ、デニム', '36');
-- insert into category values('45', 'チノパンツ', '36');

-- insert into category values('46', '腕時計、アクセサリー', null);
-- insert into category values('47', '腕時計、懐中時計', '46');
-- insert into category values('48', 'ペアウォッチ', '46');
-- insert into category values('49', '腕時計用品', '46');
-- insert into category values('50', 'メンズ腕時計', '47');
-- insert into category values('51', 'レディース腕時計', '47');
-- insert into category values('52', '腕時計用ベルト、バンド', '49');
-- insert into category values('53', '腕時計パーツ', '49');
-- insert into category values('54', '工具、メンテナンス用品', '49');

-- insert into category values('55', 'メンズファッション(予備)', null);
-- insert into category values('56', 'コート、ジャケット', '55');
-- insert into category values('57', 'トップス', '55');
-- insert into category values('58', 'ボトムス、パンツ', '55');
-- insert into category values('59', 'テーラード、ブレザー', '56');
-- insert into category values('60', 'Gジャン、デニム', '56');
-- insert into category values('61', 'マウンテンパーカー', '56');
-- insert into category values('62', 'Tシャツ、カットソー', '57');
-- insert into category values('63', 'シャツ、カジュアルシャツ', '57');
-- insert into category values('64', 'ポロシャツ', '57');
-- insert into category values('65', 'ショート、ハーフパンツ', '58');
-- insert into category values('66', 'ジーンズ、デニム', '58');
-- insert into category values('67', 'チノパンツ', '58');

-- insert into category values('68', 'ダイエット、健康', null);
-- insert into category values('69', '暑さ対策、冷却グッズ', '68');

-- /* =========================================================
--    product (商品)
--    ========================================================= */
-- insert into product values('1', '1', '13', 'ストロングキュート', 'とってもかわいいお酒です！');
-- insert into product values('2', '1', '10', 'ミネラルキュート', 'とってもかわいいお水です！');
-- insert into product values('3', '1', '15', 'キュートな枝豆', 'とってもかわいいおつまみです！');
-- insert into product values('4', '1', '17', 'キュートなソーセージ', 'とってもかわいいソーセージです！');
-- insert into product values('5', '2', '2', '厳選オーガニック緑茶', '有機栽培された茶葉100%使用の体に優しい緑茶です。');
-- insert into product values('6', '2', '6', 'こだわり魚沼産コシヒカリ', '産地直送のモチモチで美味しいお米です。');
-- insert into product values('7', '3', '3', '贅沢濃厚ショコラケーキ', '厳選されたカカオを使用した贅沢な味わい。');
-- insert into product values('8', '3', '4', '極み特製みたらし団子', 'もちもちのお団子に秘伝のタレを絡めました。');
-- insert into product values('9', '3', '3', '十勝白い牧場アイス', '北海道の豊かな自然で育った牛乳をふんだんに使用したプレミアムアイスです。');
-- insert into product values('10', '2', '7', '釜石 中村家 岩手丸', 'いくら、あわび、数の子など海の幸を贅沢に漬け込んだ、ご飯が進む逸品です。');
-- insert into product values('11', '2', '7', '極上 豪華海鮮セット', '産地直送！新鮮なカニやホタテ、エビを詰め合わせた特製海鮮パックです。');
-- insert into product values('12', '2', '18', '極上 霜降り黒毛和牛ステーキ', '極上の柔らかさと、とろけるような脂の甘みをお楽しみいただける黒毛和牛です。');
-- insert into product values('13', '1', '37', 'スタイリッシュ ダブルブレストコート', '洗練されたシルエットでビジネスからカジュアルまで使える定番コート。');
-- insert into product values('14', '1', '37', 'スマート テーラードジャケット', '軽やかな着心地できちんと感を演出する、ヘビロテ間違いなしのジャケット。');
-- insert into product values('15', '1', '39', 'ドロップショルダー ショートジャケット', 'トレンドのルーズなシルエットが特徴的な、防風性に優れたショートジャケット。');
-- insert into product values('16', '1', '34', 'アーバンスタイル ミリタリージャケット', 'タフな素材感と機能美を兼ね備えた、大人のためのミリタリーアウター。');
-- insert into product values('17', '1', '22', 'オープンショルダー ニットワンピース', '肩見せデザインがフェミニンな、大人可愛いを叶える主役級ワンピース。');
-- insert into product values('18', '1', '28', 'ビッグカラー フリルブラウス', '大きな襟が顔周りを華やかに見せる、今年らしいクラシカルなブラウス。');
-- insert into product values('19', '1', '27', 'フロントタック プルオーバーブラウス', '上品なドレープ感が魅力の、オフィスにも使えるきれいめトップス。');
-- insert into product values('20', '1', '22', 'リブ ヘンリーネックカットソー', '程よいフィット感で着回し力抜群の、カジュアルなヘンリーネック。');
-- insert into product values('21', '1', '29', 'ボリュームスリーブ ニットカーディガン', 'ふんわりとした袖のボリュームが愛らしい、羽織るだけでサマになる一枚。');
-- insert into product values('22', '2', '69', '極冷 爽快汗拭きシート', '一枚で全身すっきり！圧倒的な冷涼感が持続する大判シートです。');
-- insert into product values('23', '2', '69', 'ポータブル手持ち扇風機 Aero Breeze', '静音設計＆パワフル風量。お出かけに便利な軽量ハンディファン。');
-- insert into product values('24', '2', '69', '超軽量 遮光率100% 晴雨兼用日傘', '強い日差しを完全にシャットアウト。熱中症対策に最適なコンパクト日傘。');
-- insert into product values('25', '2', '69', 'プレミアム真空断熱スポーツ魔法瓶', '長時間の保冷力に優れた、アウトドアやスポーツに最適な大容量ボトル。');
-- insert into product values('26', '2', '69', '冷却ジェルシート 冷えピタ大人用', '急な発熱や、暑くて寝苦しい夜のクールダウンに最適な冷却シート。');

-- /* =========================================================
--    product_attributes (商品バリエーション)
--    ========================================================= */
-- insert into product_attributes values('1', '1', '容量');
-- insert into product_attributes values('2', '1', '味');
-- insert into product_attributes values('3', '2', '容量');
-- insert into product_attributes values('4', '3', 'パック数');
-- insert into product_attributes values('5', '4', 'サイズ');
-- insert into product_attributes values('6', '5', '容量');
-- insert into product_attributes values('7', '6', '容量');
-- insert into product_attributes values('8', '7', 'サイズ');
-- insert into product_attributes values('9', '8', '個数');
-- insert into product_attributes values('10', '9', 'セット内容');
-- insert into product_attributes values('11', '10', '内容量');
-- insert into product_attributes values('12', '11', 'セット内容');
-- insert into product_attributes values('13', '12', '内容量');

-- -- メンズファッション系
-- insert into product_attributes values('14', '13', 'サイズ');
-- insert into product_attributes values('15', '14', 'サイズ');
-- insert into product_attributes values('16', '15', 'サイズ');
-- insert into product_attributes values('17', '16', 'サイズ');

-- -- レディースファッション系
-- insert into product_attributes values('18', '17', 'サイズ');
-- insert into product_attributes values('19', '18', 'サイズ');
-- insert into product_attributes values('20', '19', 'サイズ');
-- insert into product_attributes values('21', '20', 'サイズ');
-- insert into product_attributes values('22', '21', 'カラー');

-- -- 暑さ対策・冷却グッズ系
-- insert into product_attributes values('23', '22', 'パック数');
-- insert into product_attributes values('24', '23', 'カラー');
-- insert into product_attributes values('25', '24', 'カラー');
-- insert into product_attributes values('26', '25', '容量');
-- insert into product_attributes values('27', '26', 'パック数');

-- /* =========================================================
--    product_attributes_options (バリエーション選択肢)
--    ========================================================= */
-- insert into product_attributes_options values('1', '1', '500ml', '150', '10000');
-- insert into product_attributes_options values('2', '1', '750ml', '200', '5000');
-- insert into product_attributes_options values('3', '2', '無糖DRY', '0', '7500');
-- insert into product_attributes_options values('4', '3', '550ml', '110', '8000');
-- insert into product_attributes_options values('5', '3', '2L', '280', '4000');
-- insert into product_attributes_options values('6', '4', '1パック(200g)', '300', '2000');
-- insert into product_attributes_options values('7', '4', 'お得用3パックセット', '850', '1000');
-- insert into product_attributes_options values('8', '5', '通常パック', '400', '1500');
-- insert into product_attributes_options values('9', '6', '500ml', '160', '3000');
-- insert into product_attributes_options values('10', '6', '2L', '350', '1500');
-- insert into product_attributes_options values('11', '7', '2kg', '1200', '800');
-- insert into product_attributes_options values('12', '7', '5kg', '2800', '500');
-- insert into product_attributes_options values('13', '8', '4号ホール', '1800', '200');
-- insert into product_attributes_options values('14', '8', '6号ホール', '3200', '100');
-- insert into product_attributes_options values('15', '9', '5本入り', '450', '1000');
-- insert into product_attributes_options values('16', '9', '10本入り', '850', '500');
-- insert into product_attributes_options values('17', '10', '8個入り詰め合わせ', '3500', '300');
-- insert into product_attributes_options values('18', '11', '400g（化粧箱入り）', '4800', '150');
-- insert into product_attributes_options values('19', '12', '特選3種盛り（カニ・ホタテ・エビ）', '8800', '100');
-- insert into product_attributes_options values('20', '13', '極厚ステーキ 200g×2枚', '7500', '80');

-- insert into product_attributes_options values('21', '14', 'Mサイズ', '12800', '50');
-- insert into product_attributes_options values('22', '14', 'Lサイズ', '12800', '45');
-- insert into product_attributes_options values('23', '15', 'Mサイズ', '8900', '60');
-- insert into product_attributes_options values('24', '15', 'Lサイズ', '8900', '55');
-- insert into product_attributes_options values('25', '16', 'Mサイズ', '9800', '40');
-- insert into product_attributes_options values('26', '16', 'Lサイズ', '9800', '40');
-- insert into product_attributes_options values('27', '17', 'Mサイズ', '11000', '30');
-- insert into product_attributes_options values('28', '17', 'Lサイズ', '11000', '30');

-- insert into product_attributes_options values('29', '18', 'Mサイズ（フリー）', '6900', '70');
-- insert into product_attributes_options values('30', '19', 'Mサイズ（フリー）', '4500', '80');
-- insert into product_attributes_options values('31', '20', 'Mサイズ（フリー）', '3900', '100');
-- insert into product_attributes_options values('32', '21', 'Mサイズ', '2900', '120');
-- insert into product_attributes_options values('33', '22', 'ベージュ', '5400', '60');
-- insert into product_attributes_options values('34', '22', 'ブラウン', '5400', '40');

-- insert into product_attributes_options values('35', '23', '30枚入り×3パックセット', '1200', '500');
-- insert into product_attributes_options values('36', '24', 'スノーホワイト', '1980', '250');
-- insert into product_attributes_options values('37', '24', 'ミントグリーン', '1980', '200');
-- insert into product_attributes_options values('38', '25', 'クラシックブラック', '2480', '300');
-- insert into product_attributes_options values('39', '26', '800ml スポーツブルー', '3200', '150');
-- insert into product_attributes_options values('40', '27', '12枚入り×2箱パック', '980', '400');

-- /* =========================================================
--    product_images (商品画像) ※画像と選択肢IDの紐付けを全面修正済み
--    ========================================================= */
-- insert into product_images values('1', '1', 'localhost/yahoo-shopping/yahoo-shopping/Yahooshopping/Image/ストゼロ500.png', '1');
-- insert into product_images values('2', '2', 'Image/ストゼロ750.png', '1');
-- insert into product_images values('3', '3', 'Image/ストゼロドライ.png', '1');

-- insert into product_images values('4', '4', 'Image/ミネラル550.png', '1');
-- insert into product_images values('5', '5', 'Image/ミネラル2000.png', '1');

-- insert into product_images values('6', '6', 'Image/枝豆1.jpg', '1');
-- insert into product_images values('7', '7', 'Image/枝豆3.jpg', '1');

-- insert into product_images values('8', '8', 'Image/ソーセージ.webp', '1');

-- insert into product_images values('9', '9', 'Image/お茶500.jpg', '1');
-- insert into product_images values('10', '10', 'Image/お茶2000.jpg', '1');

-- insert into product_images values('11', '11', 'Image/コシヒカリ2.png', '1');
-- insert into product_images values('12', '12', 'Image/コシヒカリ5.png', '1');

-- insert into product_images values('13', '13', 'Image/チョコケーキ4.png', '1');
-- insert into product_images values('14', '14', 'Image/チョコケーキ6.png', '1');

-- insert into product_images values('15', '15', 'Image/みたらし5.png', '1');
-- insert into product_images values('16', '16', 'Image/みたらし10.png', '1');

-- insert into product_images values('17', '17', 'Image/アイス.webp', '1');
-- insert into product_images values('18', '18', 'Image/岩手丸.png', '1');
-- insert into product_images values('19', '19', 'Image/海鮮.webp', '1');
-- insert into product_images values('20', '20', 'Image/肉.webp', '1');

-- insert into product_images values('21', '21', 'Image/衣類メンズ ダブルブレスト.png', '1');
-- insert into product_images values('22', '22', 'Image/衣類メンズ ダブルブレスト.png', '1');
-- insert into product_images values('23', '23', 'Image/衣類メンズ テーラードジャケット.png', '1');
-- insert into product_images values('24', '24', 'Image/衣類メンズ テーラードジャケット.png', '1');
-- insert into product_images values('25', '25', 'Image/衣類メンズ ドロップショルダー・ショートジャケット.png', '1');
-- insert into product_images values('26', '26', 'Image/衣類メンズ ドロップショルダー・ショートジャケット.png', '1');
-- insert into product_images values('27', '27', 'Image/衣類メンズ ミリタリージャケット.png', '1');
-- insert into product_images values('28', '28', 'Image/衣類メンズ ミリタリージャケット.png', '1');

-- insert into product_images values('29', '29', 'Image/衣類レディース オープンショルダーニットワンピース.png', '1');
-- insert into product_images values('30', '30', 'Image/衣類レディース ビッグカラー.png', '1');
-- insert into product_images values('31', '31', 'Image/衣類レディース フロントタック.png', '1');
-- insert into product_images values('32', '32', 'Image/衣類レディース ヘンリーネック.png', '1');
-- insert into product_images values('33', '33', 'Image/衣類レディース ボリュームスリーブカーディガン.png', '1');
-- insert into product_images values('34', '34', 'Image/衣類レディース ボリュームスリーブカーディガン.png', '1');

-- insert into product_images values('35', '35', 'Image/汗拭きシート.webp', '1');
-- insert into product_images values('36', '36', 'Image/手持ち扇風機.png', '1');
-- insert into product_images values('37', '37', 'Image/手持ち扇風機.png', '1');
-- insert into product_images values('38', '38', 'Image/日傘.webp', '1');
-- insert into product_images values('39', '39', 'Image/魔法瓶.webp', '1');
-- insert into product_images values('40', '40', 'Image/冷えピタ.webp', '1');

-- /* =========================================================
--    coupons (クーポン)
--    ========================================================= */
-- insert into coupons values('1', '1', '新規開店セールのキュートなクーポン', '20', true, '1000', '500', '2026-07-01 00:00:00', '2026-08-31 23:59:59', false, '1', current_timestamp);
-- insert into coupons values('2', '1', '【検証用】10%OFFクーポン(上限なし)', '10', true, '0', '0', '2026-01-01 00:00:00', '2026-12-31 23:59:59', false, '1', current_timestamp);
-- insert into coupons values('3', '1', '【検証用】500円定額値引きクーポン', '500', false, '1000', '500', '2026-01-01 00:00:00', '2026-12-31 23:59:59', false, '1', current_timestamp);
-- insert into coupons values('4', '1', '【検証用】半額クーポン(最大300円まで)', '50', true, '0', '300', '2026-01-01 00:00:00', '2026-12-31 23:59:59', false, '1', current_timestamp);
-- insert into coupons values('5', '2', '新規登録感謝5%OFFクーポン', '5', true, '0', '1000', '2026-01-01 00:00:00', '2026-12-31 23:59:59', false, '1', current_timestamp);
-- insert into coupons values('6', '3', 'まとめ買い応援1000円割引', '1000', false, '5000', '1000', '2026-04-01 00:00:00', '2026-10-31 23:59:59', false, '1', current_timestamp);

-- /* =========================================================
--    user_coupons (ユーザー保有クーポン)
--    ========================================================= */
-- insert into user_coupons values('1', '1', false, null, current_timestamp);
-- insert into user_coupons values('1', '2', true, current_timestamp, current_timestamp);
-- insert into user_coupons values('1', '3', false, null, current_timestamp);
-- insert into user_coupons values('1', '4', true, current_timestamp, current_timestamp);
-- insert into user_coupons values('2', '5', false, null, current_timestamp);
-- insert into user_coupons values('3', '6', false, null, current_timestamp);
-- insert into user_coupons values('4', '5', true, current_timestamp, current_timestamp);

-- /* =========================================================
--    shipping_addresses (お届け先住所)
--    ========================================================= */
-- insert into shipping_addresses values('1', '1', '松本翔聖（実家）', '001-0010', '北海道札幌市北区北10条西4丁目');
-- insert into shipping_addresses values('2', '2', '佐藤優奈（自宅）', '100-0001', '東京都千代田区千代田1-1');
-- insert into shipping_addresses values('3', '3', '田中健太（自宅）', '530-0001', '大阪府大阪市北区梅田1丁目1');
-- insert into shipping_addresses values('4', '4', '鈴木美咲（自宅）', '810-0001', '福岡県福岡市中央区天神1丁目1');

-- /* =========================================================
--    cart (カート)
--    ========================================================= */
-- insert into cart values('1', '1', '1', '2');
-- insert into cart values('2', '2', '5', '3');
-- insert into cart values('3', '3', '8', '1');
-- insert into cart values('4', '4', '11', '2');
-- insert into cart values('5', '1', '32', '1');
-- insert into cart values('6', '2', '25', '1');
-- insert into cart values('7', '3', '15', '1');
-- insert into cart values('8', '4', '34', '1');

-- /* =========================================================
--    order_history (注文履歴)
--    ========================================================= */
-- insert into order_history values('1', '1', '松本翔聖', '062-0031', '北海道札幌市豊平区西岡1条10丁目1-1-3', current_timestamp, null, '0', '450', 'クレジットカード', 'ordered');
-- insert into order_history values('2', '1', '松本翔聖', '062-0031', '北海道札幌市豊平区西岡1条10丁目1-1-3', current_timestamp, '2', '100', '900', 'クレジットカード', 'ordered');
-- insert into order_history values('3', '1', '松本翔聖', '062-0031', '北海道札幌市豊平区西岡1条10丁目1-1-3', current_timestamp, '4', '300', '700', 'PayPay', 'ordered');
-- insert into order_history values('4', '1', '松本翔聖', '062-0031', '北海道札幌市豊平区西岡1条10丁目1-1-3', current_timestamp, '1', '500', '12300', 'クレジットカード', 'shipped');
-- insert into order_history values('5', '2', '佐藤優奈（自宅）', '100-0001', '東京都千代田区千代田1-1', current_timestamp, null, '0', '3500', 'PayPay', 'delivered');
-- insert into order_history values('6', '3', '田中健太', '530-0001', '大阪府大阪市北区梅田1丁目1', current_timestamp, null, '0', '7950', 'クレジットカード', 'ordered');
-- insert into order_history values('7', '4', '鈴木美咲', '810-0001', '福岡県福岡市中央区天神1丁目1', current_timestamp, '5', '109', '2071', 'キャリア決済', 'ordered');

-- /* =========================================================
--    order_details (注文明細)
--    ========================================================= */
-- insert into order_details values('1', '1', '1', 'ストロングキュート', '500ml', '3', '150');
-- insert into order_details values('2', '2', '2', 'ストロングキュート', '750ml', '5', '200');
-- insert into order_details values('3', '3', '3', 'ストロングキュート', '1000ml', '4', '250');
-- insert into order_details values('4', '4', '5', '厳選オーガニック緑茶', '500ml', '3', '160');
-- insert into order_details values('5', '5', '8', 'こだわり魚沼産コシヒカリ', '5kg', '1', '2800');
-- insert into order_details values('6', '6', '9', '贅沢濃厚ショコラケーキ', '4号ホール', '1', '1800');
-- insert into order_details values('7', '4', '17', 'スタイリッシュ ダブルブレストコート', 'Mサイズ', '1', '12800');
-- insert into order_details values('8', '5', '13', '十勝白い牧場アイス', '8個入り詰め合わせ', '1', '3500');
-- insert into order_details values('9', '6', '16', '極上 霜降り黒毛和牛ステーキ', '極厚ステーキ 200g×2枚', '1', '7500');
-- insert into order_details values('10', '6', '11', '極み特製みたらし団子', '5本入り', '1', '450');
-- insert into order_details values('11', '7', '40', '冷却ジェルシート 冷えピタ大人用', '12枚入り×2箱パック', '1', '980');
-- insert into order_details values('12', '7', '35', '極冷 爽快汗拭きシート', '30枚入り×3パックセット', '1', '1200');

-- /* =========================================================
--    product_reviews (商品レビュー)
--    ========================================================= */
-- insert into product_reviews values('1', '1', '1', '1', '5', '最高にキュート！', 'パケ買いしましたが味も最高でした。また買います！', 'https://example.com/reviews/my_cute_drink.jpg', current_timestamp, '12', '3');
-- insert into product_reviews values('2', '4', '5', '2', '4', 'さっぱりしていて美味しい', '普段使いにちょうどいいお茶です。また注文したいです。', 'まだ画像ないです', current_timestamp, '5', '1');
-- insert into product_reviews values('3', '5', '8', '3', '5', 'ふっくら美味しいお米', 'モチモチした食感で、冷めても美味しく食べられました。', 'https://example.com/reviews/kome_review.jpg', current_timestamp, '20', '5');
-- insert into product_reviews values('4', '8', '17', '2', '5', '濃厚で本当に美味しい！', '牧場の牛乳の味がしっかりしていて、これ以上の美味しいアイスはないです！家族大満足。', 'Image/アイス.webp', current_timestamp, '45', '15');
-- insert into product_reviews values('5', '7', '21', '1', '4', 'シルエットが綺麗です', 'ビジネス用に買いました。ダブルブレストのラインがスタイリッシュで気に入っています。少し薄手なので春秋向けですね。', 'Image/衣類メンズ ダブルブレスト.png', current_timestamp, '18', '2');
-- insert into product_reviews values('6', '11', '40', '4', '5', '夏の必需品です', '寝苦しい夜に貼ると一気に涼しくなり、安眠できます。リピ買い決定です！', 'Image/冷えピタ.webp', current_timestamp, '8', '0');

-- /* =========================================================
--    product_favorites (お気に入り商品)
--    ========================================================= */
-- insert into product_favorites values('1', '1', '1', current_timestamp);
-- insert into product_favorites values('2', '2', '5', current_timestamp);
-- insert into product_favorites values('3', '3', '9', current_timestamp);
-- insert into product_favorites values('4', '4', '11', current_timestamp);
-- insert into product_favorites values('5', '1', '32', current_timestamp);
-- insert into product_favorites values('6', '1', '16', current_timestamp);
-- insert into product_favorites values('7', '2', '26', current_timestamp);
-- insert into product_favorites values('8', '3', '14', current_timestamp);
-- insert into product_favorites values('9', '4', '29', current_timestamp);

-- /* =========================================================
--    producer_favorites (お気に入り出品者)
--    ========================================================= */
-- insert into producer_favorites values('1', '1', '1', current_timestamp);
-- insert into producer_favorites values('2', '2', '2', current_timestamp);
-- insert into producer_favorites values('3', '3', '3', current_timestamp);
-- insert into producer_favorites values('4', '4', '3', current_timestamp);

-- /* =========================================================
--    inquiry_category (問い合わせカテゴリ)
--    ========================================================= */
-- insert into inquiry_category values('1', '商品について');
-- insert into inquiry_category values('2', '配送について');
-- insert into inquiry_category values('3', 'その他');

-- /* =========================================================
--    inquiry (問い合わせ)
--    ========================================================= */
-- insert into inquiry values('1', '1', '1', '1', '1', '賞味期限について', 'ストロングキュートの賞味期限はどのくらいですか？', 'arukusandbag@gmail.com', current_timestamp, false);
-- insert into inquiry values('2', '2', '2', '2', '5', '配送日時の指定について', 'お届け日の指定は可能でしょうか？', 'yuna_satou@example.com', current_timestamp, false);
-- insert into inquiry values('3', '3', '3', '1', '9', 'アレルギー成分について', 'ショコラケーキの小麦粉の使用について教えてください。', 'kenta_tanaka@example.com', current_timestamp, true);
-- insert into inquiry values('4', '1', '1', '1', '21', 'コートのサイズ感について', '身長178cm、体重70kgですが、MサイズとLサイズどちらがおすすめですか？', 'arukusandbag@gmail.com', current_timestamp, false);
-- insert into inquiry values('5', '4', '2', '1', '38', '日傘のカラー展開について', '日傘のクラシックブラック以外の色が入荷する予定はありますでしょうか？', 'misaki_suzuki@example.com', current_timestamp, true);

-- /* =========================================================
--    inquiry_history (問い合わせ履歴)
--    ========================================================= */
-- insert into inquiry_history values('1', '1', 'ストロングキュートの賞味期限はどのくらいですか？', null, true, true, current_timestamp);
-- insert into inquiry_history values('2', '1', 'お問い合わせありがとうございます！製造から約半年となります。', null, false, false, current_timestamp);
-- insert into inquiry_history values('3', '2', 'お届け日の指定は可能でしょうか？', null, true, true, current_timestamp);
-- insert into inquiry_history values('4', '2', 'お問い合わせありがとうございます。ご注文手続きの際に配送希望日時をご指定いただけます。', null, false, true, current_timestamp);
-- insert into inquiry_history values('5', '3', 'ショコラケーキの小麦粉の使用について教えてください。', null, true, false, current_timestamp);
-- insert into inquiry_history values('6', '4', '身長178cm、体重70kgですが、MサイズとLサイズどちらがおすすめですか？', null, true, true, current_timestamp);
-- insert into inquiry_history values('7', '4', 'お問い合わせありがとうございます！少しゆったりめに羽織られたい場合はLサイズ、ジャストサイズでスタイリッシュに着こなしたい場合はMサイズを推奨しております。', null, false, false, current_timestamp);
-- insert into inquiry_history values('8', '5', '日傘のクラシックブラック以外の色が入荷する予定はありますでしょうか？', null, true, true, current_timestamp);
-- insert into inquiry_history values('9', '5', 'お問い合わせありがとうございます。来月上旬に「ミントブルー」および「シェルピンク」の2色を追加販売する予定でございます。今しばらくお待ちください。', null, false, false, current_timestamp);

-- /* =========================================================
--    views_history (閲覧履歴)
--    ========================================================= */
-- insert into views_history values('1', '1', current_timestamp);
-- insert into views_history values('1', '2', current_timestamp);
-- insert into views_history values('2', '5', current_timestamp);
-- insert into views_history values('2', '6', current_timestamp);
-- insert into views_history values('3', '7', current_timestamp);
-- insert into views_history values('4', '8', current_timestamp);
-- insert into views_history values('1', '13', current_timestamp);
-- insert into views_history values('1', '23', current_timestamp);
-- insert into views_history values('2', '24', current_timestamp);
-- insert into views_history values('2', '17', current_timestamp);
-- insert into views_history values('3', '12', current_timestamp);
-- insert into views_history values('3', '9', current_timestamp);
-- insert into views_history values('4', '18', current_timestamp);
-- insert into views_history values('4', '26', current_timestamp);