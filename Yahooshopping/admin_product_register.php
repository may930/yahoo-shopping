<?php
require_once 'db.php';

$message = '';
$error = '';

// ==========================================
// 1. 画像非同期アップロード処理 (Ajax/Fetch)
// ==========================================
if (isset($_GET['action']) && $_GET['action'] === 'upload_image') {
    header('Content-Type: application/json');
    
    if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'Image/'; // 保存先フォルダ
        
        // フォルダが存在しない場合は自動作成
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileTmpPath = $_FILES['image_file']['tmp_name'];
        $fileName = $_FILES['image_file']['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        // 許可する拡張子
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (in_array($fileExtension, $allowedExtensions)) {
            // ファイル名の重複防止（ユニークな名前をつける）
            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
            $destPath = $uploadDir . $newFileName;

            if (move_uploaded_file($fileTmpPath, $destPath)) {
                // DB保存用のパス（例: localhost/Yahooshopping/Image/xxx.png）
                $dbImagePath = "localhost/Yahooshopping/" . $destPath;
                echo json_encode(['success' => true, 'image_url' => $dbImagePath, 'preview_url' => $destPath]);
                exit;
            }
        }
    }
    echo json_encode(['success' => false, 'message' => '画像のアップロードに失敗しました。']);
    exit;
}

// ==========================================
// 2. 商品登録処理
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $producer_id  = 1; // ログイン中の出品者ID（仮）
    $category_id  = isset($_POST['category_id']) ? intval($_POST['category_id']) : 0;
    $product_name = trim($_POST['product_name'] ?? '');
    $information  = trim($_POST['information'] ?? '');

    $option_names = $_POST['option_name'] ?? [];
    $prices       = $_POST['price'] ?? [];
    $stocks       = $_POST['stock'] ?? [];
    $image_urls   = $_POST['image_url'] ?? [];

    if (empty($product_name) || $category_id === 0 || empty($option_names)) {
        $error = '必須項目（商品名、カテゴリ、少なくとも1つのバリエーション）を入力してください。';
    } else {
        try {
            $pdo->beginTransaction();

            // 1. product テーブル挿入
            $sql_prod = "INSERT INTO product (producer_id, category_id, product_name, information) 
                         VALUES (:producer_id, :category_id, :product_name, :information)";
            $stmt_prod = $pdo->prepare($sql_prod);
            $stmt_prod->execute([
                ':producer_id'  => $producer_id,
                ':category_id'  => $category_id,
                ':product_name' => $product_name,
                ':information'  => $information,
            ]);

            $new_product_id = $pdo->lastInsertId();

            // 2. product_attributes_options & product_images 挿入
            $sql_opt = "INSERT INTO product_attributes_options (variation_id, option_name, price, stock) 
                        VALUES (:variation_id, :option_name, :price, :stock)";
            $stmt_opt = $pdo->prepare($sql_opt);

            $sql_img = "INSERT INTO product_images (option_id, image_url, display_order) 
                        VALUES (:option_id, :image_url, :display_order)";
            $stmt_img = $pdo->prepare($sql_img);

            for ($i = 0; $i < count($option_names); $i++) {
                $opt_name  = trim($option_names[$i]);
                $opt_price = intval($prices[$i] ?? 0);
                $opt_stock = intval($stocks[$i] ?? 0);
                $img_url   = trim($image_urls[$i] ?? '');

                if (!empty($opt_name)) {
                    $stmt_opt->execute([
                        ':variation_id' => $new_product_id,
                        ':option_name'  => $opt_name,
                        ':price'        => $opt_price,
                        ':stock'        => $opt_stock
                    ]);

                    $new_option_id = $pdo->lastInsertId();

                    if (!empty($img_url)) {
                        $stmt_img->execute([
                            ':option_id'     => $new_option_id,
                            ':image_url'     => $img_url,
                            ':display_order' => 1
                        ]);
                    }
                }
            }

            $pdo->commit();
            $message = "商品「{$product_name}」を正常に登録しました！";

        } catch (PDOException $e) {
            $pdo->rollBack();
            $error = "登録エラー: " . $e->getMessage();
        }
    }
}

// カテゴリ取得
try {
    $stmt_cat = $pdo->query("SELECT category_id, category_name FROM category ORDER BY category_id ASC");
    $categories = $stmt_cat->fetchAll();
} catch (PDOException $e) {
    $categories = [];
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>商品登録 - 出品者管理ツール</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        .admin-body { background-color: #f4f6f8; font-family: 'Noto Sans JP', sans-serif; min-height: 100vh; }
        .admin-header { background-color: #2c3e50; color: #fff; padding: 15px 0; }
        .admin-header-inner { display: flex; justify-content: space-between; align-items: center; }
        .admin-logo { font-size: 1.2rem; font-weight: bold; color: #fff; text-decoration: none; }
        .admin-container { max-width: 950px; margin: 30px auto; padding: 0 20px; }
        
        .form-card { background: #fff; border-radius: 8px; padding: 30px; border: 1px solid #e0e6ed; box-shadow: 0 2px 5px rgba(0,0,0,0.03); margin-bottom: 25px; }
        .form-card h2 { font-size: 1.1rem; margin-top: 0; margin-bottom: 20px; border-bottom: 2px solid #0056b3; padding-bottom: 8px; color: #2c3e50; }
        
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-weight: bold; font-size: 0.9rem; margin-bottom: 8px; color: #333; }
        .form-group label .req { color: #e74c3c; margin-left: 4px; }
        .form-control { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; font-size: 0.95rem; box-sizing: border-box; }
        textarea.form-control { resize: vertical; height: 100px; }

        /* バリエーションカード */
        .variant-card { background: #f8f9fa; border: 1px solid #e0e6ed; border-radius: 8px; padding: 15px; margin-bottom: 15px; }
        .variant-grid { display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 15px; margin-bottom: 12px; }
        
        /* 🛠️ ドラッグ＆ドロップエリア */
        .drop-zone {
            border: 2px dashed #0056b3;
            border-radius: 6px;
            padding: 15px;
            text-align: center;
            background: #fff;
            cursor: pointer;
            transition: all 0.2s ease;
            position: relative;
            min-height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
        }
        .drop-zone.dragover { background: #e6f0fa; border-color: #003366; }
        .drop-zone p { margin: 0; font-size: 0.85rem; color: #666; pointer-events: none; }
        .drop-zone img { max-height: 80px; max-width: 100%; object-fit: contain; margin-top: 8px; display: none; }

        .remove-row-btn { background: #dc3545; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 0.8rem; }
        .add-row-btn { background: #6c757d; color: white; border: none; padding: 8px 16px; border-radius: 4px; cursor: pointer; font-size: 0.85rem; margin-top: 5px; }
        .submit-btn { background: #28a745; color: white; border: none; padding: 14px 40px; font-size: 1.1rem; font-weight: bold; border-radius: 6px; cursor: pointer; width: 100%; transition: background 0.2s; }
        .submit-btn:hover { background: #218838; }

        .alert-success { background: #d4edda; color: #155724; padding: 12px 20px; border-radius: 6px; margin-bottom: 20px; border: 1px solid #c3e6cb; }
        .alert-danger { background: #f8d7da; color: #721c24; padding: 12px 20px; border-radius: 6px; margin-bottom: 20px; border: 1px solid #f5c6cb; }
    </style>
</head>
<body class="admin-body">

    <header class="admin-header">
        <div class="container admin-header-inner">
            <a href="admin_top.php" class="admin-logo">HCS! ストアマネージャー</a>
            <div><a href="admin_top.php" style="color: #fff; text-decoration: none; font-size: 0.9rem;">← TOPに戻る</a></div>
        </div>
    </header>

    <div class="admin-container">
        <h1 style="font-size: 1.5rem; color: #333; margin-bottom: 20px;">📝 新規商品登録</h1>

        <?php if (!empty($message)): ?>
            <div class="alert-success"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="admin_product_register.php">
            
            <!-- 1. 基本情報 -->
            <div class="form-card">
                <h2>1. 基本情報</h2>
                
                <div class="form-group">
                    <label>商品名 <span class="req">*</span></label>
                    <input type="text" name="product_name" class="form-control" placeholder="例：ストロングキュート" required>
                </div>

                <div class="form-group">
                    <label>カテゴリ <span class="req">*</span></label>
                    <select name="category_id" class="form-control" required>
                        <option value="">-- カテゴリを選択してください --</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['category_id'] ?>"><?= htmlspecialchars($cat['category_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>商品説明文</label>
                    <textarea name="information" class="form-control" placeholder="商品の特徴や魅力を入力してください"></textarea>
                </div>
            </div>

            <!-- 2. バリエーション・価格・在庫・画像 -->
            <div class="form-card">
                <h2>2. バリエーション・価格・在庫・画像設定</h2>

                <div id="variantList">
                    <!-- バリエーション1行目 -->
                    <div class="variant-card">
                        <div class="variant-grid">
                            <div>
                                <label style="font-size: 0.8rem; font-weight: bold;">仕様/容量名 <span class="req">*</span></label>
                                <input type="text" name="option_name[]" class="form-control" placeholder="例：500ml" required>
                            </div>
                            <div>
                                <label style="font-size: 0.8rem; font-weight: bold;">価格 (円) <span class="req">*</span></label>
                                <input type="number" name="price[]" class="form-control" placeholder="150" required>
                            </div>
                            <div>
                                <label style="font-size: 0.8rem; font-weight: bold;">在庫数 <span class="req">*</span></label>
                                <input type="number" name="stock[]" class="form-control" placeholder="100" required>
                            </div>
                        </div>

                        <!-- ドラッグ＆ドロップエリア -->
                        <div class="form-group" style="margin-bottom: 0;">
                            <label style="font-size: 0.8rem; font-weight: bold;">商品画像 (ドラッグ＆ドロップまたはクリックで選択)</label>
                            <div class="drop-zone" onclick="triggerFileInput(this)">
                                <p>📁 画像ファイルをここにドロップ または クリックして選択</p>
                                <img src="" class="preview-img" alt="プレビュー">
                                <input type="file" accept="image/*" style="display: none;" onchange="handleFileSelect(this)">
                            </div>
                            <!-- アップロード後にDB登録用のURLがここに入る -->
                            <input type="hidden" name="image_url[]" class="image-url-input">
                        </div>

                        <div style="text-align: right; margin-top: 10px;">
                            <button type="button" class="remove-row-btn" onclick="removeVariantCard(this)">この仕様を削除</button>
                        </div>
                    </div>
                </div>

                <button type="button" class="add-row-btn" onclick="addVariantCard()">➕ バリエーションを追加</button>
            </div>

            <button type="submit" class="submit-btn">商品を登録する</button>

        </form>
    </div>

    <script>
        // ドラッグ＆ドロップのイベント設定
        function setupDropZone(zone) {
            ['dragenter', 'dragover'].forEach(eventName => {
                zone.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    zone.classList.add('dragover');
                }, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                zone.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    zone.classList.remove('dragover');
                }, false);
            });

            zone.addEventListener('drop', (e) => {
                const dt = e.dataTransfer;
                const files = dt.files;
                if (files.length > 0) {
                    uploadFile(files[0], zone);
                }
            });
        }

        function triggerFileInput(zone) {
            const fileInput = zone.querySelector('input[type="file"]');
            fileInput.click();
        }

        function handleFileSelect(input) {
            if (input.files.length > 0) {
                const zone = input.closest('.drop-zone');
                uploadFile(input.files[0], zone);
            }
        }

        // 画像の非同期アップロード処理
        function uploadFile(file, zone) {
            const formData = new FormData();
            formData.append('image_file', file);

            const textP = zone.querySelector('p');
            const previewImg = zone.querySelector('.preview-img');
            const hiddenInput = zone.closest('.variant-card').querySelector('.image-url-input');

            textP.textContent = '⏳ アップロード中...';

            fetch('admin_product_register.php?action=upload_image', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    textP.textContent = '✅ アップロード完了！(クリックで変更)';
                    previewImg.src = data.preview_url;
                    previewImg.style.display = 'block';
                    hiddenInput.value = data.image_url; // DB保存用のパスを設定
                } else {
                    alert(data.message || 'アップロードに失敗しました');
                    textP.textContent = '📁 画像ファイルをここにドロップ または クリックして選択';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('通信エラーが発生しました。');
                textP.textContent = '📁 画像ファイルをここにドロップ または クリックして選択';
            });
        }

        // ページ読み込み時に最初のドロップゾーンを設定
        document.querySelectorAll('.drop-zone').forEach(setupDropZone);

        // カード追加
        function addVariantCard() {
            const list = document.getElementById('variantList');
            const newCard = document.createElement('div');
            newCard.className = 'variant-card';
            newCard.innerHTML = `
                <div class="variant-grid">
                    <div>
                        <label style="font-size: 0.8rem; font-weight: bold;">仕様/容量名 <span class="req">*</span></label>
                        <input type="text" name="option_name[]" class="form-control" placeholder="例：750ml" required>
                    </div>
                    <div>
                        <label style="font-size: 0.8rem; font-weight: bold;">価格 (円) <span class="req">*</span></label>
                        <input type="number" name="price[]" class="form-control" placeholder="200" required>
                    </div>
                    <div>
                        <label style="font-size: 0.8rem; font-weight: bold;">在庫数 <span class="req">*</span></label>
                        <input type="number" name="stock[]" class="form-control" placeholder="50" required>
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label style="font-size: 0.8rem; font-weight: bold;">商品画像 (ドラッグ＆ドロップまたはクリックで選択)</label>
                    <div class="drop-zone" onclick="triggerFileInput(this)">
                        <p>📁 画像ファイルをここにドロップ または クリックして選択</p>
                        <img src="" class="preview-img" alt="プレビュー">
                        <input type="file" accept="image/*" style="display: none;" onchange="handleFileSelect(this)">
                    </div>
                    <input type="hidden" name="image_url[]" class="image-url-input">
                </div>

                <div style="text-align: right; margin-top: 10px;">
                    <button type="button" class="remove-row-btn" onclick="removeVariantCard(this)">この仕様を削除</button>
                </div>
            `;
            list.appendChild(newCard);
            setupDropZone(newCard.querySelector('.drop-zone'));
        }

        function removeVariantCard(btn) {
            const cards = document.querySelectorAll('.variant-card');
            if (cards.length > 1) {
                btn.closest('.variant-card').remove();
            } else {
                alert('少なくとも1つのバリエーションが必要です。');
            }
        }
    </script>
</body>
</html>