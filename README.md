<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurant Management System</title>
    <style>
        /* -------------------------------------- */
        /* 1. 全局和桌面样式 (Desktop and General Styles) */
        /* -------------------------------------- */
        body {
            font-family: Arial, sans-serif;
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
			text-align: center;
			}

        /* 隐藏移动菜单控制元素和汉堡标签 (桌面端) */
        #menu-toggle-checkbox,
        .hamburger-label {
            display: none;
        }
        
        /* 桌面导航容器 - 默认为 flex，并包含导航链接和按钮 */
        #mobile-menu-container {
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

        /* 🔴 顶部导航栏使用的红色按钮样式 */
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
        }
        
        /* 桌面/移动菜单中的按钮样式 - 用于桌面端间距和移动端选择 */
        .desktop-and-mobile-btn {
            margin-left: 15px; /* 桌面端按钮间距 */
            display: block;
        }
        
        .red-btn:hover {
            background-color: white;
            color: #d14524;
            border: 1px solid #d14524;
        }
        
        /* ⚪ 内容区使用的白色按钮样式 */
        .white-btn {
            padding: 15px 25px; 
            background-color: white; 
            color: #d14524;      
            border: 2px solid #d14524; 
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s;
            font-weight: bold;
            width: 150px; 
            display: inline-block; 
        }
        
        .white-btn:hover {
            background-color: #d14424; 
            color: white;          
            border: 2px solid #d14424;
        }
        
        /* --- 内容区样式 --- */
        .main-content {
            display: flex;
            justify-content: space-around;
            padding: 40px 20px;
            text-align: center;
            gap: 30px; 
        }

        .main-content .section {
            background-color: #fff;
            padding: 30px;
            width: 30%;
            min-width: 250px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }

        .main-content .section h2 {
            margin-top: 0;
            font-size: 1.8em;
            color: #d14524;
        }

        /* 确保页脚内容居中 */
        footer {
            text-align: center; 
            padding: 20px;
            background-color: #333;
            color: #fff;
            margin-top: 40px;
        }
        
        footer p {
            margin: 0; 
            padding: 0;
        }


        /* -------------------------------------- */
        /* 2. 移动设备响应式适配 (Breakpoint: 800px) */
        /* -------------------------------------- */
        @media (max-width: 800px) {
            
            .header {
                flex-wrap: nowrap;
                position: relative;
            }

            .header h1 { 
                margin-right: auto; 
            }
            
            /* 强制显示汉堡图标 */
            .hamburger-label {
                display: block !important; 
                font-size: 2em;
                color: #d14524;
                cursor: pointer;
                padding: 5px 10px;
                z-index: 1001; 
                line-height: 1;
            }
            
            /* 核心修复：强制隐藏 #mobile-menu-container，直到菜单被点击 */
            #mobile-menu-container {
                display: none !important; 
            }
            
            /* 激活全屏移动菜单 */
            #menu-toggle-checkbox:checked ~ #mobile-menu-container {
                display: flex !important; 
                flex-direction: column;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(255, 255, 255, 0.98);
                z-index: 1000;
                padding-top: 80px; 
                align-items: center;
                justify-content: flex-start;
            }
            
            /* 汉堡图标/关闭图标切换 */
            .hamburger-label::after {
                 content: '☰';
                 display: block;
            }
            #menu-toggle-checkbox:checked ~ .hamburger-label::after {
                 content: '✕';
            }

            /* 移动菜单内的导航链接和按钮样式 */
            #mobile-menu-container nav {
                flex-direction: column;
                align-items: center;
                margin: 20px 0;
            }
            
            #mobile-menu-container nav a {
                margin: 15px 0; 
                font-size: 1.5em; 
                font-weight: bold;
                color: #333; 
            }
            
            /* 移动菜单内显示的按钮样式 */
            .desktop-and-mobile-btn {
                display: block; /* 在移动菜单内显示 */
                margin-top: 20px;
                margin-left: 0; /* 移除桌面端的左边距 */
                width: 70%;
                text-align: center;
                padding: 15px;
                font-size: 1.2em;
                color: #fff;
            }
            
            /* 首页内容区适应移动端 */
            .main-content {
                flex-direction: column;
                padding: 10px;
            }
            
            .main-content .section {
                width: 100%;
                min-width: auto;
                margin-bottom: 20px;
            }
            
            /* 移动端调整 white-btn 的大小和边距 */
            .main-content .section .white-btn {
                 width: 50%;
                 padding: 10px;
            }
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>Restaurant Management System</h1>
        
        <input type="checkbox" id="menu-toggle-checkbox">

        <label for="menu-toggle-checkbox" class="hamburger-label"></label>

        <div id="mobile-menu-container">
            <nav>
                <a href="Homepage.php">Home</a>
                <a href="Menu.php">Menu</a>
                <a href="AboutUs.html">Contact Us</a>
            </nav>
            <a href="register.php" class="red-btn desktop-and-mobile-btn">Register</a> 
            <a href="login.php" class="red-btn desktop-and-mobile-btn">login</a>
        </div>
        
    </div>

    <section class="main-content">
        <div class="section">
            <h2>Explore Our Menu</h2>
            <p>Delight your taste buds with our diverse dishes, from classic favorites to modern specialties.</p>
            <a href="Menu.php" class="white-btn">View Menu</a>
        </div>

        <div class="section">
            <h2>User Login</h2>
            <p>Access your customer or staff portal to manage orders and bookings.</p>
            <a href="login.php" class="white-btn">Log In</a>
        </div>
        
        <div class="section">
            <h2>Contact Us</h2>
            <p>Have questions or feedback? Reach out to us, we're here to help!</p>
            <a href="AboutUs.html" class="white-btn">Contact Details</a>
        </div>
    </section>

    <footer>
        <p>© 2024 Restaurant Management System. All Rights Reserved.</p>
    </footer>

</body>
</html>
