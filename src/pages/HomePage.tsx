import React from 'react';
import { Link } from 'react-router-dom';
import { TrendingUp, Shield, Users, Calculator, ArrowRight, DollarSign, Target, Award } from 'lucide-react';

const HomePage = () => {
  const features = [
    {
      icon: TrendingUp,
      title: 'High-Yield Returns',
      description: 'Earn competitive returns on your investments with our carefully selected HYIP programs.',
    },
    {
      icon: Shield,
      title: 'Secure Platform',
      description: 'Your investments are protected with bank-level security and transparent operations.',
    },
    {
      icon: Users,
      title: 'Community Focus',
      description: 'Invest in Black-owned businesses and projects that strengthen our community.',
    },
    {
      icon: Calculator,
      title: 'Profit Calculator',
      description: 'Calculate your potential returns before investing with our advanced profit calculator.',
    },
  ];

  const stats = [
    { label: 'Total Invested', value: '$2.5M+', icon: DollarSign },
    { label: 'Active Investors', value: '1,200+', icon: Users },
    { label: 'Success Rate', value: '98.5%', icon: Target },
    { label: 'Years Experience', value: '5+', icon: Award },
  ];

  const investmentPlans = [
    {
      name: 'Starter Plan',
      minInvestment: '$100',
      maxInvestment: '$999',
      dailyReturn: '1.5%',
      duration: '30 days',
      totalReturn: '145%',
      popular: false,
    },
    {
      name: 'Growth Plan',
      minInvestment: '$1,000',
      maxInvestment: '$4,999',
      dailyReturn: '2.0%',
      duration: '45 days',
      totalReturn: '190%',
      popular: true,
    },
    {
      name: 'Premium Plan',
      minInvestment: '$5,000',
      maxInvestment: '$19,999',
      dailyReturn: '2.5%',
      duration: '60 days',
      totalReturn: '250%',
      popular: false,
    },
    {
      name: 'Elite Plan',
      minInvestment: '$20,000',
      maxInvestment: '$100,000',
      dailyReturn: '3.0%',
      duration: '90 days',
      totalReturn: '370%',
      popular: false,
    },
  ];

  return (
    <div className="space-y-16">
      {/* Hero Section */}
      <section className="relative bg-gradient-to-br from-gray-900 via-gray-800 to-black text-white overflow-hidden">
        <div className="absolute inset-0 bg-gradient-to-r from-yellow-500/10 to-transparent"></div>
        <div className="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
          <div className="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div className="space-y-8">
              <h1 className="text-5xl lg:text-6xl font-bold leading-tight">
                Empowering <span className="text-yellow-500">Black Wealth</span> Through Strategic Investment
              </h1>
              <p className="text-xl text-gray-300 leading-relaxed">
                Join BlackCnote's mission to flip the Black-White wealth gap by 2040. 
                Invest in high-yield programs that circulate wealth within our community.
              </p>
              <div className="flex flex-col sm:flex-row gap-4">
                <Link
                  to="/investment-plans"
                  className="bg-gradient-to-r from-yellow-500 to-yellow-600 text-black px-8 py-4 rounded-lg font-semibold hover:from-yellow-600 hover:to-yellow-700 transition-all duration-200 shadow-lg hover:shadow-xl flex items-center justify-center space-x-2"
                >
                  <span>Start Investing</span>
                  <ArrowRight className="h-5 w-5" />
                </Link>
                <Link
                  to="/calculator"
                  className="border-2 border-yellow-500 text-yellow-500 px-8 py-4 rounded-lg font-semibold hover:bg-yellow-500 hover:text-black transition-all duration-200 flex items-center justify-center space-x-2"
                >
                  <Calculator className="h-5 w-5" />
                  <span>Calculate Returns</span>
                </Link>
              </div>
            </div>
            <div className="relative">
              <div className="bg-gradient-to-br from-yellow-500/20 to-transparent rounded-3xl p-8 backdrop-blur-sm border border-yellow-500/30">
                <img 
                  src="https://images.pexels.com/photos/3760067/pexels-photo-3760067.jpeg?auto=compress&cs=tinysrgb&w=600" 
                  alt="Investment Growth" 
                  className="w-full h-80 object-cover rounded-2xl"
                />
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Stats Section */}
      <section className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="grid grid-cols-2 lg:grid-cols-4 gap-8">
          {stats.map((stat, index) => (
            <div key={index} className="text-center space-y-4">
              <div className="bg-gradient-to-br from-yellow-500 to-yellow-600 w-16 h-16 rounded-full flex items-center justify-center mx-auto">
                <stat.icon className="h-8 w-8 text-white" />
              </div>
              <div>
                <div className="text-3xl font-bold text-gray-900">{stat.value}</div>
                <div className="text-gray-600 font-medium">{stat.label}</div>
              </div>
            </div>
          ))}
        </div>
      </section>

      {/* Features Section */}
      <section className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="text-center space-y-4 mb-16">
          <h2 className="text-4xl font-bold text-gray-900">Why Choose BlackCnote?</h2>
          <p className="text-xl text-gray-600 max-w-3xl mx-auto">
            We're committed to building generational wealth within the Black community through 
            secure, transparent, and profitable investment opportunities.
          </p>
        </div>
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
          {features.map((feature, index) => (
            <div key={index} className="bg-white p-8 rounded-2xl shadow-lg hover:shadow-xl transition-shadow duration-300 border border-gray-100">
              <div className="bg-gradient-to-br from-yellow-500 to-yellow-600 w-12 h-12 rounded-lg flex items-center justify-center mb-6">
                <feature.icon className="h-6 w-6 text-white" />
              </div>
              <h3 className="text-xl font-semibold text-gray-900 mb-4">{feature.title}</h3>
              <p className="text-gray-600 leading-relaxed">{feature.description}</p>
            </div>
          ))}
        </div>
      </section>

      {/* Investment Plans Preview */}
      <section className="bg-gray-100 py-16">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center space-y-4 mb-16">
            <h2 className="text-4xl font-bold text-gray-900">Investment Plans</h2>
            <p className="text-xl text-gray-600 max-w-3xl mx-auto">
              Choose from our carefully designed investment plans that offer competitive returns 
              while supporting Black-owned businesses and community projects.
            </p>
          </div>
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            {investmentPlans.map((plan, index) => (
              <div key={index} className={`bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 overflow-hidden ${plan.popular ? 'ring-2 ring-yellow-500 transform scale-105' : ''}`}>
                {plan.popular && (
                  <div className="bg-gradient-to-r from-yellow-500 to-yellow-600 text-white text-center py-2 font-semibold">
                    Most Popular
                  </div>
                )}
                <div className="p-8 space-y-6">
                  <div className="text-center">
                    <h3 className="text-2xl font-bold text-gray-900">{plan.name}</h3>
                    <div className="text-3xl font-bold text-yellow-600 mt-2">{plan.dailyReturn}</div>
                    <div className="text-gray-600">Daily Return</div>
                  </div>
                  <div className="space-y-3">
                    <div className="flex justify-between">
                      <span className="text-gray-600">Min Investment:</span>
                      <span className="font-semibold">{plan.minInvestment}</span>
                    </div>
                    <div className="flex justify-between">
                      <span className="text-gray-600">Max Investment:</span>
                      <span className="font-semibold">{plan.maxInvestment}</span>
                    </div>
                    <div className="flex justify-between">
                      <span className="text-gray-600">Duration:</span>
                      <span className="font-semibold">{plan.duration}</span>
                    </div>
                    <div className="flex justify-between">
                      <span className="text-gray-600">Total Return:</span>
                      <span className="font-semibold text-green-600">{plan.totalReturn}</span>
                    </div>
                  </div>
                  <button className={`w-full py-3 rounded-lg font-semibold transition-all duration-200 ${
                    plan.popular 
                      ? 'bg-gradient-to-r from-yellow-500 to-yellow-600 text-white hover:from-yellow-600 hover:to-yellow-700' 
                      : 'border-2 border-yellow-500 text-yellow-600 hover:bg-yellow-500 hover:text-white'
                  }`}>
                    Invest Now
                  </button>
                </div>
              </div>
            ))}
          </div>
          <div className="text-center mt-12">
            <Link
              to="/investment-plans"
              className="inline-flex items-center space-x-2 text-yellow-600 hover:text-yellow-700 font-semibold text-lg"
            >
              <span>View All Plans</span>
              <ArrowRight className="h-5 w-5" />
            </Link>
          </div>
        </div>
      </section>

      {/* CTA Section */}
      <section className="bg-gradient-to-r from-gray-900 to-black text-white py-16">
        <div className="max-w-4xl mx-auto text-center px-4 sm:px-6 lg:px-8 space-y-8">
          <h2 className="text-4xl font-bold">Ready to Build Generational Wealth?</h2>
          <p className="text-xl text-gray-300">
            Join thousands of investors who are already building wealth within the Black community. 
            Start your investment journey today.
          </p>
          <div className="flex flex-col sm:flex-row gap-4 justify-center">
            <button className="bg-gradient-to-r from-yellow-500 to-yellow-600 text-black px-8 py-4 rounded-lg font-semibold hover:from-yellow-600 hover:to-yellow-700 transition-all duration-200 shadow-lg hover:shadow-xl">
              Create Account
            </button>
            <Link
              to="/calculator"
              className="border-2 border-yellow-500 text-yellow-500 px-8 py-4 rounded-lg font-semibold hover:bg-yellow-500 hover:text-black transition-all duration-200 flex items-center justify-center space-x-2"
            >
              <Calculator className="h-5 w-5" />
              <span>Try Calculator</span>
            </Link>
          </div>
        </div>
      </section>
    </div>
  );
};

export default HomePage;