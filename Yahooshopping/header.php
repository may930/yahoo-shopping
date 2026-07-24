<header class="header">
    <div class="header-top">
        <div class="container header-top-inner">
            <span class="header-notice">送料無料をお届け！お得なキャンペーン実施中</span>
            <nav class="header-top-nav">
                <!-- 🔑 ログイン状態に応じた名前の切り替え -->
                <?php if (isset($_SESSION['user'])): ?>
                    <span class="welcome-text">ようこそ、<strong><?= htmlspecialchars($_SESSION['user']['name'] ?? $_SESSION['user']['store_name'] ?? 'ユーザー') ?></strong> さん</span>
                    <a href="logout.php" style="margin-left: 10px; color: #666; text-decoration: underline; font-size: 0.8rem;">ログアウト</a>
                <?php else: ?>
                    <span class="welcome-text">ようこそ、<strong>ゲスト</strong> さん</span>
                <?php endif; ?>
            </nav>
        </div>
    </div>

    <div class="header-main">
        <div class="container header-main-inner">
            <a href="index.php" class="logo">
                <span class="logo-y">HCS!</span><span class="logo-s">ショッピング</span>
            </a>
            <div class="search-bar">
                <input type="text" placeholder="何をお探しですか？ 商品名、カテゴリ、ブランドから探す" aria-label="商品検索">
                <button type="submit" class="search-btn" aria-label="検索">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                        <circle cx="11" cy="11" r="8" />
                        <path d="m21 21-4.35-4.35" />
                    </svg>
                    <span>検索する</span>
                </button>
            </div>
            <div class="header-actions">
                <a href="cart.html" class="action-item-btn">
                    <span class="action-icon">🛒</span>
                    <span class="action-label">カート</span>
                    <span class="cart-count">3</span>
                </a>
                <a href="favorites.html" class="action-item-btn">
                    <span class="action-icon">❤</span>
                    <span class="action-label">お気に入り</span>
                    <span class="cart-count">3</span>
                </a>
                <a href="browsing-history.html" class="action-item-btn">
                    <span class="action-icon">🕒</span>
                    <span class="action-label">閲覧履歴</span>
                </a>
                <a href="order-history.html" class="action-item-btn">
                    <span class="action-icon">⏱️</span>
                    <span class="action-label">注文履歴</span>
                </a>

                <!-- 🔑 ログイン状態に応じたUI切り替え -->
                <?php if (isset($_SESSION['user'])): ?>
                    
                    <!-- ① マイページ（買い物用） -->
                    <a href="mypage.php" class="action-item-btn">
                        <span class="action-icon">👤</span>
                        <span class="action-label">マイページ</span>
                    </a>

                    <!-- ② 出品者(producer)アカウントの時だけ、マイページの【右側】に出現！ -->
                    <?php if (!empty($_SESSION['user']['is_producer'])): ?>
                        <a href="producer_dashboard.php" class="action-item-btn" style="background-color: #f0f7ff; border: 1px solid #0066cc; border-radius: 8px;">
                            <span class="action-icon">🏪</span>
                            <span class="action-label" style="color: #0066cc; font-weight: bold;">出品者ページ</span>
                        </a>
                    <?php endif; ?>

                <?php else: ?>
                    <a href="login_view.php" class="action-item-btn">
                        <span class="action-icon">🔑</span>
                        <span class="action-label">ログイン</span>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>