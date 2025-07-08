import React, { useState, useEffect } from 'react';
import { Link, useLocation } from 'react-router-dom';
import { 
  Menu, 
  X, 
  TrendingUp, 
  Calculator, 
  Info, 
  Phone, 
  BarChart3, 
  User, 
  DollarSign, 
  LogOut,
  Settings
} from 'lucide-react';
import type { WordPressSettings } from '../types';
import { SyncStatusIndicator } from './SyncStatusIndicator';

interface UserData {
  id: number;
  name: string;
  email: string;
  balance: number;
}

// Mock user data for development when backend is not available
const mockUserData: UserData = {
  id: 1,
  name: 'John Doe',
  email: 'john@example.com',
  balance: 15000.00
};

const Header = () => {
  const [isMenuOpen, setIsMenuOpen] = useState(false);
  const [isUserMenuOpen, setIsUserMenuOpen] = useState(false);
  const [userData, setUserData] = useState<UserData | null>(null);
  const [loading, setLoading] = useState(true);
  const location = useLocation();

  // Check user authentication status
  useEffect(() => {
    const checkAuth = async () => {
      try {
        const apiSettings = (window as { blackCnoteApiSettings?: WordPressSettings }).blackCnoteApiSettings;
        if (!apiSettings) {
          setLoading(false);
          return;
        }

        // Check if we're in development mode using the new flag
        const isDevelopmentMode = apiSettings.isDevelopment === true;
        
        if (isDevelopmentMode) {
          // Use mock data for development
          setUserData(mockUserData);
        } else {
          // Try to fetch real data from WordPress backend
          try {
            const response = await fetch(`${apiSettings.baseUrl}user-data`, {
              headers: {
                'X-WP-Nonce': apiSettings.nonce || '',
              }
            });

            if (response.ok) {
              const data = await response.json();
              setUserData(data);
            }
          } catch (apiError) {
            // Fallback to mock data if API call fails
            if (process.env.NODE_ENV === 'development') console.warn('API call failed, falling back to mock data:', apiError);
            setUserData(mockUserData);
          }
        }
      } catch (error) {
        if (process.env.NODE_ENV === 'development') console.error('Auth check failed:', error);
      } finally {
        setLoading(false);
      }
    };

    checkAuth();
  }, []);

  const handleLogout = () => {
    window.location.href = '/wp-login.php?action=logout';
  };

  const handleLogin = () => {
    window.location.href = '/wp-login.php';
  };

  const handleGetStarted = () => {
    if (userData) {
      window.location.href = '/investment-plans';
    } else {
      window.location.href = '/wp-login.php';
    }
  };

  const publicNavigation = [
    { name: 'Home', href: '/', icon: TrendingUp },
    { name: 'Investment Plans', href: '/investment-plans', icon: BarChart3 },
    { name: 'Profit Calculator', href: '/calculator', icon: Calculator },
    { name: 'About', href: '/about', icon: Info },
    { name: 'Contact', href: '/contact', icon: Phone },
  ];

  const userNavigation = [
    { name: 'Dashboard', href: '/dashboard', icon: BarChart3 },
    { name: 'Investment Plans', href: '/investment-plans', icon: TrendingUp },
    { name: 'Transactions', href: '/transactions', icon: DollarSign },
    { name: 'Profile', href: '/profile', icon: User },
    { name: 'Calculator', href: '/calculator', icon: Calculator },
  ];

  const isActive = (href: string) => {
    return location.pathname === href;
  };

  return (
    <header className="bg-blue-600 shadow-lg sticky top-0 z-50">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="flex justify-between items-center h-20">
          {/* Logo */}
          <Link to="/" className="flex items-center space-x-3">
            <img 
              src="/cropped-BLACKCNOTE-1-2.png" 
              alt="BlackCnote" 
              className="h-12 w-auto"
            />
            <div className="hidden sm:block">
              <h1 className="text-2xl font-bold text-white">BlackCnote</h1>
              <p className="text-sm text-blue-100">Empowering Black Wealth</p>
            </div>
          </Link>

          {/* Desktop Navigation */}
          <nav className="hidden lg:flex space-x-8">
            {(userData ? userNavigation : publicNavigation).map((item) => (
              <Link
                key={item.name}
                to={item.href}
                className={`flex items-center space-x-2 transition-colors duration-200 font-medium ${
                  isActive(item.href) 
                    ? 'text-yellow-400' 
                    : 'text-white hover:text-yellow-400'
                }`}
              >
                <item.icon className="h-4 w-4" />
                <span>{item.name}</span>
              </Link>
            ))}
          </nav>

          {/* Auth Buttons / User Menu */}
          <div className="hidden lg:flex items-center space-x-4">
            <SyncStatusIndicator />
            {!loading && (
              userData ? (
                <div className="relative">
                  <button
                    onClick={() => setIsUserMenuOpen(!isUserMenuOpen)}
                    className="flex items-center space-x-3 bg-gray-50 hover:bg-gray-100 px-4 py-2 rounded-lg transition-colors duration-200"
                  >
                    <div className="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                      <User className="h-4 w-4 text-yellow-600" />
                    </div>
                    <div className="text-left">
                      <div className="text-sm font-medium text-gray-900">{userData.name}</div>
                      <div className="text-xs text-gray-500">${userData.balance.toLocaleString()}</div>
                    </div>
                  </button>

                  {isUserMenuOpen && (
                    <div className="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-gray-200 py-2 z-50">
                      <div className="px-4 py-2 border-b border-gray-100">
                        <div className="text-sm font-medium text-gray-900">{userData.name}</div>
                        <div className="text-xs text-gray-500">{userData.email}</div>
                      </div>
                      
                      <Link
                        to="/profile"
                        className="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 flex items-center space-x-2"
                        onClick={() => setIsUserMenuOpen(false)}
                      >
                        <Settings className="h-4 w-4" />
                        <span>Settings</span>
                      </Link>
                      
                      <button
                        onClick={handleLogout}
                        className="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-50 flex items-center space-x-2"
                      >
                        <LogOut className="h-4 w-4" />
                        <span>Logout</span>
                      </button>
                    </div>
                  )}
                </div>
              ) : (
                <>
                  <button
                    onClick={handleLogin}
                    className="text-gray-700 hover:text-yellow-600 font-medium transition-colors"
                  >
                    Login
                  </button>
                  <button
                    onClick={handleGetStarted}
                    className="bg-gradient-to-r from-yellow-500 to-yellow-600 text-white px-6 py-2 rounded-lg font-medium hover:from-yellow-600 hover:to-yellow-700 transition-all duration-200 shadow-md hover:shadow-lg"
                  >
                    Get Started
                  </button>
                </>
              )
            )}
          </div>

          {/* Mobile menu button */}
          <button
            onClick={() => setIsMenuOpen(!isMenuOpen)}
            className="lg:hidden p-2 rounded-md text-gray-700 hover:text-yellow-600 hover:bg-gray-100"
          >
            {isMenuOpen ? <X className="h-6 w-6" /> : <Menu className="h-6 w-6" />}
          </button>
        </div>

        {/* Mobile Navigation */}
        {isMenuOpen && (
          <div className="lg:hidden border-t border-gray-200 py-4">
            <div className="space-y-4">
              {(userData ? userNavigation : publicNavigation).map((item) => (
                <Link
                  key={item.name}
                  to={item.href}
                  className={`flex items-center space-x-3 transition-colors duration-200 font-medium py-2 ${
                    isActive(item.href) 
                      ? 'text-yellow-600' 
                      : 'text-gray-700 hover:text-yellow-600'
                  }`}
                  onClick={() => setIsMenuOpen(false)}
                >
                  <item.icon className="h-5 w-5" />
                  <span>{item.name}</span>
                </Link>
              ))}
              
              <div className="pt-4 border-t border-gray-200 space-y-3">
                {userData ? (
                  <>
                    <div className="px-2 py-2 bg-gray-50 rounded-lg">
                      <div className="text-sm font-medium text-gray-900">{userData.name}</div>
                      <div className="text-xs text-gray-500">{userData.email}</div>
                      <div className="text-sm text-yellow-600 font-medium">
                        Balance: ${userData.balance.toLocaleString()}
                      </div>
                    </div>
                    <Link
                      to="/profile"
                      className="block text-gray-700 hover:text-yellow-600 font-medium"
                      onClick={() => setIsMenuOpen(false)}
                    >
                      Settings
                    </Link>
                    <button
                      onClick={handleLogout}
                      className="block w-full text-left text-red-600 font-medium"
                    >
                      Logout
                    </button>
                  </>
                ) : (
                  <>
                    <button
                      onClick={handleLogin}
                      className="block w-full text-left text-gray-700 hover:text-yellow-600 font-medium"
                    >
                      Login
                    </button>
                    <button
                      onClick={handleGetStarted}
                      className="block w-full bg-gradient-to-r from-yellow-500 to-yellow-600 text-white px-4 py-2 rounded-lg font-medium hover:from-yellow-600 hover:to-yellow-700 transition-all duration-200"
                    >
                      Get Started
                    </button>
                  </>
                )}
              </div>
            </div>
          </div>
        )}
      </div>
    </header>
  );
};

export default Header;