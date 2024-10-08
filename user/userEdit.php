<?php

// 整合頁面
include "../vars.php";
$cateNum = 1;
$pageTitle = "{$cate_ary[$cateNum]}";
include "../template_top.php";
include "../template_nav.php";

if (!isset($_GET["id"])) {
    echo "請正確帶入 get id 變數";
    header("location:users.php");
}

$id = $_GET["id"];

require_once("../db_connect.php");

$sql = "SELECT users.*, address_city.address_city_name, address_cityarea.address_cityarea_name, user_level.level_name
    FROM users
    LEFT JOIN address_city ON users.address_city_id = address_city.address_city_id
    LEFT JOIN address_cityarea ON users.address_cityarea_id = address_cityarea.address_cityarea_id
    LEFT JOIN user_level ON users.level_id = user_level.level_id
    WHERE users.id = $id";

$result = $conn->query($sql);

if ($result->num_rows == 0) {
    exit("找不到此用戶");
}

$user = $result->fetch_assoc();
$userCount = $result->num_rows;

$title =  $user["user_name"];

// 抓取城市與區域資料
$sqlCity = "SELECT * FROM address_city";
$sqlCityArea = "SELECT * FROM address_cityarea";

$resultCity = $conn->query($sqlCity);
$resultCityArea = $conn->query($sqlCityArea);

$CityList = $resultCity->fetch_all(MYSQLI_ASSOC);
$CityAreaList = $resultCityArea->fetch_all(MYSQLI_ASSOC);

?>
<!doctype html>
<html lang="en">

<head>
    <title>使用者管理/編輯</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <?php include("../css.php") ?>
    <?php include("../js.php") ?>

</head>

<body>
    <main class="main-content pb-3">
        <div class="container">

            <div class="d-flex">
                <a href="users.php" class="btn btn-dark">返回列表</a>
                <h1>使用者管理/編輯</h1>
            </div>

            <?php
            $valid_class = '';
            $valid_content = '';
            switch ($user["valid"]) {
                case '0':
                    $valid_class = 'bg-danger';
                    $valid_content = '已停用';
                    break;
                case '1':
                    $valid_class = 'bg-success';
                    $valid_content = '啟用中';
                    break;
            }
            ?>
            <p class="h3"><?= $title ?><span class="ms-1 badge <?= $valid_class ?>"> <?= $valid_content ?></span></p>


            <?php if ($userCount > 0) : ?>
                <form action="doEditUser.php" method="post" class="searchbar row g-3">

                    <!-- 使用者ID -->
                    <input type="hidden" name="id" value="<?= $user['id'] ?>">

                    <!-- 使用者名稱 -->
                    <div class="col-3 mb-3">
                        <label for="user_name" class="form-label">使用者名稱<span class="text-danger">(必填)</span></label>
                        <input type="text" class="form-control" name="user_name" id="user_name" value="<?= $user['user_name'] ?>" required>
                    </div>

                    <!-- 性別 -->
                    <div class="col-3 mb-3">
                        <label for="gender" class="form-label">性別</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="gender" id="inlineRadio1" value="1" <?= $user['gender'] == 1 ? 'checked' : '' ?>>
                                <label class="form-check-label" for="inlineRadio1">男</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="gender" id="inlineRadio2" value="2" <?= $user['gender'] == 2 ? 'checked' : '' ?>>
                                <label class="form-check-label" for="inlineRadio2">女</label>
                            </div>
                        </div>
                    </div>

                    <!-- 使用者帳號 -->
                    <div class="col-3 mb-3">
                        <label for="account" class="form-label">使用者帳號<span class="text-danger">*帳號註冊後無法修改！</span></label>
                        <p class="form-label"><?= $user['account'] ?></p>
                    </div>

                    <!-- 使用者密碼 -->
                    <div class="col-3 mb-3">
                        <label for="password" class="form-label">使用者密碼<span class="text-danger"> 不須變更時保持空白即可!</span></label>
                        <input type="password" class="form-control" name="password" placeholder="請輸入使用者密碼">
                    </div>
                    <div class="col-3 mb-3">
                        <label for="confirm_password" class="form-label">再次輸入使用者密碼<span class="text-danger"> 不須變更時保持空白即可!</span></label>
                        <input type="password" class="form-control" name="confirm_password" placeholder="請再次輸入使用者密碼">
                    </div>

                    <!-- 使用者電話 -->
                    <div class="col-3 mb-3">
                        <label for="phone" class="form-label">使用者電話</label>
                        <input type="tel" class="form-control" name="phone" value="<?= "0" . $user['phone'] ?>" required>
                    </div>

                    <!-- 使用者信箱 -->
                    <div class="col-3 mb-3">
                        <label for="email" class="form-label">使用者信箱</label>
                        <input type="email" class="form-control" name="email" value="<?= $user['email'] ?>">
                    </div>

                    <!-- 生日 -->
                    <div class="col-3 mb-3">
                        <label for="birthday" class="form-label">生日</label>
                        <input type="date" class="form-control" name="birthday" value="<?= $user['birthday'] ?>">
                    </div>

                    <!-- 地址 -->
                    <div class="col-6 mb-3">
                        <label for="address" class="form-label">地址</label>
                        <div class="d-flex">
                            <select class="form-select me-1" style="flex-basis: 20%; max-width: 20%;" name="address_city_id">
                                <option selected>行政區</option>
                                <?php foreach ($CityList as $city) : ?>
                                    <option value="<?= $city['address_city_id'] ?>" <?= isset($user['address_city_id']) && $city['address_city_id'] == $user['address_city_id'] ? 'selected' : '' ?>>
                                        <?= $city['address_city_name'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <select class="form-select me-1" style="flex-basis: 20%; max-width: 20%;" name="address_cityarea_id">
                                <option selected>鄉鎮市區</option>
                                <?php foreach ($CityAreaList as $cityArea) : ?>
                                    <option value="<?= $cityArea['address_cityarea_id'] ?>" <?= isset($user['address_cityarea_id']) && $cityArea['address_cityarea_id'] == $user['address_cityarea_id'] ? 'selected' : '' ?>>
                                        <?= $cityArea['address_cityarea_name'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <input type="text" class="form-control" style="flex-basis: 60%; max-width: 60%;" name="address_street" placeholder="請輸入詳細地址" value="<?= isset($user['address_street']) ? $user['address_street'] : '' ?>">
                        </div>
                    </div>

                    <!-- 創建時間 -->
                    <div class="col-3 mb-3">
                        <p class="form-label">創建時間</p>
                        <p class="form-label"><?= $user['create_date'] ?></p>
                    </div>

                    <!-- 上次更新時間 -->
                    <div class="col-3 mb-3">
                        <p class="form-label">上次更新時間</p>
                        <p class="form-label"><?= $user['update_time'] ?></p>
                    </div>

                    <!-- 帳號狀態 -->
                    <div class="col-3 mb-3">
                        <div class="d-flex">
                            <label for="valid" class="form-label">帳號狀態</label>

                        </div>

                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="valid" id="inlineRadio1" value="1" <?= $user['valid'] == 1 ? 'checked' : '' ?>>
                                <label class="form-check-label" for="inlineRadio1">啟用</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="valid" id="inlineRadio2" value="0" <?= $user['valid'] == 0 ? 'checked' : '' ?>>
                                <label class="form-check-label" for="inlineRadio2">停用</label>
                            </div>
                        </div>
                    </div>

                    <!-- 按鈕 -->
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">確認更新</button>
                        <a href="users.php" class="btn btn-dark">取消返回</a>
                    </div>
                </form>
            <?php else : ?>
                使用者不存在
            <?php endif; ?>
        </div>

        <script>
            document.getElementsByName('address_city_id')[0].addEventListener('change', function() {
                var cityId = this.value;

                // 使用 AJAX 請求對應城市區域資料
                var xhr = new XMLHttpRequest();
                xhr.open('POST', 'get_cityareas.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        document.getElementsByName('address_cityarea_id')[0].innerHTML = xhr.responseText;
                    }
                };
                xhr.send('city_id=' + cityId);
            });
        </script>

        <?php $conn->close(); ?>
    </main>
</body>

</html>