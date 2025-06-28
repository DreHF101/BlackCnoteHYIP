import React, { useState, useEffect } from 'react';
import { 
  User, 
  Calendar, 
  Edit, 
  Save, 
  X, 
  RefreshCw, 
  AlertCircle,
  Shield,
  Eye,
  EyeOff
} from 'lucide-react';
import type { BlackCnoteApiSettings } from '../config/environment';

interface UserProfile {
  id: number;
  username: string;
  name: string;
  email: string;
  first_name: string;
  last_name: string;
  balance: number;
  registration_date: string;
}

// Mock data for development when backend is not available
const mockProfile: UserProfile = {
  id: 1,
  username: 'johndoe',
  name: 'John Doe',
  email: 'john@example.com',
  first_name: 'John',
  last_name: 'Doe',
  balance: 15000.00,
  registration_date: '2024-01-10T10:30:00Z'
};

const Profile = () => {
  const [profile, setProfile] = useState<UserProfile | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [isEditing, setIsEditing] = useState(false);
  const [showBalance, setShowBalance] = useState(true);
  const [formData, setFormData] = useState({
    first_name: '',
    last_name: '',
    display_name: '',
  });
  const [saving, setSaving] = useState(false);

  // Fetch user profile from WordPress REST API
  useEffect(() => {
    const fetchProfile = async () => {
      try {
        setLoading(true);
        
        // Check if we're in development mode first
        const isDevelopment = process.env.NODE_ENV === 'development' || window.location.hostname === 'localhost';
        
        if (isDevelopment) {
          // Use mock data for development
          return;
        }
        
        const apiSettings = (window as { blackCnoteApiSettings?: BlackCnoteApiSettings }).blackCnoteApiSettings;
        if (!apiSettings) {
          throw new Error('API settings not found');
        }

        // Check if we're in development mode using the new flag
        const isDevelopmentMode = apiSettings.isDevelopment === true;
        
        if (isDevelopmentMode) {
          // Use mock data for development
          setProfile(mockProfile);
          setFormData({
            first_name: mockProfile.first_name || '',
            last_name: mockProfile.last_name || '',
            display_name: mockProfile.name || '',
          });
        } else {
          // Try to fetch real data from WordPress backend
          try {
            const response = await fetch(`${apiSettings?.baseUrl ?? ''}profile`, {
              headers: {
                'X-WP-Nonce': (window as { blackcnoteSettings?: { nonce?: string } }).blackcnoteSettings?.nonce || '',
              }
            });

            if (response.ok) {
              const data = await response.json();
              setProfile(data);
              setFormData({
                first_name: data.first_name || '',
                last_name: data.last_name || '',
                display_name: data.name || '',
              });
            } else if (response.status === 401) {
              // User not logged in, redirect to login
              window.location.href = (apiSettings?.homeUrl ?? '') + 'login';
              return;
            } else {
              throw new Error('Failed to fetch profile');
            }
          } catch (apiError) {
            if (process.env.NODE_ENV === 'development') console.warn('API call failed, falling back to mock data:', apiError);
            // Fallback to mock data if API call fails
            setProfile(mockProfile);
            setFormData({
              first_name: mockProfile.first_name || '',
              last_name: mockProfile.last_name || '',
              display_name: mockProfile.name || '',
            });
          }
        }

      } catch (err) {
        setError(err instanceof Error ? err.message : 'Failed to load profile');
      } finally {
        setLoading(false);
      }
    };

    fetchProfile();
  }, []);

  const handleSave = async () => {
    try {
      setSaving(true);
      
      const apiSettings = (window as { blackCnoteApiSettings?: BlackCnoteApiSettings }).blackCnoteApiSettings;
      
      // Check if we're in development mode
      const isDevelopmentMode = apiSettings?.homeUrl === 'http://localhost:3000';
      
      if (isDevelopmentMode) {
        // Simulate successful update in development mode
        const updatedProfile = { ...mockProfile, ...formData };
        setProfile(updatedProfile);
        setIsEditing(false);
        alert('Profile updated successfully! (Development mode)');
      } else {
        // Try to update via API
        try {
          const response = await fetch(`${apiSettings?.baseUrl ?? ''}profile`, {
            method: 'PUT',
            headers: {
              'Content-Type': 'application/json',
              'X-WP-Nonce': (window as { blackcnoteSettings?: { nonce?: string } }).blackcnoteSettings?.nonce || '',
            },
            body: JSON.stringify(formData),
          });

          if (response.ok) {
            const updatedProfile = await response.json();
            setProfile(prev => prev ? { ...prev, ...updatedProfile } : null);
            setIsEditing(false);
            alert('Profile updated successfully!');
          } else {
            throw new Error('Failed to update profile');
          }
        } catch (apiError) {
          if (process.env.NODE_ENV === 'development') console.warn('API update failed:', apiError);
          alert('Profile update failed. Please try again.');
        }
      }

    } catch (err) {
      alert(err instanceof Error ? err.message : 'Failed to update profile');
    } finally {
      setSaving(false);
    }
  };

  const handleCancel = () => {
    if (profile) {
      setFormData({
        first_name: profile.first_name || '',
        last_name: profile.last_name || '',
        display_name: profile.name || '',
      });
    }
    setIsEditing(false);
  };

  const formatDate = (dateString: string) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    });
  };

  const refreshData = () => {
    window.location.reload();
  };

  if (loading) {
    return (
      <div className="min-h-screen bg-gray-50 flex items-center justify-center">
        <div className="text-center">
          <RefreshCw className="h-8 w-8 text-yellow-500 animate-spin mx-auto mb-4" />
          <p className="text-gray-600">Loading profile...</p>
        </div>
      </div>
    );
  }

  if (error) {
    return (
      <div className="min-h-screen bg-gray-50 flex items-center justify-center">
        <div className="text-center">
          <AlertCircle className="h-8 w-8 text-red-500 mx-auto mb-4" />
          <p className="text-red-600 mb-4">{error}</p>
          <button 
            onClick={refreshData}
            className="bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600"
          >
            Try Again
          </button>
        </div>
      </div>
    );
  }

  if (!profile) {
    return (
      <div className="min-h-screen bg-gray-50 flex items-center justify-center">
        <div className="text-center">
          <User className="h-8 w-8 text-gray-400 mx-auto mb-4" />
          <p className="text-gray-600">Profile not found</p>
        </div>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-gray-50 py-8">
      <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        {/* Header */}
        <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8">
          <div>
            <h1 className="text-3xl font-bold text-gray-900">Profile</h1>
            <p className="text-gray-600 mt-1">Manage your account information and settings.</p>
          </div>
          <div className="flex space-x-4 mt-4 sm:mt-0">
            {!isEditing ? (
              <button
                onClick={() => setIsEditing(true)}
                className="bg-yellow-500 text-white px-6 py-2 rounded-lg hover:bg-yellow-600 transition-colors duration-200 flex items-center space-x-2"
              >
                <Edit className="h-4 w-4" />
                <span>Edit Profile</span>
              </button>
            ) : (
              <div className="flex space-x-2">
                <button
                  onClick={handleCancel}
                  className="border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 transition-colors duration-200 flex items-center space-x-2"
                >
                  <X className="h-4 w-4" />
                  <span>Cancel</span>
                </button>
                <button
                  onClick={handleSave}
                  disabled={saving}
                  className="bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600 disabled:opacity-50 transition-colors duration-200 flex items-center space-x-2"
                >
                  <Save className="h-4 w-4" />
                  <span>{saving ? 'Saving...' : 'Save Changes'}</span>
                </button>
              </div>
            )}
          </div>
        </div>

        <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
          {/* Profile Information */}
          <div className="lg:col-span-2 space-y-6">
            {/* Basic Information */}
            <div className="bg-white rounded-2xl shadow-lg p-8">
              <h2 className="text-2xl font-semibold text-gray-900 mb-6">Basic Information</h2>
              
              <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-2">
                    Username
                  </label>
                  <input
                    type="text"
                    value={profile.username}
                    disabled
                    className="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-500"
                  />
                  <p className="text-xs text-gray-500 mt-1">Username cannot be changed</p>
                </div>

                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-2">
                    Email
                  </label>
                  <input
                    type="email"
                    value={profile.email}
                    disabled
                    className="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-500"
                  />
                  <p className="text-xs text-gray-500 mt-1">Email cannot be changed</p>
                </div>

                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-2">
                    First Name
                  </label>
                  <input
                    type="text"
                    value={isEditing ? formData.first_name : (profile.first_name || '')}
                    onChange={(e) => setFormData(prev => ({ ...prev, first_name: e.target.value }))}
                    disabled={!isEditing}
                    className={`w-full px-4 py-2 border border-gray-300 rounded-lg ${
                      isEditing ? 'focus:ring-2 focus:ring-yellow-500 focus:border-transparent' : 'bg-gray-50 text-gray-500'
                    }`}
                  />
                </div>

                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-2">
                    Last Name
                  </label>
                  <input
                    type="text"
                    value={isEditing ? formData.last_name : (profile.last_name || '')}
                    onChange={(e) => setFormData(prev => ({ ...prev, last_name: e.target.value }))}
                    disabled={!isEditing}
                    className={`w-full px-4 py-2 border border-gray-300 rounded-lg ${
                      isEditing ? 'focus:ring-2 focus:ring-yellow-500 focus:border-transparent' : 'bg-gray-50 text-gray-500'
                    }`}
                  />
                </div>

                <div className="md:col-span-2">
                  <label className="block text-sm font-medium text-gray-700 mb-2">
                    Display Name
                  </label>
                  <input
                    type="text"
                    value={isEditing ? formData.display_name : profile.name}
                    onChange={(e) => setFormData(prev => ({ ...prev, display_name: e.target.value }))}
                    disabled={!isEditing}
                    className={`w-full px-4 py-2 border border-gray-300 rounded-lg ${
                      isEditing ? 'focus:ring-2 focus:ring-yellow-500 focus:border-transparent' : 'bg-gray-50 text-gray-500'
                    }`}
                  />
                </div>
              </div>
            </div>

            {/* Account Information */}
            <div className="bg-white rounded-2xl shadow-lg p-8">
              <h2 className="text-2xl font-semibold text-gray-900 mb-6">Account Information</h2>
              
              <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div className="flex items-center space-x-4 p-4 border border-gray-200 rounded-lg">
                  <div className="bg-yellow-100 w-12 h-12 rounded-lg flex items-center justify-center">
                    <Calendar className="h-6 w-6 text-yellow-600" />
                  </div>
                  <div>
                    <div className="text-sm text-gray-600">Member Since</div>
                    <div className="font-semibold text-gray-900">{formatDate(profile.registration_date)}</div>
                  </div>
                </div>

                <div className="flex items-center space-x-4 p-4 border border-gray-200 rounded-lg">
                  <div className="bg-green-100 w-12 h-12 rounded-lg flex items-center justify-center">
                    <Shield className="h-6 w-6 text-green-600" />
                  </div>
                  <div>
                    <div className="text-sm text-gray-600">Account Status</div>
                    <div className="font-semibold text-green-600">Active</div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          {/* Balance Card */}
          <div className="space-y-6">
            <div className="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-2xl shadow-lg p-8 text-white">
              <div className="flex items-center justify-between mb-6">
                <h3 className="text-xl font-semibold">Account Balance</h3>
                <button
                  onClick={() => setShowBalance(!showBalance)}
                  className="text-yellow-100 hover:text-white"
                >
                  {showBalance ? <EyeOff className="h-5 w-5" /> : <Eye className="h-5 w-5" />}
                </button>
              </div>
              
              <div className="text-3xl font-bold mb-4">
                {showBalance ? (
                  `$${profile.balance.toLocaleString('en-US', { minimumFractionDigits: 2 })}`
                ) : (
                  '••••••'
                )}
              </div>
              
              <div className="space-y-3">
                <button className="w-full bg-white text-yellow-600 py-2 px-4 rounded-lg font-semibold hover:bg-gray-100 transition-colors duration-200">
                  Deposit Funds
                </button>
                <button className="w-full border border-white text-white py-2 px-4 rounded-lg font-semibold hover:bg-white hover:text-yellow-600 transition-colors duration-200">
                  Withdraw Funds
                </button>
              </div>
            </div>

            {/* Quick Actions */}
            <div className="bg-white rounded-2xl shadow-lg p-6">
              <h3 className="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
              <div className="space-y-3">
                <button className="w-full text-left p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                  <div className="font-medium text-gray-900">View Transactions</div>
                  <div className="text-sm text-gray-600">Check your transaction history</div>
                </button>
                <button className="w-full text-left p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                  <div className="font-medium text-gray-900">Investment Plans</div>
                  <div className="text-sm text-gray-600">Browse available plans</div>
                </button>
                <button className="w-full text-left p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                  <div className="font-medium text-gray-900">Security Settings</div>
                  <div className="text-sm text-gray-600">Manage account security</div>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default Profile; 
