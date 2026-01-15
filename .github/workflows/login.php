<?php
session_start();

// Database connection details (请确保这些信息是正确的)
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'finalpro';

$error = '';
$success_data = null; // 用于存储成功登录后的信息

// --- 1. Database Connection ---
$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    // 生产环境中不应暴露敏感的连接错误信息
    die("Connection failed: Please check database configuration.");
}

// --- 2. Authentication Logic ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input_name = trim($_POST['name'] ?? '');
    $input_password = $_POST['password'] ?? '';

    if (empty($input_name) || empty($input_password)) {
        $error = "Please enter both name and password.";
    } else {
        // 使用 name 作为用户标识进行查询
        $sql = "SELECT name, password, role FROM users WHERE name = ?";
        $stmt = $conn->prepare($sql);
        
        if ($stmt === false) {
             $error = "System error during login. Please try again.";
        } else {
            $stmt->bind_param("s", $input_name);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 1) {
                $user = $result->fetch_assoc();
                
                // 验证密码哈希值
                if (password_verify($input_password, $user['password'])) {
                    // 安全地重新生成会话ID
                    session_regenerate_id(true);

                    // 存储用户名和角色到会话中，用于欢迎消息和权限检查
                    $_SESSION['name'] = $user['name'];
                    $_SESSION['role'] = $user['role'];
                    
                    // 根据角色确定目标 URL
                    $target_url = '';
                    switch ($user['role']) {
                        case 'customer':
                            $target_url = "customer_home.php";
                            break;
                        case 'admin':
                            $target_url = "admin_page.php";
                            break;
                        case 'staff':
                            $target_url = "staff_page.php";
                            break;
                        default:
                            $error = "Invalid user role. Please contact support.";
                            break;
                    }
                    
                    if (empty($error)) {
                         // 设置成功的会话标志和目标 URL，但不立即重定向
                         $success_data = [
                            'name' => $user['name'],
                            'target' => $target_url
                         ];
                         $_SESSION['login_success'] = $success_data;
                    }

                } else {
                    $error = "Invalid name or password.";
                }
            } else {
                $error = "Invalid name or password.";
            }
            $stmt->close();
        }
    }
}
$conn->close();

// 在渲染 HTML 之前，检查并获取成功数据 (如果存在)
if (isset($_SESSION['login_success'])) {
    $success_data = $_SESSION['login_success'];
    // 清除会话标志，防止刷新时再次显示欢迎页面
    unset($_SESSION['login_success']); 
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Login - Restaurant Management</title>
    <style>
/* -------------------------------------- */
/* 1. 桌面和通用样式 (Desktop and General Styles) */
/* -------------------------------------- */
        body {
            font-family: 'Inter', Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f6f0e7;
            color: #333;
        }

        .header {
            background-color: #fff;
            padding: 20px;
            display: flex; 
            justify-content: space-between;
            align-items: center; 
            flex-wrap: wrap; 
        }

        .header h1 {
            margin: 0;
            font-family: 'Georgia', serif;
            color: #d14524;
            font-size: 24px;
        }

        /* 桌面导航容器：Flex layout to keep links and buttons side-by-side */
        #mobile-menu {
            display: flex; 
            align-items: center;
        }

        nav {
            margin-top: 0;
            display: flex;
            justify-content: center;
        }

        nav a {
            text-decoration: none;
            color: #333;
            font-size: 18px;
            margin: 0 15px;
            transition: color 0.3s;
        }

        nav a:hover {
            color: #d14524;
        }
        
        /* 1. DESKTOP HIDE: 隐藏菜单控制元素 */
        #menu-toggle-checkbox,
        .hamburger-label {
            display: none;
        }
        
        /* 2. BUTTON STYLES (统一按钮样式) */
        .red-btn {
            padding: 8px 15px;
            background-color: #d14524;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            text-decoration: none;
            transition: background-color 0.3s, border 0.3s;
            margin-left: 15px; /* 桌面端按钮间距 */
        }

        .red-btn:hover {
            background-color: white;
            color: #d14524;
            border: 1px solid #d14524;
        }

        /* 登录表单容器样式 */
        .register-container {
            max-width: 500px;
            margin: 50px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .register-container h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #d14524;
            font-size: 28px;
        }
        
        .register-container label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            font-size: 16px;
        }

        .register-container input {
            width: 100%;
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }

        .register-container button {
            width: 100%;
            padding: 15px;
            background-color: #d14524;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 18px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .register-container button:hover {
            background-color: #a1361b;
        }

        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            font-size: 18px;
            text-align: center;
        }

        .register-container p {
            text-align: center;
            font-size: 16px;
        }

        .register-container p a {
            color: #d14524;
            text-decoration: none;
        }

        .register-container p a:hover {
            text-decoration: underline;
        }

        /* -------------------------------------- */
        /* 3. 欢迎屏幕样式 (Welcome Screen Styles) */
        /* -------------------------------------- */
        .welcome-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: #d14524; /* 红色背景 */
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            z-index: 2000; /* 确保在最上层 */
            text-align: center;
            opacity: 1;
            transition: opacity 0.5s ease-out;
        }

        .welcome-overlay.fade-out {
            opacity: 0;
        }

        .welcome-overlay h1 {
            font-family: 'Georgia', serif;
            font-size: 4em;
            margin-bottom: 15px;
        }

        .welcome-overlay p {
            font-size: 1.5em;
        }
        
/* -------------------------------------- */
/* START: 移动设备响应式适配 (Breakpoint: 800px) */
/* -------------------------------------- */
@media (max-width: 800px) {
            /* 1. HEADER LAYOUT FIX */
            .header {
                flex-wrap: nowrap;
                position: relative;
            }

            .header h1 { 
                font-size: 1.5em; 
                margin-right: auto;
            }
            
            /* 2. 强制隐藏桌面内容 (汉堡打开时显示) */
            #mobile-menu {
                display: none !important; 
            }
            
            /* 3. 强制显示汉堡图标 */
            .hamburger-label {
                display: block !important; 
                font-size: 2em;
                color: #d14524;
                cursor: pointer;
                padding: 5px 10px;
                z-index: 1001; 
                line-height: 1;
            }
            
            /* 4. 当菜单被勾选时，强制显示全屏菜单 */
            #menu-toggle-checkbox:checked ~ #mobile-menu {
                display: flex !important; /* 强制显示菜单 */
                flex-direction: column;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(255, 255, 255, 0.95);
                z-index: 1000;
                padding-top: 80px; 
                align-items: center;
                justify-content: flex-start;
            }
            
            /* CSS HACK: Icon content */
            .hamburger-label::after {
                content: '☰';
                display: block;
            }
            
            /* CSS HACK: Change icon to close (✕) when checked */
            #menu-toggle-checkbox:checked ~ .hamburger-label::after {
                content: '✕';
            }

            /* 移动菜单内的导航链接和按钮样式 */
            #mobile-menu nav {
                flex-direction: column;
                align-items: center;
                margin: 20px 0;
            }
            
            #mobile-menu nav a {
                margin: 15px 0; 
                font-size: 1.5em; 
                font-weight: bold;
                color: #333; /* 导航链接颜色在移动菜单内改为黑色 */
            }
            
            /* 移动菜单内的所有按钮样式调整 */
            #mobile-menu .red-btn {
                display: block; 
                margin-top: 10px;
                margin-left: 0; /* 移除桌面端左侧间距 */
                width: 70%;
                text-align: center;
                padding: 15px;
                font-size: 1.2em;
            }
            
            /* 5. FORM CONTAINER FIX */
            .register-container {
                width: 90%;
                margin: 20px auto;
                padding: 20px;
            }

            /* 欢迎屏幕移动端字体调整 */
            .welcome-overlay h1 {
                font-size: 2.5em;
            }

            .welcome-overlay p {
                font-size: 1.2em;
            }
        }
/* -------------------------------------- */
/* END: 移动设备响应式适配 */
/* -------------------------------------- */
    </style>
</head>
<body>

    <div class="header">
        <h1>Restaurant Management System</h1>
        
        <input type="checkbox" id="menu-toggle-checkbox">

        <label for="menu-toggle-checkbox" class="hamburger-label"></label>

        <div id="mobile-menu">
            <nav>
                <a href="Homepage.php">Home</a>
                <a href="Menu.php">Menu</a>
                <a href="AboutUs.html">Contact Us</a>
            </nav>
            <a href="register.php" class="red-btn">Register</a> 
            <a href="login.php" class="red-btn">Login</a>
        </div>
        
    </div>

<?php if ($success_data): ?>
    <!-- 欢迎屏幕 HTML 结构 -->
    <div id="welcomeScreen" class="welcome-overlay">
        <h1>Welcome, <?php echo htmlspecialchars($success_data['name']); ?>!</h1>
        <p>Redirecting in 5 seconds...</p>
    </div>

    <!-- JavaScript 计时器和重定向逻辑 -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const screen = document.getElementById('welcomeScreen');
            // 从 PHP 注入目标 URL
            const targetUrl = '<?php echo $success_data['target']; ?>';
            const delay = 5000; // 5000 milliseconds = 5 seconds

            // 1. 等待 4.5 秒后开始淡出效果 (CSS transition handles the fade)
            setTimeout(() => {
                screen.classList.add('fade-out');
            }, delay - 500); 

            // 2. 等待 5 秒后进行重定向
            setTimeout(() => {
                window.location.href = targetUrl;
            }, delay);
        });
    </script>
<?php else: ?>
    <!-- 正常登录表单 HTML 结构 -->
    <div class="register-container">
        <h2>User Login</h2>
        
        <?php if ($error): ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <form action="login.php" method="POST">
            <label for="name">Name</label>
            <input type="text" id="name" name="name" required>
            
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
            
            <button type="submit">Log In</button>
        </form>
        
        <p>Don't have an account? <a href="register.php">Register here</a></p>
    </div>
<?php endif; ?>

</body>
</html>