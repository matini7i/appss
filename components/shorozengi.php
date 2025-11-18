<?php
// دیتابیس ساده با فایل
$db_file = 'purity_database.json';

// بارگذاری دیتابیس
function loadDatabase($file) {
    if (file_exists($file)) {
        return json_decode(file_get_contents($file), true);
    }
    return [
        'streak' => 0,
        'lastDate' => null,
        'allDates' => [],
        'totalDays' => 0
    ];
}

// ذخیره دیتابیس
function saveDatabase($file, $data) {
    file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

// پردازش درخواست‌ها
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = loadDatabase($db_file);
    
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'register_day':
                $today = date('Y-m-d');
                
                // بررسی آیا امروز ثبت شده
                if ($db['lastDate'] !== $today) {
                    // بررسی ادامه استریک
                    $yesterday = date('Y-m-d', strtotime('-1 day'));
                    if ($db['lastDate'] === $yesterday || $db['streak'] === 0) {
                        $db['streak']++;
                    } else {
                        $db['streak'] = 1;
                    }
                    
                    $db['lastDate'] = $today;
                    $db['allDates'][] = date('c');
                    $db['totalDays'] = count(array_unique(array_map(function($date) {
                        return date('Y-m-d', strtotime($date));
                    }, $db['allDates'])));
                    
                    saveDatabase($db_file, $db);
                    echo json_encode(['success' => true, 'message' => '✅ امروز با موفقیت ثبت شد!', 'streak' => $db['streak'], 'totalDays' => $db['totalDays']]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'امروز قبلاً ثبت شده است!']);
                }
                exit;
                
            case 'export_data':
                $db = loadDatabase($db_file);
                header('Content-Type: application/json');
                header('Content-Disposition: attachment; filename="purity_tracker_export.json"');
                echo json_encode($db, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                exit;
                
            case 'reset_data':
                $db = [
                    'streak' => 0,
                    'lastDate' => null,
                    'allDates' => [],
                    'totalDays' => 0
                ];
                saveDatabase($db_file, $db);
                echo json_encode(['success' => true, 'message' => 'داده‌ها بازنشانی شدند']);
                exit;
        }
    }
}

$db = loadDatabase($db_file);
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>آیت الکرسی - سیستم ثبت پاک‌دامنی</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            padding: 40px;
            max-width: 900px;
            width: 100%;
            text-align: center;
        }

        .header {
            margin-bottom: 30px;
        }

        .header h1 {
            color: #2c3e50;
            font-size: 2.5em;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
        }

        .header p {
            color: #7f8c8d;
            font-size: 1.2em;
        }

        .ayat-box {
            background: linear-gradient(45deg, #4facfe 0%, #00f2fe 100%);
            border-radius: 15px;
            padding: 30px;
            margin: 30px 0;
            color: white;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            line-height: 2.2;
        }

        .ayat-text {
            font-size: 1.4em;
            text-align: center;
            margin-bottom: 20px;
            font-weight: bold;
        }

        .ayat-part {
            display: block;
            margin: 15px 0;
            padding: 10px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
        }

        .ayat-reference {
            font-size: 1.2em;
            opacity: 0.9;
            margin-top: 20px;
        }

        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }

        .stat-box {
            background: #ecf0f1;
            border-radius: 15px;
            padding: 25px;
            text-align: center;
        }

        .stat-number {
            font-size: 2.5em;
            color: #27ae60;
            font-weight: bold;
            margin: 10px 0;
        }

        .stat-label {
            color: #7f8c8d;
            font-size: 1.1em;
        }

        .btn {
            background: linear-gradient(45deg, #27ae60, #2ecc71);
            color: white;
            border: none;
            padding: 15px 30px;
            font-size: 1.2em;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(39, 174, 96, 0.4);
            margin: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(39, 174, 96, 0.6);
        }

        .btn-export {
            background: linear-gradient(45deg, #3498db, #2980b9);
        }

        .btn-reset {
            background: linear-gradient(45deg, #e74c3c, #c0392b);
        }

        .message {
            margin: 20px 0;
            padding: 15px;
            border-radius: 10px;
            font-size: 1.1em;
            display: none;
        }

        .success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .footer {
            margin-top: 30px;
            color: #7f8c8d;
            font-size: 0.9em;
        }

        .daily-message {
            background: #fff3cd;
            color: #856404;
            padding: 15px;
            border-radius: 10px;
            margin: 20px 0;
            border: 1px solid #ffeaa7;
        }

        .progress-bar {
            width: 100%;
            height: 20px;
            background: #ecf0f1;
            border-radius: 10px;
            margin: 20px 0;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(45deg, #27ae60, #2ecc71);
            border-radius: 10px;
            transition: width 0.5s ease;
        }

        @media (max-width: 768px) {
            .container {
                padding: 20px;
            }
            
            .header h1 {
                font-size: 2em;
            }
            
            .ayat-text {
                font-size: 1.1em;
            }
            
            .stat-number {
                font-size: 2em;
            }
            
            .stats-container {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🌙 آیت الکرسی کامل 🌙</h1>
            <p>سیستم ثبت روزهای پاک‌دامنی با برکت آیت الکرسی</p>
        </div>

        <div class="ayat-box">
            <div class="ayat-text">
                <span class="ayat-part">اللَّهُ لاَ إِلَهَ إِلاَّ هُوَ الْحَيُّ الْقَيُّومُ لاَ تَأْخُذُهُ سِنَةٌ وَلاَ نَوْمٌ لَّهُ مَا فِي السَّمَاوَاتِ وَمَا فِي الأَرْضِ</span>
                <span class="ayat-part">مَن ذَا الَّذِي يَشْفَعُ عِنْدَهُ إِلاَّ بِإِذْنِهِ يَعْلَمُ مَا بَيْنَ أَيْدِيهِمْ وَمَا خَلْفَهُمْ وَلاَ يُحِيطُونَ بِشَيْءٍ مِّنْ عِلْمِهِ إِلاَّ بِمَا شَاءَ</span>
                <span class="ayat-part">وَسِعَ كُرْسِيُّهُ السَّمَاوَاتِ وَالأَرْضَ وَلاَ يَئُودُهُ حِفْظُهُمَا وَهُوَ الْعَلِيُّ الْعَظِيمُ</span>
                <span class="ayat-part">لاَ إِكْرَاهَ فِي الدِّينِ قَد تَّبَيَّنَ الرُّشْدُ مِنَ الْغَيِّ فَمَنْ يَكْفُرْ بِالطَّاغُوتِ وَيُؤْمِن بِاللَّهِ فَقَدِ اسْتَمْسَكَ بِالْعُرْوَةِ الْوُثْقَىَ لاَ انفِصَامَ لَهَا وَاللَّهُ سَمِيعٌ عَلِيمٌ</span>
                <span class="ayat-part">اللَّهُ وَلِيُّ الَّذِينَ آمَنُوا يُخْرِجُهُم مِّنَ الظُّلُمَاتِ إِلَى النُّورِ وَالَّذِينَ كَفَرُوا أَوْلِيَاؤُهُمُ الطَّاغُوتُ يُخْرِجُونَهُم مِّنَ النُّورِ إِلَى الظُّلُمَاتِ أُوْلَئِكَ أَصْحَابُ النَّارِ هُمْ فِيهَا خَالِدُونَ</span>
            </div>
            <div class="ayat-reference">سوره البقرة - آیات 255 تا 257</div>
        </div>

        <div class="daily-message">
            <h3>📖 فضیلت آیت الکرسی:</h3>
            <p>پیامبر اکرم (ص) فرمودند: "آیت الکرسی آیة عظیمة من القرآن، لها سلطان و عظمة"</p>
        </div>

        <div class="stats-container">
            <div class="stat-box">
                <div class="stat-number" id="streakCounter"><?php echo $db['streak']; ?></div>
                <div class="stat-label">روز متوالی</div>
            </div>
            <div class="stat-box">
                <div class="stat-number" id="totalCounter"><?php echo $db['totalDays']; ?></div>
                <div class="stat-label">روز کل</div>
            </div>
            <div class="stat-box">
                <div class="stat-number" id="lastDate">
                    <?php echo $db['lastDate'] ? date('Y/m/d', strtotime($db['lastDate'])) : '---'; ?>
                </div>
                <div class="stat-label">آخرین ثبت</div>
            </div>
        </div>

        <div class="progress-bar">
            <div class="progress-fill" id="progressFill" style="width: <?php echo min(($db['streak'] / 30) * 100, 100); ?>%"></div>
        </div>
        <div style="color: #7f8c8d; margin-bottom: 20px;">
            هدف ۳۰ روز: <span id="progressText"><?php echo $db['streak']; ?></span>/۳۰ روز
        </div>

        <button class="btn" onclick="registerToday()">
            ✅ امروز را ثبت کن (جق نزدم)
        </button>

        <div style="margin: 20px 0;">
            <button class="btn btn-export" onclick="exportData()">
                📥 دریافت فایل دیتابیس
            </button>
            <button class="btn btn-reset" onclick="resetData()">
                🔄 بازنشانی داده‌ها
            </button>
        </div>

        <div id="message" class="message"></div>

        <div class="footer">
            <p>«با برکت آیت الکرسی، بر نفس خویش چیره شو و پاداش بزرگ الهی را دریافت کن»</p>
            <p>💾 داده‌ها در سرور و localStorage ذخیره می‌شوند</p>
        </div>
    </div>

    <script>

// سیستم رمز عبور ساده
function checkPassword() {
    let password = prompt("رمز عبور را وارد کنید:");
    
    if (password === "khoda") {
        alert("خوش آمدید!");
        return true;
    } else {
        alert("رمز اشتباه است!");
        window.location.reload(); // صفحه ریلود شود
        return false;
    }
}

// فراخوانی هنگام لود صفحه
window.onload = function() {
    checkPassword();
};



        // دیتابیس محلی مرورگر
        let localDB = {
            streak: <?php echo $db['streak']; ?>,
            lastDate: '<?php echo $db['lastDate']; ?>',
            totalDays: <?php echo $db['totalDays']; ?>
        };

        // همگام‌سازی با localStorage
        function syncLocalStorage() {
            const saved = localStorage.getItem('purityTrackerLocal');
            if (saved) {
                localDB = JSON.parse(saved);
            } else {
                localStorage.setItem('purityTrackerLocal', JSON.stringify(localDB));
            }
            updateDisplay();
        }

        function updateDisplay() {
            document.getElementById('streakCounter').textContent = localDB.streak;
            document.getElementById('totalCounter').textContent = localDB.totalDays;
            document.getElementById('lastDate').textContent = localDB.lastDate ? 
                new Date(localDB.lastDate).toLocaleDateString('fa-IR') : '---';
            
            // بروزرسانی نوار پیشرفت
            const progressPercent = Math.min((localDB.streak / 30) * 100, 100);
            document.getElementById('progressFill').style.width = progressPercent + '%';
            document.getElementById('progressText').textContent = localDB.streak;
        }

        function showMessage(text, type) {
            const messageEl = document.getElementById('message');
            messageEl.textContent = text;
            messageEl.className = `message ${type}`;
            messageEl.style.display = 'block';
            
            setTimeout(() => {
                messageEl.style.display = 'none';
            }, 5000);
        }

        async function registerToday() {
            try {
                const response = await fetch('', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'action=register_day'
                });
                
                const result = await response.json();
                
                if (result.success) {
                    // بروزرسانی دیتابیس محلی
                    localDB.streak = result.streak;
                    localDB.lastDate = new Date().toISOString().split('T')[0];
                    localDB.totalDays = result.totalDays;
                    localStorage.setItem('purityTrackerLocal', JSON.stringify(localDB));
                    
                    updateDisplay();
                    showMessage(result.message, 'success');
                } else {
                    showMessage(result.message, 'error');
                }
            } catch (error) {
                showMessage('خطا در ارتباط با سرور', 'error');
                console.error('Error:', error);
            }
        }

        async function exportData() {
            try {
                const response = await fetch('', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'action=export_data'
                });
                
                if (response.ok) {
                    const blob = await response.blob();
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.style.display = 'none';
                    a.href = url;
                    a.download = 'purity_tracker_export.json';
                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(url);
                    document.body.removeChild(a);
                }
            } catch (error) {
                showMessage('خطا در دریافت فایل', 'error');
                console.error('Error:', error);
            }
        }

        async function resetData() {
            if (confirm('⚠️ آیا مطمئن هستید که می‌خواهید همه داده‌ها را پاک کنید؟ این عمل غیرقابل بازگشت است.')) {
                try {
                    const response = await fetch('', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'action=reset_data'
                    });
                    
                    const result = await response.json();
                    
                    if (result.success) {
                        // بروزرسانی دیتابیس محلی
                        localDB = {
                            streak: 0,
                            lastDate: null,
                            totalDays: 0
                        };
                        localStorage.setItem('purityTrackerLocal', JSON.stringify(localDB));
                        
                        updateDisplay();
                        showMessage(result.message, 'success');
                    }
                } catch (error) {
                    showMessage('خطا در بازنشانی داده‌ها', 'error');
                    console.error('Error:', error);
                }
            }
        }

        // مقداردهی اولیه
        syncLocalStorage();
    </script>
</body>
</html>


