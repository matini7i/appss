// app/page.tsx
"use client";

import { useState, useEffect } from 'react';
import ICYRANDashboard from "@/components/icyran-dashboard";

export default function Page() {
  const [isAuthenticated, setIsAuthenticated] = useState(false);
  const [loading, setLoading] = useState(true);
  const [username, setUsername] = useState('');
  const [password, setPassword] = useState('');
  const [error, setError] = useState('');

  // بررسی وضعیت احراز هویت در هنگام لود اولیه
  useEffect(() => {
    const checkAuth = () => {
      const authStatus = localStorage.getItem('isAuthenticated');
      const savedUsername = localStorage.getItem('username');
      
      if (authStatus === 'true' && savedUsername === 'matin') {
        setIsAuthenticated(true);
      }
      setLoading(false);
    };

    // تأخیر کوچک برای شبیه‌سازی بررسی
    setTimeout(checkAuth, 500);
  }, []);

  const handleLogin = (e: React.FormEvent) => {
    e.preventDefault();
    
    // اعتبارسنجی ساده
    if (username === 'matin' && password === '123') {
      // ذخیره در localStorage
      localStorage.setItem('isAuthenticated', 'true');
      localStorage.setItem('username', username);
      setIsAuthenticated(true);
      setError('');
    } else {
      setError('نام کاربری یا رمز عبور اشتباه است');
    }
  };

  // حالت لودینگ
  if (loading) {
    return (
      <div className="min-h-screen flex items-center justify-center bg-gray-100">
        <div className="text-center">
          <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
          <p className="mt-4 text-gray-600">در حال بررسی...</p>
        </div>
      </div>
    );
  }

  // اگر کاربر لاگین نکرده باشد، صفحه ورود را نمایش بده
  if (!isAuthenticated) {
    return (
      <div className="min-h-screen flex flex-col items-center justify-center bg-gradient-to-br from-gray-50 to-gray-100 p-4">
        <div className="w-full max-w-md">
          {/* هدر */}
          <div className="text-center mb-8">
            <h1 className="text-3xl font-bold text-gray-800">ICYRAN Dashboard</h1>
            <p className="text-gray-600 mt-2">لطفاً برای دسترسی وارد شوید</p>
          </div>

          {/* فرم لاگین */}
          <div className="bg-white rounded-xl shadow-lg p-6 md:p-8">
            <form onSubmit={handleLogin} className="space-y-6">
              <div>
                <label htmlFor="username" className="block text-sm font-medium text-gray-700 mb-2">
                  نام کاربری
                </label>
                <input
                  id="username"
                  type="text"
                  required
                  value={username}
                  onChange={(e) => setUsername(e.target.value)}
                  className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                  placeholder="نام کاربری خود را وارد کنید"
                />
              </div>

              <div>
                <label htmlFor="password" className="block text-sm font-medium text-gray-700 mb-2">
                  رمز عبور
                </label>
                <input
                  id="password"
                  type="password"
                  required
                  value={password}
                  onChange={(e) => setPassword(e.target.value)}
                  className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                  placeholder="رمز عبور خود را وارد کنید"
                />
              </div>

              {error && (
                <div className="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-lg text-sm">
                  {error}
                </div>
              )}

              <button
                type="submit"
                className="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-lg transition duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
              >
                ورود به سیستم
              </button>
            </form>

            {/* اطلاعات نمونه */}
            <div className="mt-8 pt-6 border-t border-gray-200">
              <div className="text-sm text-gray-600">
                <p className="font-medium mb-2">برای تست وارد شوید:</p>
                <div className="bg-gray-50 rounded-lg p-4">
                  <div className="flex justify-between items-center mb-1">
                    <span className="text-gray-500">نام کاربری:</span>
                    <span className="font-mono font-bold text-blue-600">matin</span>
                  </div>
                  <div className="flex justify-between items-center">
                    <span className="text-gray-500">رمز عبور:</span>
                    <span className="font-mono font-bold text-green-600">123</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          {/* فوتر */}
          <div className="text-center text-gray-500 text-sm mt-8">
            <p>سیستم احراز هویت ICYRAN</p>
          </div>
        </div>
      </div>
    );
  }

  // اگر کاربر لاگین کرده باشد، داشبورد اصلی را نمایش بده
  return <ICYRANDashboard />;
}