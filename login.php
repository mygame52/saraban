<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบการศึกษาต่อเนื่อง</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="login-container">
        <div class="login-form">
            <h2>งานการศึกษาต่อเนื่อง</h2>
            <form action="login_connection.php" method="POST">
                <div class="input-group">
                    <label for="username">ชื่อผู้ใช้</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="input-group">
                    <label for="password">รหัสผ่าน</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit">เข้าสู่ระบบ</button>
                <?php
                if (isset($_GET['error'])) {
                    echo '<div class="error">' . htmlspecialchars($_GET['error']) . '</div>';
                }
                ?>
            </form>
        </div>
    </div>
</body>
</html>
