<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta property="og:title" content="おえかきごはん - 子どものお絵かきがレシピに！">
    <meta property="og:description" content="子どものお絵かきを元に親子で作れるレシピを提案する楽しい食育アプリ！">
    <meta property="og:image" content="https://oekakigohan.com/images/ogp-image.png">
    <meta property="og:url" content="https://oekakigohan.com">
    <meta property="og:type" content="website">
    <meta name="twitter:card" content="summary_large_image"> <!-- Twitter用（LINEも対応） -->
    <title>おえかきごはん</title>

    <!-- フォントとBootstrapの読み込み -->
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Yomogi&display=swap" rel="stylesheet">
    <!--ファビコン-->
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link rel="shortcut icon" href="/favicon.ico">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">


</head>
<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-light bg-light shadow">
            <div class="container-fluid">
                <!-- ロゴ -->
                <a class="navbar-brand d-flex align-items-center" href="/">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="navbar-logo me-2">
                </a>

                <!-- ハンバーガーメニュー -->
                <div class="hamburger-menu">
                    <input type="checkbox" id="menu-btn-check">
                    <label for="menu-btn-check" class="menu-btn"><span></span></label>
                    <!--ここからメニュー-->
                    <div class="menu-content">
                        <ul>
                            <li>
                                <a href="/contact">お問い合わせ</a>
                            </li>
                        </ul>
                    </div>
                    <!--ここまでメニュー-->
                </div>


                <!-- ナビゲーションメニュー -->
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto text-center">
                        <li class="nav-item">
                            <a class="nav-link custom-nav-link" href="/contact">お問い合わせ</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <!-- メインコンテンツ -->
    <main class="custom-space custom-bg-color custom-bg-size">
        <div id="app"></div>
    </main>

    <!-- フッター -->
    <footer class="bg-light text-center py-3">
        <p>© 2024 おえかきごはん</p>
    </footer>

    <!-- Bootstrap JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

    <!-- ViteによるVue.jsファイル読み込み -->
    @vite(['resources/js/app.js'])
</body>
</html>

<style>
/* ベースフォント設定 */
body {
    font-family: 'Noto Sans JP', sans-serif;
}

header {
    position: fixed;
    width: 100%;
}

main {
    padding-top: 76px;
}

/* ロゴのサイズ調整 */
.navbar-logo {
    height: 50px;
}

/* ナビゲーションリンクのデザイン */
.nav-link.custom-nav-link {
    color: #535353 !important;
    font-weight: bold;
    font-size: 1rem;
    padding: 0.5rem 1rem;
    transition: color 0.3s ease;
}

.nav-link.custom-nav-link:hover {
    color: #007BFF !important;
}

/* デフォルトで非表示 */
.hamburger-menu {
    display: none;
}

.menu-btn {
    position: fixed;
    top: 10px;
    right: 10px;
    display: flex;
    height: 60px;
    width: 60px;
    justify-content: center;
    align-items: center;
    z-index: 90;
    /* background-color: #3584bb; */
}
.menu-btn span,
.menu-btn span:before,
.menu-btn span:after {
    content: '';
    display: block;
    height: 3px;
    width: 25px;
    border-radius: 3px;
    background-color: #535353;
    position: absolute;
}
.menu-btn span:before {
    bottom: 8px;
}
.menu-btn span:after {
    top: 8px;
}

#menu-btn-check:checked ~ .menu-btn span {
    background-color: rgba(255, 255, 255, 0);/*メニューオープン時は真ん中の線を透明にする*/
}
#menu-btn-check:checked ~ .menu-btn span::before {
    bottom: 0;
    transform: rotate(45deg);
}
#menu-btn-check:checked ~ .menu-btn span::after {
    top: 0;
    transform: rotate(-45deg);
}

#menu-btn-check {
    display: none;
}

.menu-content {
    width: 100%;
    height: 100%;
    position: fixed;
    top: 0;
    left: 0;
    z-index: 80;
    background-color: #ffffff;
}
.menu-content ul {
    padding: 70px 10px 0;
}
.menu-content ul li {
    border-bottom: solid 1px #111111;
    list-style: none;
}
.menu-content ul li a {
    display: block;
    width: 100%;
    font-size: 15px;
    box-sizing: border-box;
    color:#111111;
    text-decoration: none;
    padding: 9px 15px 10px 0;
    position: relative;
}
.menu-content ul li a::before {
    content: "";
    width: 7px;
    height: 7px;
    border-top: solid 2px #111111;
    border-right: solid 2px #111111;
    transform: rotate(45deg);
    position: absolute;
    right: 11px;
    top: 16px;
}

.menu-content {
    width: 100%;
    height: 100%;
    position: fixed;
    top: 0;
    left: 100%;/*leftの値を変更してメニューを画面外へ*/
    z-index: 80;
    background-color: #ffffff;
    transition: all 0.5s;/*アニメーション設定*/
    /* opacity: 0.75; */
}

#menu-btn-check:checked ~ .menu-content {
    left: 0;/*メニューを画面内へ*/
}

/* ナビゲーションメニューのセンタリング */
.navbar-nav {
    gap: 10px;
}

/* ナビゲーションメニューのレスポンシブ対応 */
@media (max-width: 991px) {
    .navbar-nav {
        flex-direction: column;
        align-items: center;
    }

    .nav-link.custom-nav-link {
        font-size: 1.2rem;
    }

    .hamburger-menu {
        display: block;
    }
}

/* フッターのスタイル */
footer p {
    margin: 0;
    font-size: 0.9rem;
    color: #555;
}
</style>
