import React, { useState, useEffect } from 'react';
import { 
  TrendingUp, 
  Shield, 
  Clock, 
  ArrowRight, 
  CheckCircle,
  Star,
  Users,
  Zap,
  Globe,
  RefreshCw,
  AlertCircle
} from 'lucide-react';
import type { BlackCnoteApiSettings } from '../config/environment';
import WelcomeBanner from '../components/WelcomeBanner';
import { fetchHomepage, fetchSettings } from '../api/wordpress';
import { useLiveEditing } from '../hooks/useLiveEditing';

interface Stats {
  totalUsers: number;
  totalInvested: number;
  totalPaid: number;
  activeInvestments: number;
}

interface Feature {
  icon: React.ComponentType<React.SVGProps<SVGSVGElement>>;
  title: string;
  description: string;
}

// Mock data for development when backend is not available
const mockStats: Stats = {
  totalUsers: 15420,
  totalInvested: 28475000,
  totalPaid: 31568000,
  activeInvestments: 8920
};

const HomePage = () => {
  const [stats, setStats] = useState<Stats>({
    totalUsers: 0,
    totalInvested: 0,
    totalPaid: 0,
    activeInvestments: 0
  });
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [isLoggedIn, setIsLoggedIn] = useState(false);
  const [wpHomepage, setWpHomepage] = useState<{ title: string; content: string } | null>(null);
  const [liveEditingEnabled, setLiveEditingEnabled] = useState(true); // TODO: Fetch from API/settings
  const [liveEditingState, liveEditingActions] = useLiveEditing({
    onChange: (change) => {
      // Handle content/component changes for HomePage
      if (change.type === 'content' && change.id === 'homepage-content') {
        setWpHomepage(change.content);
      }
      if (change.type === 'component' && change.name === 'homepage-stats') {
        setStats(change.data);
      }
    }
  });

  // Fetch stats and check login status
  useEffect(() => {
    let interval: NodeJS.Timeout | null = null;
    let timeout: NodeJS.Timeout | null = null;
    const fetchData = async () => {
      try {
        setLoading(true);
        
        // Always try to fetch live data from WordPress API
        try {
          // Fetch homepage content from WordPress
          const homepage = await fetchHomepage();
          setWpHomepage({ title: homepage.title, content: homepage.content });
          console.log('✅ Homepage data fetched from WordPress:', homepage);

          // Try to fetch stats from WordPress API
          const statsResponse = await fetch((window.blackCnoteApiSettings?.apiUrl || window.location.origin + '/wp-json/blackcnote/v1').replace(/\/$/, '') + '/stats');
          if (statsResponse.ok) {
            const statsData = await statsResponse.json();
            setStats(statsData);
            console.log('✅ Stats data fetched from WordPress:', statsData);
          } else {
            // Fallback to mock stats if API endpoint doesn't exist
            console.log('⚠️ Stats API not available, using mock data');
            setStats(mockStats);
          }

          // Check if user is logged in
          const userResponse = await fetch((window.blackCnoteApiSettings?.apiUrl || window.location.origin + '/wp-json/wp/v2').replace(/\/$/, '') + '/users/me');
          setIsLoggedIn(userResponse.ok);
          console.log('✅ User login status checked:', userResponse.ok);

        } catch (apiError) {
          console.warn('⚠️ API calls failed, falling back to mock data:', apiError);
          // Fallback to mock data if API calls fail
          setStats(mockStats);
          setIsLoggedIn(false);
        }

      } catch (err) {
        setError(err instanceof Error ? err.message : 'Failed to load data');
      } finally {
        setLoading(false);
      }
    };
    fetchData();
    if (liveEditingEnabled) {
      interval = setInterval(fetchData, 5000);
    }
    // Add timeout for loading
    timeout = setTimeout(() => {
      if (loading) {
        setError('The server is taking too long to respond. Showing demo data.');
        setStats(mockStats);
        setLoading(false);
      }
    }, 8000);
    return () => {
      if (interval) clearInterval(interval);
      if (timeout) clearTimeout(timeout);
    };
  }, [liveEditingEnabled]);

  // Fetch live editing setting from WordPress
  useEffect(() => {
    fetchSettings().then(settings => {
      setLiveEditingEnabled(settings.live_editing_enabled);
    }).catch(() => setLiveEditingEnabled(true));
  }, []);

  const features: Feature[] = [
    {
      icon: Shield,
      title: 'Secure & Reliable',
      description: 'Your investments are protected with industry-leading security measures and transparent operations.'
    },
    {
      icon: TrendingUp,
      title: 'High Returns',
      description: 'Earn competitive daily returns on your investments with our proven investment strategies.'
    },
    {
      icon: Clock,
      title: '24/7 Support',
      description: 'Our dedicated support team is available around the clock to assist you with any questions.'
    },
    {
      icon: Zap,
      title: 'Instant Activation',
      description: 'Start earning immediately with instant investment activation and real-time profit tracking.'
    },
    {
      icon: Globe,
      title: 'Global Access',
      description: 'Access your investments from anywhere in the world with our secure online platform.'
    },
    {
      icon: Users,
      title: 'Community',
      description: 'Join thousands of successful investors in our growing community.'
    }
  ];

  const handleGetStarted = () => {
    if (isLoggedIn) {
      window.location.href = '/investment-plans';
    } else {
      window.location.href = '/wp-login.php';
    }
  };

  const handleViewPlans = () => {
    window.location.href = '/investment-plans';
  };

  const handleDashboard = () => {
    if (isLoggedIn) {
      window.location.href = '/dashboard';
    } else {
      window.location.href = '/wp-login.php';
    }
  };

  if (loading) {
    return (
      <div className="min-h-screen bg-gray-50 flex items-center justify-center">
        <div className="text-center">
          <RefreshCw className="h-8 w-8 text-yellow-500 animate-spin mx-auto mb-4" />
          <p className="text-gray-600">Loading...</p>
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
            onClick={() => window.location.reload()}
            className="bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600"
          >
            Try Again
          </button>
        </div>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-gray-50">
      {/* Hero Section */}
      <section className="bg-gradient-to-br from-yellow-500 to-yellow-600 text-white py-20">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center">
            <h1 className="text-5xl md:text-6xl font-bold mb-6">
              {wpHomepage ? wpHomepage.title : 'Welcome to BlackCnote'}
            </h1>
            <div className="text-xl md:text-2xl mb-8 max-w-3xl mx-auto">
              {wpHomepage ? (
                <span dangerouslySetInnerHTML={{ __html: wpHomepage.content }} />
              ) : (
                'The most trusted investment platform for earning daily returns.'
              )}
            </div>
            <div className="flex flex-col sm:flex-row gap-4 justify-center">
              <button
                onClick={handleGetStarted}
                className="bg-white text-yellow-600 px-8 py-4 rounded-lg font-semibold text-lg hover:bg-gray-100 transition-colors duration-200 flex items-center justify-center space-x-2"
              >
                <span>{isLoggedIn ? 'View Plans' : 'Get Started'}</span>
                <ArrowRight className="h-5 w-5" />
              </button>
              {isLoggedIn && (
                <button
                  onClick={handleDashboard}
                  className="border-2 border-white text-white px-8 py-4 rounded-lg font-semibold text-lg hover:bg-white hover:text-yellow-600 transition-colors duration-200"
                >
                  Go to Dashboard
                </button>
              )}
            </div>
          </div>
        </div>
      </section>

      {/* Development Welcome Banner */}
      <section className="py-8 bg-gray-50">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <WelcomeBanner 
            title="Development Environment Active!"
            subtitle="Your React components are hot-reloading at http://localhost:5174"
            showAnimation={true}
          />
        </div>
      </section>

      {/* Stats Section */}
      <section className="py-16 bg-white">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="grid grid-cols-2 md:grid-cols-4 gap-8">
            <div className="text-center">
              <div className="text-3xl md:text-4xl font-bold text-gray-900 mb-2">
                {stats.totalUsers.toLocaleString()}+
              </div>
              <div className="text-gray-600">Active Users</div>
            </div>
            <div className="text-center">
              <div className="text-3xl md:text-4xl font-bold text-gray-900 mb-2">
                ${stats.totalInvested.toLocaleString()}
              </div>
              <div className="text-gray-600">Total Invested</div>
            </div>
            <div className="text-center">
              <div className="text-3xl md:text-4xl font-bold text-gray-900 mb-2">
                ${stats.totalPaid.toLocaleString()}
              </div>
              <div className="text-gray-600">Total Paid Out</div>
            </div>
            <div className="text-center">
              <div className="text-3xl md:text-4xl font-bold text-gray-900 mb-2">
                {stats.activeInvestments.toLocaleString()}
              </div>
              <div className="text-gray-600">Active Investments</div>
            </div>
          </div>
        </div>
      </section>

      {/* Features Section */}
      <section className="py-20 bg-gray-50">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center mb-16">
            <h2 className="text-4xl font-bold text-gray-900 mb-4">
              Why Choose BlackCnote?
            </h2>
            <p className="text-xl text-gray-600 max-w-3xl mx-auto">
              We provide the tools and support you need to achieve your financial goals 
              with confidence and security.
            </p>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            {features.map((feature, index) => (
              <div key={index} className="bg-white rounded-2xl p-8 shadow-lg hover:shadow-xl transition-shadow duration-300">
                <div className="bg-yellow-100 w-16 h-16 rounded-full flex items-center justify-center mb-6">
                  <feature.icon className="h-8 w-8 text-yellow-600" />
                </div>
                <h3 className="text-xl font-semibold text-gray-900 mb-4">{feature.title}</h3>
                <p className="text-gray-600">{feature.description}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Investment Plans Preview */}
      <section className="py-20 bg-white">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center mb-16">
            <h2 className="text-4xl font-bold text-gray-900 mb-4">
              Investment Plans
            </h2>
            <p className="text-xl text-gray-600 max-w-3xl mx-auto">
              Choose from our carefully crafted investment plans designed to maximize your returns 
              while maintaining security and transparency.
            </p>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
            {/* Basic Plan */}
            <div className="bg-gradient-to-br from-gray-50 to-gray-100 rounded-2xl p-8 text-center">
              <h3 className="text-2xl font-bold text-gray-900 mb-4">Basic Plan</h3>
              <div className="text-4xl font-bold text-yellow-600 mb-2">1.5%</div>
              <div className="text-gray-600 mb-6">Daily Return</div>
              <ul className="text-left space-y-3 mb-8">
                <li className="flex items-center">
                  <CheckCircle className="h-5 w-5 text-green-500 mr-3" />
                  <span>30 Days Duration</span>
                </li>
                <li className="flex items-center">
                  <CheckCircle className="h-5 w-5 text-green-500 mr-3" />
                  <span>Min: $100</span>
                </li>
                <li className="flex items-center">
                  <CheckCircle className="h-5 w-5 text-green-500 mr-3" />
                  <span>Max: $10,000</span>
                </li>
              </ul>
              <button
                onClick={handleViewPlans}
                className="w-full bg-yellow-500 text-white py-3 px-6 rounded-lg font-semibold hover:bg-yellow-600 transition-colors duration-200"
              >
                Learn More
              </button>
            </div>

            {/* Growth Plan */}
            <div className="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-2xl p-8 text-center text-white relative">
              <div className="absolute -top-4 left-1/2 transform -translate-x-1/2">
                <div className="bg-yellow-400 text-yellow-900 px-4 py-1 rounded-full text-sm font-semibold">
                  Most Popular
                </div>
              </div>
              <h3 className="text-2xl font-bold mb-4">Growth Plan</h3>
              <div className="text-4xl font-bold mb-2">2.0%</div>
              <div className="text-yellow-100 mb-6">Daily Return</div>
              <ul className="text-left space-y-3 mb-8">
                <li className="flex items-center">
                  <CheckCircle className="h-5 w-5 text-yellow-200 mr-3" />
                  <span>45 Days Duration</span>
                </li>
                <li className="flex items-center">
                  <CheckCircle className="h-5 w-5 text-yellow-200 mr-3" />
                  <span>Min: $500</span>
                </li>
                <li className="flex items-center">
                  <CheckCircle className="h-5 w-5 text-yellow-200 mr-3" />
                  <span>Max: $50,000</span>
                </li>
              </ul>
              <button
                onClick={handleViewPlans}
                className="w-full bg-white text-yellow-600 py-3 px-6 rounded-lg font-semibold hover:bg-gray-100 transition-colors duration-200"
              >
                Learn More
              </button>
            </div>

            {/* Premium Plan */}
            <div className="bg-gradient-to-br from-gray-50 to-gray-100 rounded-2xl p-8 text-center">
              <h3 className="text-2xl font-bold text-gray-900 mb-4">Premium Plan</h3>
              <div className="text-4xl font-bold text-yellow-600 mb-2">2.5%</div>
              <div className="text-gray-600 mb-6">Daily Return</div>
              <ul className="text-left space-y-3 mb-8">
                <li className="flex items-center">
                  <CheckCircle className="h-5 w-5 text-green-500 mr-3" />
                  <span>60 Days Duration</span>
                </li>
                <li className="flex items-center">
                  <CheckCircle className="h-5 w-5 text-green-500 mr-3" />
                  <span>Min: $1,000</span>
                </li>
                <li className="flex items-center">
                  <CheckCircle className="h-5 w-5 text-green-500 mr-3" />
                  <span>Max: $100,000</span>
                </li>
              </ul>
              <button
                onClick={handleViewPlans}
                className="w-full bg-yellow-500 text-white py-3 px-6 rounded-lg font-semibold hover:bg-yellow-600 transition-colors duration-200"
              >
                Learn More
              </button>
            </div>
          </div>

          <div className="text-center">
            <button
              onClick={handleViewPlans}
              className="bg-gradient-to-r from-yellow-500 to-yellow-600 text-white px-8 py-4 rounded-lg font-semibold text-lg hover:from-yellow-600 hover:to-yellow-700 transition-all duration-200 flex items-center space-x-2 mx-auto"
            >
              <span>View All Plans</span>
              <ArrowRight className="h-5 w-5" />
            </button>
          </div>
        </div>
      </section>

      {/* Testimonials Section */}
      <section className="py-20 bg-gray-50">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center mb-16">
            <h2 className="text-4xl font-bold text-gray-900 mb-4">
              What Our Users Say
            </h2>
            <p className="text-xl text-gray-600 max-w-3xl mx-auto">
              Join thousands of satisfied investors who have achieved their financial goals with BlackCnote.
            </p>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div className="bg-white rounded-2xl p-8 shadow-lg">
              <div className="flex items-center mb-4">
                {[...Array(5)].map((_, i) => (
                  <Star key={i} className="h-5 w-5 text-yellow-400 fill-current" />
                ))}
              </div>
              <p className="text-gray-600 mb-4">
                "BlackCnote has transformed my investment strategy. The daily returns are consistent and the platform is incredibly user-friendly."
              </p>
              <div className="flex items-center">
                <div className="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center mr-4">
                  <span className="text-yellow-600 font-semibold">JS</span>
                </div>
                <div>
                  <div className="font-semibold text-gray-900">John Smith</div>
                  <div className="text-gray-600">Investor</div>
                </div>
              </div>
            </div>

            <div className="bg-white rounded-2xl p-8 shadow-lg">
              <div className="flex items-center mb-4">
                {[...Array(5)].map((_, i) => (
                  <Star key={i} className="h-5 w-5 text-yellow-400 fill-current" />
                ))}
              </div>
              <p className="text-gray-600 mb-4">
                "The security and transparency of BlackCnote give me peace of mind. I've been investing for 6 months and the results are amazing."
              </p>
              <div className="flex items-center">
                <div className="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center mr-4">
                  <span className="text-yellow-600 font-semibold">MJ</span>
                </div>
                <div>
                  <div className="font-semibold text-gray-900">Maria Johnson</div>
                  <div className="text-gray-600">Business Owner</div>
                </div>
              </div>
            </div>

            <div className="bg-white rounded-2xl p-8 shadow-lg">
              <div className="flex items-center mb-4">
                {[...Array(5)].map((_, i) => (
                  <Star key={i} className="h-5 w-5 text-yellow-400 fill-current" />
                ))}
              </div>
              <p className="text-gray-600 mb-4">
                "Excellent customer support and reliable returns. BlackCnote has exceeded my expectations in every way."
              </p>
              <div className="flex items-center">
                <div className="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center mr-4">
                  <span className="text-yellow-600 font-semibold">DW</span>
                </div>
                <div>
                  <div className="font-semibold text-gray-900">David Wilson</div>
                  <div className="text-gray-600">Retired</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* CTA Section */}
      <section className="py-20 bg-gradient-to-br from-yellow-500 to-yellow-600 text-white">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
          <h2 className="text-4xl font-bold mb-4">
            Ready to Start Your Investment Journey?
          </h2>
          <p className="text-xl mb-8 max-w-3xl mx-auto">
            Join thousands of successful investors and start earning daily returns today. 
            Your financial future starts here.
          </p>
          <div className="flex flex-col sm:flex-row gap-4 justify-center">
            <button
              onClick={handleGetStarted}
              className="bg-white text-yellow-600 px-8 py-4 rounded-lg font-semibold text-lg hover:bg-gray-100 transition-colors duration-200 flex items-center justify-center space-x-2"
            >
              <span>{isLoggedIn ? 'View Plans' : 'Get Started Now'}</span>
              <ArrowRight className="h-5 w-5" />
            </button>
            {isLoggedIn && (
              <button
                onClick={handleDashboard}
                className="border-2 border-white text-white px-8 py-4 rounded-lg font-semibold text-lg hover:bg-white hover:text-yellow-600 transition-colors duration-200"
              >
                Go to Dashboard
              </button>
            )}
          </div>
        </div>
      </section>
    </div>
  );
};

export default HomePage; 
